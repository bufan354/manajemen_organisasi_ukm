<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Admin
 * CRUD untuk entitas Admin & Superadmin
 */
class AdminModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua admin */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT a.*, u.singkatan AS ukm_nama 
             FROM admins a
             LEFT JOIN ukm u ON a.ukm_id = u.id
             ORDER BY a.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /** Ambil admin by ID */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.singkatan AS ukm_nama 
             FROM admins a
             LEFT JOIN ukm u ON a.ukm_id = u.id 
             WHERE a.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Cari admin by email (untuk login) */
    public function getByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Tambah admin baru */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO admins (ukm_id, nama, email, password, role, periode_id, foto_path) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['ukm_id'] ?: null,
            $data['nama'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'admin',
            $data['periode_id'] ?: null,
            $data['foto_path'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update admin */
    public function update(int $id, array $data): bool
    {
        $fields = "nama = ?, email = ?, role = ?, ukm_id = ?, periode_id = ?, foto_path = ?";
        $params = [
            $data['nama'],
            $data['email'],
            $data['role'],
            $data['ukm_id'] ?: null,
            $data['periode_id'] ?: null,
            $data['foto_path'] ?? null,
        ];

        // Update password hanya jika diisi
        if (!empty($data['password'])) {
            $fields .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE admins SET {$fields} WHERE id = ?");
        return $stmt->execute($params);
    }

    /** Hapus admin beserta foto */
    public function delete(int $id): bool
    {
        $admin = $this->getById($id);
        if ($admin && !empty($admin['foto_path'])) {
            FileUpload::delete($admin['foto_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM admins WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Verifikasi password untuk login */
    public function verifyPassword(string $email, string $password): array|false
    {
        $admin = $this->getByEmail($email);
        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    /** Hitung total admin */
    public function count(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    }

    /** Ambil semua admin berdasarkan role */
    public function getByRole(string $role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    /** Ambil semua admin berdasrkan ukm */
    public function getByUkm(int $ukmId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE ukm_id = ?");
        $stmt->execute([$ukmId]);
        return $stmt->fetchAll();
    }

    public function updateTotpSettings(int $id, string $secret, int $isActive = 1): bool
    {
        $stmt = $this->db->prepare("UPDATE admins SET totp_secret = ?, is_2fa_active = ? WHERE id = ?");
        return $stmt->execute([$secret, $isActive, $id]);
    }

    /** Reset 2FA status (disable and clear secret) */
    public function reset2FA(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE admins SET totp_secret = NULL, is_2fa_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
