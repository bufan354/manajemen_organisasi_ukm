<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: JabatanKustom
 * CRUD untuk jabatan kustom per UKM.
 * Level: 1=Pembina, 2=Ketua, 3=BPH (Sekret/Bend/Wakil), 4=Koordinator/Divisi, 5=Anggota biasa
 */
class JabatanKustom
{
    private PDO $db;

    /** Jabatan standar yang selalu tersedia tanpa perlu ada di DB */
    public const JABATAN_STANDAR = [
        ['hierarki' => 'Ketua',      'label' => 'Ketua Umum',    'level' => 2],
        ['hierarki' => 'Wakil',      'label' => 'Wakil Ketua',   'level' => 3],
        ['hierarki' => 'Sekretaris', 'label' => 'Sekretaris',    'level' => 3],
        ['hierarki' => 'Bendahara',  'label' => 'Bendahara',     'level' => 3],
        ['hierarki' => 'Anggota',    'label' => 'Anggota',       'level' => 5],
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua jabatan kustom milik sebuah UKM */
    public function getByUkm(int $ukmId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM jabatan_kustom WHERE ukm_id = ? ORDER BY level ASC, nama_jabatan ASC"
        );
        $stmt->execute([$ukmId]);
        return $stmt->fetchAll();
    }

    /** Ambil jabatan kustom by ID */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM jabatan_kustom WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Tambah jabatan kustom baru */
    public function create(int $ukmId, string $namaJabatan, int $level = 4): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO jabatan_kustom (ukm_id, nama_jabatan, level) VALUES (?, ?, ?)"
        );
        $stmt->execute([$ukmId, $namaJabatan, $level]);
        return (int)$this->db->lastInsertId();
    }

    /** Update jabatan kustom */
    public function update(int $id, string $namaJabatan, int $level): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE jabatan_kustom SET nama_jabatan = ?, level = ? WHERE id = ?"
        );
        return $stmt->execute([$namaJabatan, $level, $id]);
    }

    /** Hapus jabatan kustom */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM jabatan_kustom WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Cek apakah jabatan kustom ini masih digunakan oleh anggota.
     * Pengecekan berdasarkan nama jabatan (hierarki) karena kita menyimpan teks.
     */
    public function isUsedByAnggota(int $id): bool
    {
        $jabatan = $this->getById($id);
        if (!$jabatan) return false;

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM anggota WHERE ukm_id = ? AND hierarki = ?"
        );
        $stmt->execute([$jabatan['ukm_id'], $jabatan['nama_jabatan']]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Cek apakah nama jabatan sudah ada di UKM ini (untuk validasi duplikat)
     * Bisa exclude ID tertentu saat update.
     */
    public function nameExists(int $ukmId, string $nama, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM jabatan_kustom WHERE ukm_id = ? AND nama_jabatan = ?";
        $params = [$ukmId, $nama];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
