<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Notifikasi
 */
class NotifikasiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Membuat notifikasi baru
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO notifikasi (user_id, ukm_id, jenis, judul, pesan, link)
             VALUES (:user_id, :ukm_id, :jenis, :judul, :pesan, :link)"
        );

        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':ukm_id'  => $data['ukm_id'] ?? null,
            ':jenis'   => $data['jenis'],
            ':judul'   => $data['judul'],
            ':pesan'   => $data['pesan'],
            ':link'    => $data['link'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Mendapatkan daftar notifikasi untuk user (admin/superadmin) terbaru
     * @param int $userId ID User/Admin penerima
     * @param int $limit Batas notifikasi yang diambil
     */
    public function getAllByUser(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifikasi 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC 
             LIMIT :limit"
        );
        // Bind parameters manually to handle integer correctly for LIMIT
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan jumlah notifikasi yang belum dibaca
     */
    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = :user_id AND is_dibaca = 0");
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Tandai sebuah notifikasi sudah dibaca
     */
    public function markAsRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifikasi SET is_dibaca = 1 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId
        ]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca untuk user tertentu
     */
    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifikasi SET is_dibaca = 1 WHERE user_id = :user_id AND is_dibaca = 0");
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Hapus semua notifikasi untuk user tertentu
     */
    public function deleteAll(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM notifikasi WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Hapus satu notifikasi tertentu
     */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM notifikasi WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId
        ]);
    }
}
