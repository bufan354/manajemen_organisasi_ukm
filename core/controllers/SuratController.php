<?php
/**
 * Controller: Surat
 * Logika pemrosesan arsip, template, pimpinan, dan pengasahan.
 */
require_once __DIR__ . '/../models/Surat.php';
require_once __DIR__ . '/../models/Ukm.php';
require_once __DIR__ . '/../models/Pengaturan.php';
require_once __DIR__ . '/../FileUpload.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

class SuratController
{
    private SuratModel $model;

    public function __construct()
    {
        $this->model = new SuratModel();
    }

    public function index()
    {
        Session::requireLogin();
        $ukm_id = $this->getUkmId();
        $periode_id = $this->getPeriodeId($ukm_id);

        $ukm = (new Ukm())->getById($ukm_id);
        $arsip_L = $this->model->getAllArsip($ukm_id, $periode_id, 'L');
        $arsip_D = $this->model->getAllArsip($ukm_id, $periode_id, 'D');

        // Logic for view rendering is usually handled in index.php switch
        // but if we call it directly, we need to load the view.
        // The project uses $this->render in some places, but index.php uses include.
    }

    public function buat()
    {
        Session::requireLogin();
        $ukm_id = $this->getUkmId();
        $periode_id = $this->getPeriodeId($ukm_id);
        // ... (Logic continues in index.php)
    }

    public function store()
    {
        Session::requireLogin();
        $ukm_id = (int)$_POST['ukm_id'];
        $periode_id = $this->getPeriodeId($ukm_id);

        $konten = json_decode($_POST['konten_json'], true) ?? [];
        
        // Handle Signature Uploads
        if (isset($_FILES['ttd_ketua_file']) && FileUpload::hasFile('ttd_ketua_file')) {
            $path = FileUpload::upload($_FILES['ttd_ketua_file'], 'signatures');
            if ($path) $konten['ttd_ketua_custom'] = $path;
        } elseif (!empty($_POST['ttd_ketua_base64'])) {
            $path = FileUpload::saveBase64($_POST['ttd_ketua_base64'], 'signatures');
            if ($path) $konten['ttd_ketua_custom'] = $path;
        }

        if (isset($_FILES['ttd_sekre_file']) && FileUpload::hasFile('ttd_sekre_file')) {
            $path = FileUpload::upload($_FILES['ttd_sekre_file'], 'signatures');
            if ($path) $konten['ttd_sekre_custom'] = $path;
        } elseif (!empty($_POST['ttd_sekre_base64'])) {
            $path = FileUpload::saveBase64($_POST['ttd_sekre_base64'], 'signatures');
            if ($path) $konten['ttd_sekre_custom'] = $path;
        }

        // Handle PDF Attachments
        $uploaded_pdfs = [];
        if (isset($_FILES['lampiran_surat']) && !empty($_FILES['lampiran_surat']['name'][0])) {
            $uploaded_pdfs = FileUpload::uploadMultiple($_FILES['lampiran_surat'], 'lampiran_surat');
        }
        $konten['lampiran_uploaded'] = $uploaded_pdfs;

        $data = [
            'ukm_id' => $ukm_id,
            'periode_id' => $periode_id,
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'jenis_surat' => $_POST['jenis_surat'],
            'tanggal_dikirim' => $_POST['tanggal_dikirim'] ?: date('Y-m-d'),
            'nomor_surat' => $_POST['nomor_surat'],
            'perihal' => $_POST['perihal'],
            'tujuan' => $_POST['tujuan'],
            'tempat_tanggal' => $_POST['tempat_tanggal'],
            'konten_surat' => json_encode($konten),
            'created_by' => Session::get('admin_id')
        ];

        if ($this->model->createArsip($data)) {
            setFlash('success', 'Surat berhasil diarsipkan.');
        } else {
            setFlash('error', 'Gagal mengarsipkan surat.');
        }

        redirect('index.php?page=arsip_surat&ukm_id=' . $ukm_id);
    }

