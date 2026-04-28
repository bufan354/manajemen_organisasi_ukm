<?php
/**
 * Model Surat
 * Mengelola data arsip surat, template, dan panitia tetap.
 */
require_once __DIR__ . '/../Database.php';

class SuratModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // --- ARSIP SURAT ---

    public function getAllArsip(int $ukm_id, int $periode_id, string $jenis = 'L')
    {
        $stmt = $this->db->prepare("SELECT * FROM arsip_surat WHERE ukm_id = ? AND periode_id = ? AND jenis_surat = ? ORDER BY id ASC");
        $stmt->execute([$ukm_id, $periode_id, $jenis]);
        return $stmt->fetchAll();
    }

    public function getArsipById(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM arsip_surat WHERE id = ? AND ukm_id = ?");
        $stmt->execute([$id, $ukm_id]);
        return $stmt->fetch();
    }

    public function getArsipByNomor(string $nomor, int $ukm_id, int $periode_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM arsip_surat WHERE nomor_surat = ? AND ukm_id = ? AND periode_id = ?");
        $stmt->execute([$nomor, $ukm_id, $periode_id]);
        return $stmt->fetchAll();
    }

    public function createArsip(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO arsip_surat ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function updateArsip(int $id, array $data)
    {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "$key = ?";
        }
        $sql = "UPDATE arsip_surat SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public function deleteArsip(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("DELETE FROM arsip_surat WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$id, $ukm_id]);
    }

    public function getMaxId(int $ukm_id, int $periode_id, string $jenis)
    {
        $stmt = $this->db->prepare("SELECT MAX(id) as last_id FROM arsip_surat WHERE ukm_id = ? AND periode_id = ? AND jenis_surat = ?");
        $stmt->execute([$ukm_id, $periode_id, $jenis]);
        return (int)($stmt->fetch()['last_id'] ?? 0);
    }

    public function getMaxSequence(int $ukm_id, int $periode_id, string $jenis)
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(CAST(SUBSTRING_INDEX(nomor_surat, '/', 1) AS UNSIGNED)) as last_seq 
             FROM arsip_surat 
             WHERE ukm_id = ? AND periode_id = ? AND jenis_surat = ? AND parent_id IS NULL"
        );
        $stmt->execute([$ukm_id, $periode_id, $jenis]);
        return (int)($stmt->fetch()['last_seq'] ?? 0);
    }

    // --- TEMPLATES ---

    public function getTemplates(int $ukm_id, int $periode_id, ?string $jenis = null)
    {
        if ($jenis) {
            $stmt = $this->db->prepare("SELECT * FROM surat_templates WHERE ukm_id = ? AND periode_id = ? AND jenis = ? ORDER BY label ASC");
            $stmt->execute([$ukm_id, $periode_id, $jenis]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM surat_templates WHERE ukm_id = ? AND periode_id = ? ORDER BY jenis, label ASC");
            $stmt->execute([$ukm_id, $periode_id]);
        }
        return $stmt->fetchAll();
    }

    public function getTemplateById(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM surat_templates WHERE id = ? AND ukm_id = ?");
        $stmt->execute([$id, $ukm_id]);
        return $stmt->fetch();
    }

    public function createTemplate(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO surat_templates (ukm_id, periode_id, label, jenis, isi_teks, perihal_default) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['ukm_id'], $data['periode_id'], $data['label'], $data['jenis'], $data['isi_teks'], $data['perihal_default'] ?? null]);
    }

    public function updateTemplate(int $id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE surat_templates SET label = ?, jenis = ?, isi_teks = ?, perihal_default = ? WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$data['label'], $data['jenis'], $data['isi_teks'], $data['perihal_default'] ?? null, $id, $data['ukm_id']]);
    }

    public function deleteTemplate(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("DELETE FROM surat_templates WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$id, $ukm_id]);
    }

    // --- PANITIA TETAP ---

    public function getPanitia(int $ukm_id, int $periode_id, ?string $type = null)
    {
        if ($type) {
            $stmt = $this->db->prepare("SELECT * FROM panitia_tetap WHERE ukm_id = ? AND periode_id = ? AND type = ? ORDER BY id DESC");
            $stmt->execute([$ukm_id, $periode_id, $type]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM panitia_tetap WHERE ukm_id = ? AND periode_id = ? ORDER BY type ASC, id DESC");
            $stmt->execute([$ukm_id, $periode_id]);
        }
        return $stmt->fetchAll();
    }

    public function savePanitia(array $data)
    {
        // Untuk type 'inti' (Ketua/Sekre Umum), kita cek jabatan untuk update
        if ($data['type'] === 'inti') {
            $stmt = $this->db->prepare("SELECT id FROM panitia_tetap WHERE ukm_id = ? AND periode_id = ? AND jabatan = ? AND type = 'inti'");
            $stmt->execute([$data['ukm_id'], $data['periode_id'], $data['jabatan']]);
            $existing = $stmt->fetch();

            if ($existing) {
                $stmt = $this->db->prepare("UPDATE panitia_tetap SET nama = ?, ttd_path = ? WHERE id = ?");
                return $stmt->execute([$data['nama'], $data['ttd_path'], $existing['id']]);
            }
        }

        // Untuk type 'panitia' atau jika 'inti' baru, insert saja
        $stmt = $this->db->prepare("INSERT INTO panitia_tetap (ukm_id, periode_id, nama, jabatan, type, ttd_path) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['ukm_id'], $data['periode_id'], $data['nama'], $data['jabatan'], $data['type'] ?? 'panitia', $data['ttd_path']]);
    }

    public function deletePanitia(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("DELETE FROM panitia_tetap WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$id, $ukm_id]);
    }

    // --- GLOBAL SURAT SETTINGS ---

    public function getGlobalSetting(string $key, $default = null)
    {
        $stmt = $this->db->prepare("SELECT nama_val FROM pengaturan_surat_global WHERE nama_key = ?");
        $stmt->execute([$key]);
        $res = $stmt->fetch();
        return $res ? $res['nama_val'] : $default;
    }

    public function getAllGlobalSettings()
    {
        $stmt = $this->db->query("SELECT nama_key, nama_val FROM pengaturan_surat_global");
        $res = $stmt->fetchAll();
        $settings = [];
        foreach ($res as $row) {
            $settings[$row['nama_key']] = $row['nama_val'];
        }
        return $settings;
    }

    public function saveGlobalSetting(string $key, string $val)
    {
        $stmt = $this->db->prepare("INSERT INTO pengaturan_surat_global (nama_key, nama_val) VALUES (?, ?) ON DUPLICATE KEY UPDATE nama_val = ?");
        return $stmt->execute([$key, $val, $val]);
    }

    // --- LAMPIRAN PINJAM ---

    public function getArsipLampiran(int $ukm_id, int $periode_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM lampiran_pinjam WHERE ukm_id = ? AND periode_id = ? ORDER BY id DESC");
        $stmt->execute([$ukm_id, $periode_id]);
        return $stmt->fetchAll();
    }

    public function getLampiranById(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM lampiran_pinjam WHERE id = ? AND ukm_id = ?");
        $stmt->execute([$id, $ukm_id]);
        return $stmt->fetch();
    }

    public function createLampiran(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO lampiran_pinjam (ukm_id, periode_id, nama_acara, tanggal_kegiatan, tahun, barang_json) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['ukm_id'], $data['periode_id'], $data['nama_acara'], $data['tanggal_kegiatan'], $data['tahun'], $data['barang_json']]);
    }

    public function deleteLampiran(int $id, int $ukm_id)
    {
        $stmt = $this->db->prepare("DELETE FROM lampiran_pinjam WHERE id = ? AND ukm_id = ?");
        return $stmt->execute([$id, $ukm_id]);
    }
}
