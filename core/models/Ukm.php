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
            "INSERT INTO ukm (nama, singkatan, kategori, slogan, deskripsi, logo_path, header_path, lokasi, koordinat, tanggal_berdiri) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
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
            $data['koordinat']       ?? null,
            $data['tanggal_berdiri'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update UKM */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE ukm SET nama = ?, singkatan = ?, kategori = ?, slogan = ?, deskripsi = ?,
                            logo_path = ?, header_path = ?, lokasi = ?, koordinat = ?, tanggal_berdiri = ?
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
            $data['koordinat']       ?? null,
            $data['tanggal_berdiri'] ?? null,
            $id,
        ]);
    }

    /** Hapus UKM (Hard Delete - Hapus Permanen beserta file) */
    public function delete(int $id): bool
    {
        // 1. Ambil data UKM untuk hapus logo & header
        $ukm = $this->getById($id);
        if ($ukm) {
            if (!empty($ukm['logo_path'])) FileUpload::delete($ukm['logo_path']);
            if (!empty($ukm['header_path'])) FileUpload::delete($ukm['header_path']);
        }

        // 2. Hapus foto-foto anggota UKM
        $stmtAnggota = $this->db->prepare("SELECT foto_path FROM anggota WHERE ukm_id = ?");
        $stmtAnggota->execute([$id]);
        foreach ($stmtAnggota->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!empty($row['foto_path'])) FileUpload::delete($row['foto_path']);
        }

        // 3. Hapus gambar-gambar berita UKM
        $stmtBerita = $this->db->prepare("SELECT gambar_path FROM berita WHERE ukm_id = ?");
        $stmtBerita->execute([$id]);
        foreach ($stmtBerita->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!empty($row['gambar_path'])) FileUpload::delete($row['gambar_path']);
        }

        // 4. Hapus foto-foto admin UKM
        $stmtAdmin = $this->db->prepare("SELECT foto_path FROM admins WHERE ukm_id = ?");
        $stmtAdmin->execute([$id]);
        foreach ($stmtAdmin->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!empty($row['foto_path'])) FileUpload::delete($row['foto_path']);
        }

        // 5. Akhirnya, hapus record UKM (Trigger CASCADE di DB akan hapus sisa data teks)
        $stmt = $this->db->prepare("DELETE FROM ukm WHERE id = ?");
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
