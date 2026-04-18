<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Kehadiran (tabel absensi)
 * Pencatatan dan query kehadiran anggota per event
 */
class Kehadiran
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Catat kehadiran anggota di event
     * INSERT ON DUPLICATE KEY UPDATE → mencegah double absen
     * @return string 'recorded' (baru), 'already' (sudah absen), 'error'
     */
    public function recordAttendance(int $eventId, int $anggotaId, string $metode = 'fingerprint'): string
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO absensi (event_id, anggota_id, waktu_hadir, metode)
                 VALUES (?, ?, NOW(), ?)
                 ON DUPLICATE KEY UPDATE waktu_hadir = waktu_hadir"
            );
            $stmt->execute([$eventId, $anggotaId, $metode]);
            
            // rowCount = 1 → inserted (baru), 0 → duplicate (sudah ada, tidak diubah)
            return $stmt->rowCount() > 0 ? 'recorded' : 'already';
        } catch (PDOException $e) {
            error_log("Kehadiran Error: " . $e->getMessage());
            return 'error';
        }
    }

    /**
     * Ambil daftar kehadiran per event (dengan data anggota)
     */
    public function getByEvent(int $eventId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.event_id, a.anggota_id, a.waktu_hadir, a.metode, 
                    ang.nama, ang.nim, ang.jabatan, ang.foto_path,
                    ang.ukm_id
             FROM absensi a
             JOIN anggota ang ON a.anggota_id = ang.id
             WHERE a.event_id = ?
             ORDER BY a.waktu_hadir ASC"
        );
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil riwayat kehadiran per anggota
     */
    public function getByAnggota(int $anggotaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.event_id, a.anggota_id, a.waktu_hadir, a.metode, e.nama AS event_nama, e.waktu_mulai, e.waktu_selesai, e.lokasi
             FROM absensi a
             JOIN events e ON a.event_id = e.id
             WHERE a.anggota_id = ?
             ORDER BY a.waktu_hadir DESC"
        );
        $stmt->execute([$anggotaId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua kehadiran per UKM (opsional filter tanggal)
     */
    public function getByUkm(int $ukmId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = "SELECT a.id, a.event_id, a.anggota_id, a.waktu_hadir, a.metode, ang.nama, ang.nim, e.nama AS event_nama, e.waktu_mulai
                  FROM absensi a
                  JOIN anggota ang ON a.anggota_id = ang.id
                  JOIN events e ON a.event_id = e.id
                  WHERE e.ukm_id = ?";
        $params = [$ukmId];

        if ($startDate) {
            $query .= " AND a.waktu_hadir >= ?";
            $params[] = $startDate . ' 00:00:00';
        }
        if ($endDate) {
            $query .= " AND a.waktu_hadir <= ?";
            $params[] = $endDate . ' 23:59:59';
        }

        $query .= " ORDER BY a.waktu_hadir DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Statistik kehadiran per event: jumlah hadir vs total anggota UKM
     */
    public function getStatsByEvent(int $eventId): array
    {
        // Jumlah hadir
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM absensi WHERE event_id = ?");
        $stmt->execute([$eventId]);
        $totalHadir = (int)$stmt->fetchColumn();

        // Total anggota aktif di UKM event tersebut
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM anggota 
             WHERE ukm_id = (SELECT ukm_id FROM events WHERE id = ?) 
             AND status = 'aktif'"
        );
        $stmt->execute([$eventId]);
        $totalAnggota = (int)$stmt->fetchColumn();

        return [
            'total_hadir'   => $totalHadir,
            'total_anggota' => $totalAnggota,
            'persentase'    => $totalAnggota > 0 ? round($totalHadir / $totalAnggota * 100, 1) : 0,
        ];
    }

    /**
     * Ringkasan kehadiran semua event per UKM
     * Return: array of events + stats kehadiran
     */
    public function getStatsByUkm(int $ukmId): array
    {
        $stmt = $this->db->prepare(
            "SELECT e.id, e.nama, e.waktu_mulai, e.waktu_selesai, e.lokasi, e.status_absensi,
                    COUNT(a.id) AS total_hadir
             FROM events e
             LEFT JOIN absensi a ON e.id = a.event_id
             WHERE e.ukm_id = ?
             GROUP BY e.id
             ORDER BY e.waktu_mulai DESC"
        );
        $stmt->execute([$ukmId]);
        $events = $stmt->fetchAll();

        // Tambahkan total anggota aktif UKM
        $stmt2 = $this->db->prepare("SELECT COUNT(*) FROM anggota WHERE ukm_id = ? AND status = 'aktif'");
        $stmt2->execute([$ukmId]);
        $totalAnggota = (int)$stmt2->fetchColumn();

        foreach ($events as &$ev) {
            $ev['total_anggota'] = $totalAnggota;
            $ev['persentase'] = $totalAnggota > 0 ? round($ev['total_hadir'] / $totalAnggota * 100, 1) : 0;
        }

        return $events;
    }

    /**
     * Rata-rata kehadiran overall UKM (semua event)
     */
    public function getAttendanceRate(int $ukmId): float
    {
        $stats = $this->getStatsByUkm($ukmId);
        if (empty($stats)) return 0;

        $totalPersen = 0;
        foreach ($stats as $ev) {
            $totalPersen += $ev['persentase'];
        }
        return round($totalPersen / count($stats), 1);
    }

    /**
     * Total kehadiran hari ini untuk UKM
     */
    public function countTodayByUkm(int $ukmId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM absensi a
             JOIN events e ON a.event_id = e.id
             WHERE e.ukm_id = ? AND DATE(a.waktu_hadir) = CURDATE()"
        );
        $stmt->execute([$ukmId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Total seluruh record kehadiran untuk UKM
     */
    public function countByUkm(int $ukmId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM absensi a
             JOIN events e ON a.event_id = e.id
             WHERE e.ukm_id = ?"
        );
        $stmt->execute([$ukmId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ringkasan kehadiran per periode waktu
     */
    public function getStatsByUkmPeriod(int $ukmId, string $period): array
    {
        $whereSql = "WHERE e.ukm_id = ?";
        $params = [$ukmId];

        if ($period !== 'all') {
            if ($period === 'week') {
                $whereSql .= " AND e.waktu_mulai >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            } elseif ($period === 'month') {
                $whereSql .= " AND e.waktu_mulai >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            } elseif ($period === 'year') {
                $whereSql .= " AND e.waktu_mulai >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            }
        }

        $stmt = $this->db->prepare(
            "SELECT e.id, e.nama, e.waktu_mulai, e.waktu_selesai, e.lokasi, e.status_absensi,
                    COUNT(a.id) AS total_hadir
             FROM events e
             LEFT JOIN absensi a ON e.id = a.event_id
             $whereSql
             GROUP BY e.id
             ORDER BY e.waktu_mulai DESC"
        );
        $stmt->execute($params);
        $events = $stmt->fetchAll();

        $stmt2 = $this->db->prepare("SELECT COUNT(*) FROM anggota WHERE ukm_id = ? AND status = 'aktif'");
        $stmt2->execute([$ukmId]);
        $totalAnggota = (int)$stmt2->fetchColumn();

        foreach ($events as &$ev) {
            $ev['total_anggota'] = $totalAnggota;
            $ev['persentase'] = $totalAnggota > 0 ? round($ev['total_hadir'] / $totalAnggota * 100, 1) : 0;
        }

        return $events;
    }

    /**
     * Rata-rata kehadiran UKM dengan dibatasi kurun waktu tertentu
     */
    public function getAttendanceRateByPeriod(int $ukmId, string $period): float
    {
        $stats = $this->getStatsByUkmPeriod($ukmId, $period);
        if (empty($stats)) return 0;

        $totalPersen = 0;
        foreach ($stats as $ev) {
            $totalPersen += $ev['persentase'];
        }
        return round($totalPersen / count($stats), 1);
    }

    /**
     * Get performance stats for all active UKMs (for Superadmin)
     */
    public function getGlobalUkmStats(): array
    {
        $stmt = $this->db->query(
            "SELECT u.id, u.nama, u.singkatan, 
                    (SELECT COUNT(*) FROM anggota WHERE ukm_id = u.id AND status = 'aktif') as total_anggota,
                    (SELECT COUNT(*) FROM absensi a JOIN events e ON a.event_id = e.id WHERE e.ukm_id = u.id) as total_hadir,
                    (SELECT COUNT(*) FROM events WHERE ukm_id = u.id) as total_event
             FROM ukm u
             WHERE u.status = 'aktif'"
        );
        $ukms = $stmt->fetchAll();
        
        foreach ($ukms as &$u) {
            // Rate is average of (present/target) across all events
            // Simplified here: total_hadir / (total_event * total_anggota)
            $denominator = (int)$u['total_event'] * (int)$u['total_anggota'];
            $u['attendance_rate'] = ($denominator > 0) 
                ? round(((int)$u['total_hadir'] / $denominator) * 100, 1) 
                : 0;
        }

        // Sort by rate descending
        usort($ukms, fn($a, $b) => $b['attendance_rate'] <=> $a['attendance_rate']);
        
        return $ukms;
    }
}