    public function update()
    {
        Session::requireLogin();
        $id = (int)$_POST['id'];
        $ukm_id = (int)$_POST['ukm_id'];

        $existing = $this->model->getArsipById($id, $ukm_id);
        $old_konten = json_decode($existing['konten_surat'], true) ?: [];
        $new_konten = json_decode($_POST['konten_json'], true) ?: [];

        // Carry over old signatures if not updated
        $new_konten['ttd_ketua_custom'] = $old_konten['ttd_ketua_custom'] ?? null;
        $new_konten['ttd_sekre_custom'] = $old_konten['ttd_sekre_custom'] ?? null;
        $new_konten['lampiran_uploaded'] = $old_konten['lampiran_uploaded'] ?? [];

        // Update Signatures
        if (FileUpload::hasFile('ttd_ketua_file')) {
            $path = FileUpload::upload($_FILES['ttd_ketua_file'], 'signatures');
            if ($path) $new_konten['ttd_ketua_custom'] = $path;
        } elseif (!empty($_POST['ttd_ketua_base64'])) {
            $path = FileUpload::saveBase64($_POST['ttd_ketua_base64'], 'signatures');
            if ($path) $new_konten['ttd_ketua_custom'] = $path;
        }

        if (FileUpload::hasFile('ttd_sekre_file')) {
            $path = FileUpload::upload($_FILES['ttd_sekre_file'], 'signatures');
            if ($path) $new_konten['ttd_sekre_custom'] = $path;
        } elseif (!empty($_POST['ttd_sekre_base64'])) {
            $path = FileUpload::saveBase64($_POST['ttd_sekre_base64'], 'signatures');
            if ($path) $new_konten['ttd_sekre_custom'] = $path;
        }

        // Update PDF Attachments
        if (isset($_FILES['lampiran_surat']) && !empty($_FILES['lampiran_surat']['name'][0])) {
            $new_pdfs = FileUpload::uploadMultiple($_FILES['lampiran_surat'], 'lampiran_surat');
            $new_konten['lampiran_uploaded'] = array_merge($new_konten['lampiran_uploaded'], $new_pdfs);
        }

        // Handle Deletions
        if (!empty($_POST['delete_lampiran'])) {
            $new_konten['lampiran_uploaded'] = array_diff($new_konten['lampiran_uploaded'], $_POST['delete_lampiran']);
        }

        $data = [
            'jenis_surat' => $_POST['jenis_surat'],
            'tanggal_dikirim' => $_POST['tanggal_dikirim'] ?: date('Y-m-d'),
            'nomor_surat' => $_POST['nomor_surat'],
            'perihal' => $_POST['perihal'],
            'tujuan' => $_POST['tujuan'],
            'tempat_tanggal' => $_POST['tempat_tanggal'],
            'konten_surat' => json_encode($new_konten)
        ];

        if ($this->model->updateArsip($id, $data)) {
            setFlash('success', 'Perubahan berhasil disimpan.');
        } else {
            setFlash('error', 'Gagal menyimpan perubahan.');
        }

        redirect('index.php?page=arsip_surat&ukm_id=' . $ukm_id);
    }

    public function delete()
    {
        Session::requireLogin();
        $id = (int)$_POST['id'];
        $ukm_id = $this->getUkmId();

        if ($this->model->deleteArsip($id, $ukm_id)) {
            setFlash('success', 'Arsip surat berhasil dihapus.');
        } else {
            setFlash('error', 'Gagal menghapus arsip.');
        }
        redirect('index.php?page=arsip_surat&ukm_id=' . $ukm_id);
    }

    public function saveGlobalSurat()
    {
        Session::requireLogin();
        $ukm_id = $this->getUkmId();
        
        foreach ($_POST as $key => $val) {
            if (in_array($key, ['warek_nama', 'warek_jabatan', 'presma_nama', 'presma_jabatan'])) {
                $this->model->saveGlobalSetting($key, sanitize($val));
            }
            if ($key === 'ketum_nama') {
                (new Pengaturan())->set($ukm_id, 'ketum_nama', sanitize($val));
            }
        }

        // Handle Stempel & TTD Global
        $files = ['warek_ttd', 'presma_ttd', 'cap_panitia', 'cap_warek', 'cap_bem'];
        foreach ($files as $f) {
            if (FileUpload::hasFile($f)) {
                $path = FileUpload::upload($_FILES[$f], 'global_surat');
                if ($path) $this->model->saveGlobalSetting($f, $path);
            }
        }

        // Handle UKM-specific Assets (Cap & Ketum TTD)
        $ukm_files = ['cap_ukm', 'ketum_ttd'];
        foreach ($ukm_files as $f) {
            if (FileUpload::hasFile($f)) {
                $path = FileUpload::upload($_FILES[$f], 'ukm_assets');
                if ($path) {
                    (new Pengaturan())->set($ukm_id, $f, $path);
                }
            }
        }

        setFlash('success', 'Pengaturan berhasil diperbarui.');
        redirect('index.php?page=pengaturan_surat&ukm_id=' . $ukm_id);
    }

