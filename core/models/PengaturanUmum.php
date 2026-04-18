<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: PengaturanUmum
 * Global key-value settings (bukan per-UKM, tapi untuk seluruh sistem).
 * Digunakan untuk kustomisasi label entitas, hero section, dll.
 */
class PengaturanUmum
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil satu nilai setting berdasarkan key */
    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT value FROM pengaturan_umum WHERE key_name = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    }

    /** Set atau update satu nilai setting (upsert) */
    public function set(string $key, ?string $value): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pengaturan_umum (key_name, value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE value = VALUES(value)"
        );
        return $stmt->execute([$key, $value]);
    }

    /** Ambil semua pengaturan sebagai array key => value */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT key_name, value FROM pengaturan_umum");
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $map[$row['key_name']] = $row['value'];
        }
        return $map;
    }

    /** Simpan banyak setting sekaligus (upsert) */
    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
