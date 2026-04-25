<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Berita
 * CRUD untuk entitas Berita/Artikel
 */
class Berita
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua berita (opsional filter per UKM dan Periode) */
    public function getAll(?int $ukmId = null, ?int $periodeId = null): array
    {
        $query = "SELECT b.*, u.singkatan AS ukm_nama 
                 FROM berita b
                 LEFT JOIN ukm u ON b.ukm_id = u.id";
        $params = [];
        $conditions = [];

        if ($ukmId) {
            $conditions[] = "b.ukm_id = ?";
            $params[] = $ukmId;
        }
        if ($periodeId) {
            $conditions[] = "b.periode_id = ?";
            $params[] = $periodeId;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $query .= " ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Ambil berita by ID */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Ambil berita published saja (untuk halaman publik) */
    public function getPublished(?int $ukmId = null, ?int $periodeId = null, int $limit = 10): array
    {
        $query = "SELECT * FROM berita WHERE status = 'published'";
        $params = [];

        if ($ukmId) {
            $query .= " AND ukm_id = ?";
            $params[] = $ukmId;
        }
        if ($periodeId) {
            $query .= " AND periode_id = ?";
            $params[] = $periodeId;
        }

        $query .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($query);
        // Bind limit explicitly or just use pass-in-execute if PDO handles it (sometimes limit fails with pass-in-execute)
        // Safer with passing if emulated prepares are on, which is default true in typical setups.
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Tambah berita baru */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO berita (ukm_id, periode_id, judul, konten, kategori, gambar_path, penulis, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['ukm_id'],
            $data['periode_id'] ?? 0,
            $data['judul'],
            $data['konten'],
            $data['kategori'] ?? null,
            $data['gambar_path'] ?? null,
            $data['penulis'] ?? null,
            $data['status'] ?? 'draft',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update berita */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE berita 
             SET ukm_id = ?, periode_id = ?, judul = ?, konten = ?, kategori = ?, gambar_path = ?, penulis = ?, status = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['ukm_id'],
            $data['periode_id'] ?? 0,
            $data['judul'],
            $data['konten'],
            $data['kategori'] ?? null,
            $data['gambar_path'] ?? null,
            $data['penulis'] ?? null,
            $data['status'] ?? 'draft',
            $id,
        ]);
    }

    /** Hapus berita beserta gambar */
    public function delete(int $id): bool
    {
        $berita = $this->getById($id);
        if ($berita && !empty($berita['gambar_path'])) {
            FileUpload::delete($berita['gambar_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM berita WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Hitung total berita */
    public function count(?int $ukmId = null, ?int $periodeId = null): int
    {
        $query = "SELECT COUNT(*) FROM berita";
        $params = [];
        $conditions = [];

        if ($ukmId) {
            $conditions[] = "ukm_id = ?";
            $params[] = $ukmId;
        }
        if ($periodeId) {
            $conditions[] = "periode_id = ?";
            $params[] = $periodeId;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
