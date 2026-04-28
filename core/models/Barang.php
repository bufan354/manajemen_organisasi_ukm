<?php
/**
 * Model Barang
 * Mengelola data master inventaris barang.
 */
require_once __DIR__ . '/../Database.php';

class BarangModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(int $ukm_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM barang_master WHERE ukm_id = ? ORDER BY nama_barang ASC");
        $stmt->execute([$ukm_id]);
        return $stmt->fetchAll();
    }

    public function create(int $ukm_id, string $nama_barang, string $satuan = 'Pcs')
    {
        $stmt = $this->db->prepare("INSERT INTO barang_master (ukm_id, nama_barang, satuan) VALUES (?, ?, ?)");
        return $stmt->execute([$ukm_id, $nama_barang, $satuan]);
    }

    public function update(int $id, int $ukm_id, string $nama_barang, string $satuan = 'Pcs')
    {
        $stmt = $this->db->prepare("UPDATE barang_master SET nama_barang = ?, satuan = ? WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$nama_barang, $satuan, $id, $ukm_id]);
    }

    public function delete(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("DELETE FROM barang_master WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$id, $ukm_id]);
    }
}
