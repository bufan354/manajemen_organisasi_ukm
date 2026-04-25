<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Pengaturan
 * Key-value settings per UKM
 */
class Pengaturan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua pengaturan per UKM */
    public function getAll(int $ukmId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pengaturan WHERE ukm_id = ?");
        $stmt->execute([$ukmId]);
        return $stmt->fetchAll();
    }

    /** Ambil satu nilai setting */
    public function get(int $ukmId, string $kunci, ?string $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT nilai FROM pengaturan WHERE ukm_id = ? AND kunci = ?");
        $stmt->execute([$ukmId, $kunci]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    }

    /** Set atau update satu nilai setting (upsert) */
    public function set(int $ukmId, string $kunci, ?string $nilai): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pengaturan (ukm_id, kunci, nilai) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)"
        );
        return $stmt->execute([$ukmId, $kunci, $nilai]);
    }

    /** Hapus setting */
    public function delete(int $ukmId, string $kunci): bool
    {
        $stmt = $this->db->prepare("DELETE FROM pengaturan WHERE ukm_id = ? AND kunci = ?");
        return $stmt->execute([$ukmId, $kunci]);
    }

    /** Simpan banyak setting sekaligus */
    public function setMany(int $ukmId, array $settings): void
    {
        foreach ($settings as $kunci => $nilai) {
            $this->set($ukmId, $kunci, $nilai);
        }
    }
}
