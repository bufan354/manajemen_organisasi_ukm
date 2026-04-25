<?php
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../FileUpload.php';

/**
 * Model: Ukm
 * CRUD untuk entitas UKM / Organisasi
 */
class Ukm
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua UKM (kecuali soft deleted) */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM ukm WHERE deleted_at IS NULL ORDER BY nama ASC");
        return $stmt->fetchAll();
    }

    /** Ambil UKM yang AKTRF (untuk halaman publik) */
    public function getActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM ukm WHERE deleted_at IS NULL AND status = 'aktif' ORDER BY nama ASC");
        return $stmt->fetchAll();
    }

    /** Ambil UKM berdasarkan ID (termasuk yang nonaktif, agar tabel child bisa nampil, tapi kecualikan deleted_at jika mau) */
    public function getById(int $id): array|false
    {
        // Admin tetep bisa liat yang nonaktif, yang dihapus mungkin bisa disembunyikan
        $stmt = $this->db->prepare("SELECT * FROM ukm WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Ambil UKM berdasarkan slug nama (untuk URL publik) */
    public function getBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM ukm WHERE LOWER(REPLACE(nama, ' ', '-')) = ? AND deleted_at IS NULL");
        $stmt->execute([strtolower($slug)]);
        return $stmt->fetch();
    }

    /** Tambah UKM baru */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ukm (nama, singkatan, kategori, slogan, deskripsi, logo_path, header_path, lokasi, tanggal_berdiri) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nama'],
            $data['singkatan']       ?? null,
            $data['kategori']        ?? null,
            $data['slogan']          ?? null,
            $data['deskripsi']       ?? null,
            $data['logo_path']       ?? null,
            $data['header_path']     ?? null,
            $data['lokasi']          ?? null,
            $data['tanggal_berdiri'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update UKM */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE ukm SET nama = ?, singkatan = ?, kategori = ?, slogan = ?, deskripsi = ?,
                            logo_path = ?, header_path = ?, lokasi = ?, tanggal_berdiri = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['nama'],
            $data['singkatan']       ?? null,
            $data['kategori']        ?? null,
            $data['slogan']          ?? null,
            $data['deskripsi']       ?? null,
            $data['logo_path']       ?? null,
            $data['header_path']     ?? null,
            $data['lokasi']          ?? null,
            $data['tanggal_berdiri'] ?? null,
            $id,
        ]);
    }

    /** Hapus UKM (Soft Delete) */
    public function delete(int $id): bool
    {
        // Fitur Soft Delete tidak menghapus data foto dan record terkait dari disk/basis data.
        $stmt = $this->db->prepare("UPDATE ukm SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Toggle status (Aktif / Nonaktif) */
    public function toggleStatus(int $id): bool
    {
        $ukm = $this->getById($id);
        if (!$ukm) return false;
        
        $newStatus = ($ukm['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        $stmt = $this->db->prepare("UPDATE ukm SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }

    /** Hitung total UKM */
    public function count(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM ukm WHERE deleted_at IS NULL")->fetchColumn();
    }
}
