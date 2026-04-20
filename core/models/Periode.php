<?php
require_once __DIR__ . '/../Database.php';

class Periode
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(int $ukmId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM periode WHERE ukm_id = ? ORDER BY tahun_mulai DESC");
        $stmt->execute([$ukmId]);
        return $stmt->fetchAll();
    }

    public function getActive(int $ukmId)
    {
        $stmt = $this->db->prepare("SELECT * FROM periode WHERE ukm_id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$ukmId]);
        return $stmt->fetch() ?: null;
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM periode WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function add(int $ukmId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO periode (ukm_id, nama, tahun_mulai, bulan_mulai, tahun_selesai, bulan_selesai, deskripsi, is_active) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 0)"
        );
        return $stmt->execute([
            $ukmId,
            $data['nama'],
            $data['tahun_mulai'],
            $data['bulan_mulai'] ?? null,
            $data['tahun_selesai'],
            $data['bulan_selesai'] ?? null,
            $data['deskripsi'] ?? null
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE periode 
             SET nama = ?, tahun_mulai = ?, bulan_mulai = ?, tahun_selesai = ?, bulan_selesai = ?, deskripsi = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['nama'],
            $data['tahun_mulai'],
            $data['bulan_mulai'] ?? null,
            $data['tahun_selesai'],
            $data['bulan_selesai'] ?? null,
            $data['deskripsi'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM periode WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setActive(int $ukmId, int $periodeId): bool
    {
        $this->db->beginTransaction();
        try {
            // Nonaktifkan semua periode untuk UKM ini
            $stmt1 = $this->db->prepare("UPDATE periode SET is_active = 0 WHERE ukm_id = ?");
            $stmt1->execute([$ukmId]);

            // Aktifkan periode yang dipilih
            $stmt2 = $this->db->prepare("UPDATE periode SET is_active = 1 WHERE id = ? AND ukm_id = ?");
            $stmt2->execute([$periodeId, $ukmId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
