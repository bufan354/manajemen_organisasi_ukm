<?php
require_once __DIR__ . '/../models/Pendaftaran.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Pendaftaran
 * Handle registrasi publik dan manajemen admin
 */
class PendaftaranController
{
    private Pendaftaran $model;

    public function __construct()
    {
        $this->model = new Pendaftaran();
    }

    /** Proses pendaftaran dari form publik */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=daftar_anggota');
        }

        $ukmId    = (int)($_POST['ukm_id'] ?? 0);
        $redirectBack = 'index.php?page=daftar_anggota&ukm_id=' . $ukmId;

        // Cek apakah pendaftaran UKM tujuan sedang ditutup
        require_once __DIR__ . '/../models/Pengaturan.php';
        $settingModel = new Pengaturan();
        $settingsRaw = $settingModel->getAll($ukmId);
        $settingsMap = [];
        foreach ($settingsRaw as $row) { $settingsMap[$row['kunci']] = $row['nilai']; }
        
        if (($settingsMap['form_reg_status'] ?? 'dibuka') === 'ditutup') {
            setFlash('error', 'Maaf, pendaftaran anggota baru untuk UKM ini sedang ditutup sementara.');
            redirect('index.php?page=katalog_ukm');
        }

        // ── Server-Side Validation ─────────────────────────────────────────
        $errors = [];

        if ($ukmId <= 0) {
            $errors[] = 'Pilih UKM yang ingin Anda ikuti.';
        }

        $requiredFields = [
            'nama'    => 'Nama Lengkap',
            'email'   => 'Alamat Email',
            'no_wa'   => 'Nomor WhatsApp',
            'jurusan' => 'Jurusan / Program Studi',
            'kelas'   => 'Kelas / Semester',
            'alasan'  => 'Motivasi bergabung',
        ];

        foreach ($requiredFields as $field => $label) {
            $val = trim($_POST[$field] ?? '');
            if ($val === '') {
                $errors[] = "{$label} tidak boleh kosong.";
            }
        }

        // Validasi format email
        $emailVal = trim($_POST['email'] ?? '');
        if ($emailVal !== '' && !filter_var($emailVal, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format Alamat Email tidak valid.';
        }

        // Validasi checkbox persetujuan
        if (!isset($_POST['persetujuan']) || $_POST['persetujuan'] !== 'on') {
            $errors[] = 'Anda harus menyetujui persyaratan biometrik sebelum mendaftar.';
        }

        if (!empty($errors)) {
            setFlash('error', 'Pendaftaran gagal: ' . implode(' ', $errors));
            redirect($redirectBack);
        }
        // ──────────────────────────────────────────────────────────────────

        require_once __DIR__ . '/../models/Periode.php';
        $activePeriode = (new Periode())->getActive($ukmId);

        $data = [
            'ukm_id'             => $ukmId,
            'periode_id'         => $activePeriode ? $activePeriode['id'] : 0,
            'nama'               => sanitize($_POST['nama'] ?? ''),
            'email'              => sanitize($_POST['email'] ?? ''),
            'no_wa'              => sanitize($_POST['no_wa'] ?? ''),
            'kelas'              => sanitize($_POST['kelas'] ?? ''),
            'jurusan'            => sanitize($_POST['jurusan'] ?? ''),
            'jawaban_kuisioner'  => isset($_POST['jawaban_kuisioner']) && is_array($_POST['jawaban_kuisioner']) ? json_encode($_POST['jawaban_kuisioner']) : null,
            'alasan'             => sanitize($_POST['alasan'] ?? ''),
        ];

        // Mencegah pendaftaran ganda ke UKM yang sama menggunakan email re-entry
        if ($this->model->isDuplicate($data['email'], $data['ukm_id'])) {
            setFlash('error', 'Email Anda sudah terdaftar di UKM ini dan sedang dalam proses atau sudah diterima.');
            redirect($redirectBack);
        }

        require_once __DIR__ . '/../models/Pengaturan.php';
        $settingModel = new Pengaturan();
        $settingsRaw = $settingModel->getAll($data['ukm_id']);
        $settingsMap = [];
        foreach ($settingsRaw as $row) { $settingsMap[$row['kunci']] = $row['nilai']; }
        $instruction = $settingsMap['form_reg_instruction'] ?? 'Pendaftaran berhasil dikirim! Silakan tunggu verifikasi admin.';

        $data['session_id'] = session_id();
        $this->model->create($data);

        // --- Trigger Notifikasi Pendaftaran Baru ---
        require_once __DIR__ . '/../models/Admin.php';
        require_once __DIR__ . '/../models/Ukm.php';
        $adminModel = new AdminModel();
        $ukmData = (new Ukm())->getById($ukmId);
        $ukmNama = $ukmData ? $ukmData['nama'] : 'UKM #' . $ukmId;
        
        $judulNotif = "Pendaftaran Baru";
        $pesanNotif = "Pendaftaran baru dari {$data['nama']} untuk {$ukmNama} – segera verifikasi.";
        $linkNotif  = "index.php?page=verifikasi_pendaftar"; // Arahkan ke halaman verifikasi

        // Notifikasi ke Admin UKM Terkait
        $admins = $adminModel->getByUkm($ukmId);
        foreach ($admins as $ad) {
            addNotifikasi($ad['id'], 'pendaftaran_baru', $judulNotif, $pesanNotif, $linkNotif, $ukmId);
        }

        // Notifikasi ke Semua Superadmin
        $superadmins = $adminModel->getByRole('superadmin');
        foreach ($superadmins as $sa) {
            addNotifikasi($sa['id'], 'pendaftaran_baru', $judulNotif, "Pendaftaran baru dari {$data['nama']} di {$ukmNama} – segera verifikasi.", $linkNotif, null);
        }
        // -------------------------------------------

        setFlash('success', $instruction);
        redirect('index.php?page=daftar_sukses');
    }

    /** Update status pendaftaran (admin) */
    public function updateStatus(): void
    {
        Session::requireLogin();

        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=verifikasi_pendaftar');
        }

        $id     = (int)($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'pending');
        $alasan = sanitize($_POST['alasan_tolak'] ?? '');

        if ($id > 0 && in_array($status, ['pending', 'diterima', 'ditolak'])) {
            $pendaftaran = $this->model->getById($id);
            if ($pendaftaran && $pendaftaran['status'] !== $status) {
                $this->model->updateStatus($id, $status, $alasan);
                
                // Jika baru saja diterima, otomatis pindahkan/tambahkan ke tabel anggota
                if ($status === 'diterima' && $pendaftaran['status'] === 'pending') {
                    require_once __DIR__ . '/../models/Anggota.php';
                    (new Anggota())->create([
                        'ukm_id'     => $pendaftaran['ukm_id'],
                        'periode_id' => $pendaftaran['periode_id'] ?? 0,
                        'nama'       => $pendaftaran['nama'],
                        'email'      => $pendaftaran['email'],
                        'jabatan'    => 'Anggota',
                        'status'     => 'aktif'
                    ]);
                }
                
                logSecurityActivity('Verifikasi Pendaftaran', ['id_pendaftar' => $id, 'status_baru' => $status, 'alasan' => $alasan]);
                setFlash('success', 'Status pendaftaran berhasil diperbarui.');
            }
        }
        redirect('index.php?page=verifikasi_pendaftar');
    }

    /** Hapus pendaftaran */
    public function delete(): void
    {
        Session::requireLogin();

        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=verifikasi_pendaftar');
        }

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
            logSecurityActivity('Hapus Data Pendaftaran', ['id_pendaftar' => $id]);
            setFlash('success', 'Data pendaftaran berhasil dihapus.');
        }
        redirect('index.php?page=verifikasi_pendaftar');
    }

    /** Clear pengikatan session reset (publik) */
    public function clearSession(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $ukmId = (int)($_POST['ukm_id'] ?? 0);
        if ($id > 0) {
            // Verifikasi validitas kepemilikan
            $pendaftaran = $this->model->getById($id);
            if ($pendaftaran && $pendaftaran['session_id'] === session_id()) {
                $this->model->clearSessionLink($id);
            }
        }
        redirect('index.php?page=daftar_anggota&ukm_id=' . $ukmId);
    }

    /** Simpan konfigurasi form pendaftaran (pertanyaan custom) */
    public function saveConfig(): void
    {
        Session::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=pendaftaran');
        }

        $ukmId = (int)($_POST['ukm_id'] ?? Session::get('ukm_id'));
        if ($ukmId <= 0) {
            setFlash('error', 'Pilih UKM terlebih dahulu.');
            redirect('index.php?page=pendaftaran');
        }

        // Pengecekan authorization opsional: pastikan superadmin atau admin ukm terkait

        require_once __DIR__ . '/../models/Pengaturan.php';
        $settingModel = new Pengaturan();

        $status = sanitize($_POST['regStatus'] ?? 'ditutup');
        $instruction = sanitize($_POST['regInstruction'] ?? '');
        
        $questions = [];
        if (!empty($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $q) {
                if (!empty(trim($q['text'] ?? ''))) {
                    $questions[] = [
                        'text' => sanitize($q['text']),
                        'required' => isset($q['required']) ? true : false
                    ];
                }
            }
        }
        
        $settingModel->set($ukmId, 'form_reg_status', $status);
        $settingModel->set($ukmId, 'form_reg_instruction', $instruction);
        $settingModel->set($ukmId, 'form_reg_questions', json_encode($questions));

        logSecurityActivity('Perbarui Konfigurasi Form', [
            'ukm_id' => $ukmId
        ]);

        setFlash('success', 'Konfigurasi pendaftaran berhasil disimpan.');
        redirect('index.php?page=pendaftaran' . (Session::get('admin_role') === 'superadmin' ? '&ukm_id='.$ukmId : ''));
    }
}
