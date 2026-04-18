<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Event
 * CRUD untuk entitas Event/Kegiatan Absensi
 */
class Event
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua event (opsional filter per UKM) */
    public function getAll(?int $ukmId = null): array
    {
        if ($ukmId) {
            $stmt = $this->db->prepare(
                "SELECT e.*, u.singkatan AS ukm_nama 
                 FROM events e
                 LEFT JOIN ukm u ON e.ukm_id = u.id
                 WHERE e.ukm_id = ?
                 ORDER BY e.id DESC"
            );
            $stmt->execute([$ukmId]);
        } else {
            $stmt = $this->db->query(
                "SELECT e.*, u.singkatan AS ukm_nama 
                 FROM events e
                 LEFT JOIN ukm u ON e.ukm_id = u.id
                 ORDER BY e.id DESC"
            );
        }
        return $stmt->fetchAll();
    }

    /** Ambil event by ID */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Tambah event baru */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO events (ukm_id, is_routine, hari_rutin, parent_id, nama, deskripsi, waktu_mulai, waktu_selesai, lokasi, status_absensi, status, alasan) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['ukm_id'],
            $data['is_routine'] ?? 0,
            $data['hari_rutin'] ?? null,
            $data['parent_id'] ?? null,
            $data['nama'],
            $data['deskripsi'] ?? null,
            $data['waktu_mulai'],
            $data['waktu_selesai'],
            $data['lokasi'] ?? null,
            $data['status_absensi'] ?? 0,
            $data['status'] ?? 'scheduled',
            $data['alasan'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update event */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE events 
             SET ukm_id = ?, is_routine = ?, hari_rutin = ?, nama = ?, deskripsi = ?, waktu_mulai = ?, waktu_selesai = ?, lokasi = ?, status_absensi = ?, status = ?, alasan = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['ukm_id'],
            $data['is_routine'] ?? 0,
            $data['hari_rutin'] ?? null,
            $data['nama'],
            $data['deskripsi'] ?? null,
            $data['waktu_mulai'],
            $data['waktu_selesai'],
            $data['lokasi'] ?? null,
            $data['status_absensi'] ?? 0,
            $data['status'] ?? 'scheduled',
            $data['alasan'] ?? null,
            $id,
        ]);
    }

    /** Hapus event */
    public function delete(int $id): bool
    {
        // Hapus juga anak-anaknya jika ini master (cascade hapus manual)
        $this->db->prepare("DELETE FROM events WHERE parent_id = ?")->execute([$id]);
        
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Update status event secara spesifik */
    public function updateStatus(int $id, string $status, ?string $alasan = null, ?array $newTimes = null, ?int $statusAbsensi = null): bool
    {
        if ($newTimes) {
            $stmt = $this->db->prepare("UPDATE events SET status = ?, alasan = ?, waktu_mulai = ?, waktu_selesai = ?, status_absensi = COALESCE(?, status_absensi) WHERE id = ?");
            return $stmt->execute([$status, $alasan, $newTimes['waktu_mulai'], $newTimes['waktu_selesai'], $statusAbsensi, $id]);
        }
        $stmt = $this->db->prepare("UPDATE events SET status = ?, alasan = ?, status_absensi = COALESCE(?, status_absensi) WHERE id = ?");
        return $stmt->execute([$status, $alasan, $statusAbsensi, $id]);
    }

    /** Ambil event yang sedang aktif (absensi terbuka) */
    public function getActive(?int $ukmId = null): array
    {
        if ($ukmId) {
            $stmt = $this->db->prepare(
                "SELECT * FROM events 
                 WHERE ukm_id = ? AND status_absensi = 1 AND waktu_selesai >= NOW() AND is_routine = 0 AND status = 'scheduled'
                 ORDER BY waktu_mulai ASC"
            );
            $stmt->execute([$ukmId]);
        } else {
            $stmt = $this->db->query(
                "SELECT * FROM events 
                 WHERE status_absensi = 1 AND waktu_selesai >= NOW() AND is_routine = 0 AND status = 'scheduled'
                 ORDER BY waktu_mulai ASC"
            );
        }
        return $stmt->fetchAll();
    }

    /** Hitung total event */
    public function count(?int $ukmId = null): int
    {
        if ($ukmId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM events WHERE ukm_id = ? AND is_routine = 0");
            $stmt->execute([$ukmId]);
            return (int)$stmt->fetchColumn();
        }
        return (int)$this->db->query("SELECT COUNT(*) FROM events WHERE is_routine = 0")->fetchColumn();
    }

    /** Hitung peserta hadir di event */
    public function countAttendees(int $eventId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM absensi WHERE event_id = ?");
        $stmt->execute([$eventId]);
        return (int)$stmt->fetchColumn();
    }

    /** 
     * Ambil event yang sedang berlangsung SAAT INI 
     * (waktu_mulai <= NOW() <= waktu_selesai DAN status_absensi = 1) 
     */
    public function getActiveNow(?int $ukmId = null): array|false
    {
        if ($ukmId) {
            $stmt = $this->db->prepare(
                "SELECT * FROM events 
                 WHERE ukm_id = ? 
                   AND status_absensi = 1 
                   AND is_routine = 0
                   AND waktu_mulai <= NOW() 
                   AND waktu_selesai >= NOW()
                   AND status = 'scheduled'
                 ORDER BY waktu_mulai ASC
                 LIMIT 1"
            );
            $stmt->execute([$ukmId]);
        } else {
            $stmt = $this->db->query(
                "SELECT * FROM events 
                 WHERE status_absensi = 1 
                   AND is_routine = 0
                   AND waktu_mulai <= NOW() 
                   AND waktu_selesai >= NOW()
                   AND status = 'scheduled'
                 ORDER BY waktu_mulai ASC
                 LIMIT 1"
            );
        }
        return $stmt->fetch() ?: false;
    }

    /** 
     * Ambil semua event dengan statistik kehadiran (Pisahkan Master dan Anak/Normal)
     */
    public function getWithAttendanceStats(?int $ukmId = null): array
    {
        $query = "SELECT e.*, u.singkatan AS ukm_nama,
                         COUNT(a.id) AS total_hadir,
                         (SELECT COUNT(*) FROM anggota ang WHERE ang.ukm_id = e.ukm_id AND ang.status = 'aktif') AS total_anggota
                  FROM events e
                  LEFT JOIN ukm u ON e.ukm_id = u.id
                  LEFT JOIN absensi a ON e.id = a.event_id";
        $params = [];

        if ($ukmId) {
            $query .= " WHERE e.ukm_id = ?";
            $params[] = $ukmId;
        }

        $query .= " GROUP BY e.id ORDER BY e.id DESC"; // Urutkan berdasarkan yang paling baru dibuat

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $routines = [];
        $regulars = [];
        
        foreach ($results as $r) {
            if ($r['is_routine']) {
                $routines[] = $r;
            } else {
                $regulars[] = $r;
            }
        }
        
        // Return 2 kunci: 'routines' (master) dan 'regulars' (anak/single)
        return ['routines' => $routines, 'regulars' => $regulars];
    }

    /** JIT Generator: Mengeluarkan Anak-anak Rutin Pada Hari H */
    public function generateRoutineEvents(): void
    {
        $today = date('w'); // 0 (Minggu) sampai 6 (Sabtu)
        $curDate = date('Y-m-d');
        
        $stmt = $this->db->query("SELECT * FROM events WHERE is_routine = 1 AND status_absensi = 1");
        $masters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($masters as $master) {
            $hariList = explode(',', $master['hari_rutin'] ?? '');
            if (in_array((string)$today, $hariList, true)) {
                
                // Cek apakah hari ini sudah ada anak (parent_id = master_id, tgl = curDate)
                $check = $this->db->prepare("SELECT id FROM events WHERE parent_id = ? AND DATE(waktu_mulai) = ?");
                $check->execute([$master['id'], $curDate]);
                
                if (!$check->fetch()) {
                    $jamMulai = date('H:i:s', strtotime($master['waktu_mulai']));
                    $jamSelesai = date('H:i:s', strtotime($master['waktu_selesai']));
                    
                    $namaAnak = $master['nama'] . ' - ' . date('d M Y');
                    
                    $insert = $this->db->prepare(
                        "INSERT INTO events (ukm_id, is_routine, parent_id, nama, deskripsi, waktu_mulai, waktu_selesai, lokasi, status_absensi) 
                         VALUES (?, 0, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $insert->execute([
                        $master['ukm_id'],
                        $master['id'],
                        $namaAnak,
                        $master['deskripsi'],
                        $curDate . ' ' . $jamMulai,
                        $curDate . ' ' . $jamSelesai,
                        $master['lokasi'],
                        1 // Buka absen otomatis karena masternya 1
                    ]);
                }
            }
        }
    }
}