    public function saveKopSurat()
    {
        Session::requireLogin();
        $ukm_id = (int)$this->getUkmId();

        if (FileUpload::hasFile('kop_file')) {
            $path = FileUpload::upload($_FILES['kop_file'], 'kop_surat');
            if ($path) {
                (new Pengaturan())->set($ukm_id, 'kop_surat', $path);
                setFlash('success', 'Kop surat berhasil diperbarui.');
            } else {
                setFlash('error', 'Gagal mengunggah kop surat.');
            }
        }
        redirect('index.php?page=pengaturan_surat&ukm_id=' . $ukm_id);
    }

    public function saveKop()
    {
        Session::requireLogin();
        $ukm_id = (int)$this->getUkmId();

        if (FileUpload::hasFile('kop_file')) {
            $path = FileUpload::upload($_FILES['kop_file'], 'kop_surat');
            if ($path) {
                (new Pengaturan())->set($ukm_id, 'kop_surat', $path);
                setFlash('success', 'Kop surat arsip berhasil diperbarui.');
            } else {
                setFlash('error', 'Gagal mengunggah kop surat.');
            }
        }
        redirect('index.php?page=arsip_surat&ukm_id=' . $ukm_id);
    }

    public function saveTemplate()
    {
        Session::requireLogin();
        $ukm_id = $this->getUkmId();
        $periode_id = $this->getPeriodeId($ukm_id);

        $data = [
            'ukm_id' => $ukm_id,
            'periode_id' => $periode_id,
            'label' => sanitize($_POST['label']),
            'jenis' => $_POST['jenis'],
            'isi_teks' => $_POST['isi_teks'] ?? '',
            'perihal_default' => sanitize($_POST['perihal_default'] ?? null)
        ];

        if (!empty($_POST['id'])) {
            $this->model->updateTemplate((int)$_POST['id'], $data);
        } else {
            $this->model->createTemplate($data);
        }

        setFlash('success', 'Template berhasil disimpan.');
        redirect('index.php?page=pengaturan_surat&ukm_id=' . $ukm_id);
    }

    public function deleteTemplate()
    {
        Session::requireLogin();
        $id = (int)$_POST['id'];
        $ukm_id = $this->getUkmId();

        $this->model->deleteTemplate($id, $ukm_id);
        setFlash('success', 'Template berhasil dihapus.');
        redirect('index.php?page=pengaturan_surat&ukm_id=' . $ukm_id);
    }

    public function savePanitiaTetap()
    {
        Session::requireLogin();
        $ukm_id = $this->getUkmId();
        $periode_id = $this->getPeriodeId($ukm_id);

        $ttd_path = null;
        if (FileUpload::hasFile('ttd_file')) {
            $ttd_path = FileUpload::upload($_FILES['ttd_file'], 'signatures');
        } elseif (!empty($_POST['ttd_base64'])) {
            $ttd_path = FileUpload::saveBase64($_POST['ttd_base64'], 'signatures');
        }

        $data = [
            'ukm_id' => $ukm_id,
            'periode_id' => $periode_id,
            'nama' => strtoupper(sanitize($_POST['nama'])),
            'jabatan' => $_POST['jabatan'],
            'type' => $_POST['type'] ?? 'panitia',
            'ttd_path' => $ttd_path
        ];

        if ($this->model->savePanitia($data)) {
            setFlash('success', 'Data panitia berhasil disimpan.');
        } else {
            setFlash('error', 'Gagal menyimpan data panitia.');
        }

        redirect('index.php?page=pengaturan_surat&ukm_id=' . $ukm_id);
    }

    public function deletePanitiaTetap()
    {
        Session::requireLogin();
        $id = (int)$_POST['id'];
        $ukm_id = $this->getUkmId();

        $this->model->deletePanitia($id, $ukm_id);
        setFlash('success', 'Data panitia berhasil dihapus.');
        redirect('index.php?page=pengaturan_surat&ukm_id=' . $ukm_id);
    }

    private function getUkmId()
    {
        $id = (int)(Session::get('ukm_id') ?: ($_POST['ukm_id'] ?? $_GET['ukm_id'] ?? 0));
        if ($id === 0 && Session::get('admin_role') === 'superadmin') {
            $id = (int)(Session::get('last_ukm_id') ?? 0);
        }
        return $id;
    }

    private function getPeriodeId($ukm_id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM periode WHERE ukm_id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$ukm_id]);
        $res = $stmt->fetch();
        return $res ? $res['id'] : 0;
    }
}
