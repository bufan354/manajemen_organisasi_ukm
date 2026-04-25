<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Anggota
 * CRUD untuk entitas Anggota UKM
 */
class Anggota
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua anggota (opsional filter per UKM dan Periode) */
    public function getAll(?int $ukmId = null, ?int $periodeId = null): array
    {
        $query = "SELECT a.*, u.singkatan AS ukm_nama 
                 FROM anggota a
                 LEFT JOIN ukm u ON a.ukm_id = u.id";
        $params = [];
        $conditions = [];

        if ($ukmId) {
            $conditions[] = "a.ukm_id = ?";
            $params[] = $ukmId;
        }
        if ($periodeId) {
            $conditions[] = "a.periode_id = ?";
            $params[] = $periodeId;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $query .= " ORDER BY a.nama ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Ambil anggota by ID */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM anggota WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Ambil anggota by NIM (mungkin berguna untuk CSV import dll) */
    public function getByNim(string $nim, ?int $ukmId = null): array|false
    {
        if ($ukmId !== null) {
            $stmt = $this->db->prepare("SELECT * FROM anggota WHERE nim = ? AND ukm_id = ?");
            $stmt->execute([$nim, $ukmId]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM anggota WHERE nim = ?");
            $stmt->execute([$nim]);
        }
        return $stmt->fetch();
    }

    /** Tambah anggota baru */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO anggota (ukm_id, periode_id, nama, nim, email, hierarki, jabatan, status, fingerprint_id, foto_path) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['ukm_id'],
            $data['periode_id'] ?? 0,
            $data['nama'],
            $data['nim'] ?? null,
            $data['email'] ?? null,
            $data['hierarki'] ?? 'Anggota',
            $data['jabatan'] ?? 'Anggota',
            $data['status'] ?? 'aktif',
            $data['fingerprint_id'] ?? null,
            $data['foto_path'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update anggota */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE anggota 
             SET ukm_id = ?, periode_id = ?, nama = ?, nim = ?, email = ?, hierarki = ?, jabatan = ?, status = ?, fingerprint_id = ?, foto_path = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['ukm_id'],
            $data['periode_id'] ?? 0,
            $data['nama'],
            $data['nim'] ?? null,
            $data['email'] ?? null,
            $data['hierarki'] ?? 'Anggota',
            $data['jabatan'] ?? 'Anggota',
            $data['status'] ?? 'aktif',
            $data['fingerprint_id'] ?? null,
            $data['foto_path'] ?? null,
            $id,
        ]);
    }

    /** Hapus anggota beserta foto */
    public function delete(int $id): bool
    {
        $anggota = $this->getById($id);
        if ($anggota && !empty($anggota['foto_path'])) {
            FileUpload::delete($anggota['foto_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM anggota WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Hitung total anggota (opsional per UKM) */
    public function count(?int $ukmId = null, ?int $periodeId = null): int
    {
        $query = "SELECT COUNT(*) FROM anggota";
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

    /** Ambil anggota aktif per UKM */
    public function getActive(int $ukmId, ?int $periodeId = null): array
    {
        if ($periodeId) {
            $stmt = $this->db->prepare("SELECT * FROM anggota WHERE ukm_id = ? AND periode_id = ? AND status = 'aktif' ORDER BY nama ASC");
            $stmt->execute([$ukmId, $periodeId]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM anggota WHERE ukm_id = ? AND status = 'aktif' ORDER BY nama ASC");
            $stmt->execute([$ukmId]);
        }
        return $stmt->fetchAll();
    }
}
