<?php
require_once __DIR__ . '/../models/Periode.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: PeriodeController
 * Handle CRUD untuk Kelola Periode (Super Admin)
 */
class PeriodeController
{
    private Periode $model;

    public function __construct()
    {
        $this->model = new Periode();
    }

    public function store(): void
    {
        Session::requireSuperAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php?page=dashboard');

        $ukmId = (int)($_POST['ukm_id'] ?? 0);
        $data = [
            'nama' => sanitize($_POST['nama'] ?? ''),
            'bulan_mulai' => (int)($_POST['bulan_mulai'] ?? 1),
            'tahun_mulai' => (int)($_POST['tahun_mulai'] ?? 0),
            'bulan_selesai' => (int)($_POST['bulan_selesai'] ?? 12),
            'tahun_selesai' => (int)($_POST['tahun_selesai'] ?? 0),
            'deskripsi' => sanitize($_POST['deskripsi'] ?? '')
        ];

        if ($ukmId > 0 && !empty($data['nama'])) {
            $this->model->add($ukmId, $data);
            logSecurityActivity('Tambah Periode UKM', ['ukm_id' => $ukmId, 'nama' => $data['nama']]);
            setFlash('success', 'Periode baru berhasil ditambahkan.');
        } else {
            setFlash('error', 'Cek kelengkapan data!');
        }
        redirect("index.php?page=kelola_periode&ukm_id=$ukmId");
    }

    public function update(): void
    {
        Session::requireSuperAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php?page=dashboard');

        $id = (int)($_POST['id'] ?? 0);
        $ukmId = (int)($_POST['ukm_id'] ?? 0);
        $data = [
            'nama' => sanitize($_POST['nama'] ?? ''),
            'bulan_mulai' => (int)($_POST['bulan_mulai'] ?? 1),
            'tahun_mulai' => (int)($_POST['tahun_mulai'] ?? 0),
            'bulan_selesai' => (int)($_POST['bulan_selesai'] ?? 12),
            'tahun_selesai' => (int)($_POST['tahun_selesai'] ?? 0),
            'deskripsi' => sanitize($_POST['deskripsi'] ?? '')
        ];

        if ($id > 0 && !empty($data['nama'])) {
            $this->model->update($id, $data);
            logSecurityActivity('Ubah Periode UKM', ['periode_id' => $id, 'nama' => $data['nama']]);
            setFlash('success', 'Periode berhasil diperbarui.');
        }
        redirect("index.php?page=kelola_periode&ukm_id=$ukmId");
    }

    public function delete(): void
    {
        Session::requireSuperAdmin();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $ukmId = (int)($_POST['ukm_id'] ?? $_GET['ukm_id'] ?? 0);

        if ($id > 0) {
            // Delete associated physical files
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT file_surat, konten_surat FROM arsip_surat WHERE periode_id = ?");
            $stmt->execute([$id]);
            $suratList = $stmt->fetchAll();

            foreach ($suratList as $s) {
                if (!empty($s['file_surat']) && file_exists(__DIR__ . '/../../' . $s['file_surat'])) {
                    unlink(__DIR__ . '/../../' . $s['file_surat']);
                }
                $konten = json_decode($s['konten_surat'], true) ?? [];
                if (!empty($konten['lampiran_uploaded']) && is_array($konten['lampiran_uploaded'])) {
                    foreach ($konten['lampiran_uploaded'] as $l) {
                        if (!empty($l) && file_exists(__DIR__ . '/../../' . $l)) {
                            unlink(__DIR__ . '/../../' . $l);
                        }
                    }
                }
                if (!empty($konten['ttd_ketua_custom']) && file_exists(__DIR__ . '/../../' . $konten['ttd_ketua_custom'])) {
                    unlink(__DIR__ . '/../../' . $konten['ttd_ketua_custom']);
                }
                if (!empty($konten['ttd_sekre_custom']) && file_exists(__DIR__ . '/../../' . $konten['ttd_sekre_custom'])) {
                    unlink(__DIR__ . '/../../' . $konten['ttd_sekre_custom']);
                }
            }

            $this->model->delete($id);
            logSecurityActivity('Hapus Periode UKM', ['periode_id' => $id]);
            setFlash('success', 'Periode beserta seluruh arsip fisiknya berhasil dihapus.');
        }
        redirect("index.php?page=kelola_periode&ukm_id=$ukmId");
    }

    public function setActive(): void
    {
        Session::requireSuperAdmin();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $ukmId = (int)($_POST['ukm_id'] ?? $_GET['ukm_id'] ?? 0);

        if ($id > 0 && $ukmId > 0) {
            $this->model->setActive($ukmId, $id);
            logSecurityActivity('Ubah Periode Aktif', ['ukm_id' => $ukmId, 'periode_id' => $id]);
            setFlash('success', 'Periode aktif berhasil diubah.');
        }
        redirect("index.php?page=kelola_periode&ukm_id=$ukmId");
    }
}
