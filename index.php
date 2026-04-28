<?php
/**
 * Main Router: Sistem Absensi IoT
 * Handles page rendering AND form action processing
 */

// ===== Bootstrap: Load Core =====
date_default_timezone_set('Asia/Jakarta');
require_once 'core/Database.php';
require_once 'core/Session.php';
require_once 'core/FileUpload.php';
require_once 'core/helpers.php';
require_once 'core/View.php';

Session::start();

// ===== Global: Load Entity Label untuk semua view =====
$ENTITY = getEntityLabel();
$APP_NAME = getSetting('app_name', 'The Digital Curator');

// ===== Middleware: Cek Sesi Multi-Perangkat / Timeout Inactivity =====
if (Session::isLoggedIn()) {
    require_once 'core/models/AdminSession.php';
    $sessionModel = new AdminSessionModel();
    $sid = session_id();

    if ($sessionModel->isSessionExpired($sid)) {
        $sessionModel->revokeSession($sid);
        Session::logout();
        setFlash('error', 'Sesi berakhir karena tidak aktif selama 30 menit.');
        redirect('index.php?page=login');
    } else {
        $sessionModel->updateActivity($sid);
        if (rand(1, 100) <= 5) {
            $sessionModel->clearExpiredSessions();
        }
    }
}

// ===== Action Router (POST form submissions) =====
$action = $_GET['action'] ?? null;

if ($action) {
    // CSRF Protection for all POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'logout') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Session::validateCsrf($token)) {
            logSecurityActivity('CSRF Violation Attempted', ['action' => $action]);

            // Deteksi apakah request dari AJAX (fetch/XMLHttpRequest)
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                   || !empty($_SERVER['HTTP_X_CSRF_TOKEN'])
                   || ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json';

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Sesi tidak valid (CSRF Token mismatch). Silakan refresh halaman.']);
                exit;
            }

            // Untuk form submission biasa: redirect kembali dengan flash error
            setFlash('error', 'Sesi keamanan tidak valid atau sudah berakhir. Silakan refresh halaman dan coba lagi.');
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=dashboard';
            redirect($referer);
        }
    }

    switch ($action) {
        // --- Auth ---
        case 'login':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->login();
            break;
        case 'logout':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->logout();
            break;
        case 'verify_2fa_post':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->verify2FA();
            break;

        // --- UKM ---
        case 'ukm_store':
            require_once 'core/controllers/UkmController.php';
            (new UkmController())->store();
            break;
        case 'ukm_update':
            require_once 'core/controllers/UkmController.php';
            (new UkmController())->update();
            break;
        case 'ukm_delete':
            require_once 'core/controllers/UkmController.php';
            (new UkmController())->delete();
            break;
        case 'ukm_toggle_status':
            require_once 'core/controllers/UkmController.php';
            (new UkmController())->toggleStatus();
            break;

        // --- Anggota ---
        case 'anggota_store':
            require_once 'core/controllers/AnggotaController.php';
            (new AnggotaController())->store();
            break;
        case 'anggota_update':
            require_once 'core/controllers/AnggotaController.php';
            (new AnggotaController())->update();
            break;
        case 'anggota_delete':
            require_once 'core/controllers/AnggotaController.php';
            (new AnggotaController())->delete();
            break;

        // --- Jabatan Kustom ---
        case 'jabatan_store':
            require_once 'core/controllers/JabatanController.php';
            (new JabatanController())->store();
            break;
        case 'jabatan_update':
            require_once 'core/controllers/JabatanController.php';
            (new JabatanController())->update();
            break;
        case 'jabatan_delete':
            require_once 'core/controllers/JabatanController.php';
            (new JabatanController())->delete();
            break;

        // --- Admin ---
        case 'admin_store':
            require_once 'core/controllers/AdminController.php';
            (new AdminController())->store();
            break;
        case 'admin_update':
            require_once 'core/controllers/AdminController.php';
            (new AdminController())->update();
            break;
        case 'admin_delete':
            require_once 'core/controllers/AdminController.php';
            (new AdminController())->delete();
            break;
        case 'admin_reset_access':
            require_once 'core/controllers/AdminController.php';
            (new AdminController())->resetAccess();
            break;
        case 'update_profile':
            require_once 'core/controllers/AdminController.php';
            (new AdminController())->updateProfile();
            break;
        case 'update_password':
            require_once 'core/controllers/AdminController.php';
            (new AdminController())->updatePassword();
            break;

        // --- Berita ---
        case 'berita_store':
            require_once 'core/controllers/BeritaController.php';
            (new BeritaController())->store();
            break;
        case 'berita_update':
            require_once 'core/controllers/BeritaController.php';
            (new BeritaController())->update();
            break;
        case 'berita_delete':
            require_once 'core/controllers/BeritaController.php';
            (new BeritaController())->delete();
            break;

        // --- Event ---
        case 'event_store':
            require_once 'core/controllers/EventController.php';
            (new EventController())->store();
            break;
        case 'event_update':
            require_once 'core/controllers/EventController.php';
            (new EventController())->update();
            break;
        case 'event_delete':
            require_once 'core/controllers/EventController.php';
            (new EventController())->delete();
            break;
        case 'export_kegiatan':
            require_once 'core/controllers/EventController.php';
            (new EventController())->export();
            break;
        case 'export_detail_kegiatan':
            require_once 'core/controllers/EventController.php';
            (new EventController())->exportDetail();
            break;
        case 'event_postpone':
            require_once 'core/controllers/EventController.php';
            (new EventController())->postpone();
            break;
        case 'event_cancel':
            require_once 'core/controllers/EventController.php';
            (new EventController())->cancel();
            break;
        case 'event_redaksi':
            require_once 'core/controllers/EventController.php';
            (new EventController())->generateRedaksi();
            break;
        case 'event_import_csv':
            require_once 'core/controllers/EventController.php';
            (new EventController())->importCsv();
            break;
        case 'kehadiran_import_csv':
            require_once 'core/controllers/EventController.php';
            (new EventController())->importKehadiranCsv();
            break;
        case 'kehadiran_toggle_manual':
            require_once 'core/controllers/EventController.php';
            (new EventController())->toggleKehadiranManual();
            break;

        // --- Periode ---
        case 'periode_store':
            require_once 'core/controllers/PeriodeController.php';
            (new PeriodeController())->store();
            break;
        case 'periode_update':
            require_once 'core/controllers/PeriodeController.php';
            (new PeriodeController())->update();
            break;
        case 'periode_delete':
            require_once 'core/controllers/PeriodeController.php';
            (new PeriodeController())->delete();
            break;
        case 'periode_set_active':
            require_once 'core/controllers/PeriodeController.php';
            (new PeriodeController())->setActive();
            break;

        // --- Pendaftaran ---
        case 'pendaftaran_register':
            require_once 'core/controllers/PendaftaranController.php';
            (new PendaftaranController())->register();
            break;
        case 'pendaftaran_status':
            require_once 'core/controllers/PendaftaranController.php';
            (new PendaftaranController())->updateStatus();
            break;
        case 'pendaftaran_delete':
            require_once 'core/controllers/PendaftaranController.php';
            (new PendaftaranController())->delete();
            break;
        case 'pendaftaran_clear_session':
            require_once 'core/controllers/PendaftaranController.php';
            (new PendaftaranController())->clearSession();
            break;
        case 'pendaftaran_config_save':
            require_once 'core/controllers/PendaftaranController.php';
            (new PendaftaranController())->saveConfig();
            break;

        // --- Pengaturan & Sesi Aktif ---
        case 'pengaturan_save':
            require_once 'core/controllers/PengaturanController.php';
            (new PengaturanController())->save();
            break;
        case 'revoke_session':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->revokeSession();
            break;
        case 'revoke_all_sessions':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->revokeAllSessions();
            break;
        case 'setup_2fa_dashboard':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->setup2faDashboard();
            break;
        case 'init_setup_2fa_dashboard':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->initSetup2faDashboard();
            break;
        case 'reset_2fa_dashboard':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->reset2faDashboard();
            break;
        case 'qr_image_2fa':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->qrImage2fa();
            break;
        case 'cancel_setup_2fa':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->cancelSetup2faDashboard();
            break;

        // --- Backup & Restore ---
        case 'backup_verify_password':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->verifyPassword();
            break;
        case 'backup_verify_2fa':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->verify2FA();
            break;
        case 'backup_check_fingerprint':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->checkFingerprint();
            break;
        case 'backup_heartbeat':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->heartbeat();
            break;
        case 'backup_download':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->download();
            break;
        case 'download_2fa_backup':
            require_once 'core/controllers/AuthController.php';
            (new AuthController())->downloadBackup2fa();
            break;

        // --- Konfigurasi Umum (Superadmin) ---
        case 'konfigurasi_umum_save':
            require_once 'core/controllers/KonfigurasiUmumController.php';
            (new KonfigurasiUmumController())->save();
            break;

        // --- Log Keamanan ---
        case 'log_keamanan_export_csv':
            require_once 'core/controllers/LogKeamananController.php';
            (new LogKeamananController())->exportExcel();
            break;
        case 'log_keamanan_export_json':
            require_once 'core/controllers/LogKeamananController.php';
            (new LogKeamananController())->exportJson();
            break;

        // --- Fingerprint AJAX ---
        case 'fingerprint_check_status':
            require_once 'core/controllers/FingerprintController.php';
            (new FingerprintController())->checkStatusAjax();
            break;
        case 'fingerprint_set_enroll':
            require_once 'core/controllers/FingerprintController.php';
            (new FingerprintController())->setEnrollModeAjax();
            break;
        case 'fingerprint_set_delete':
            require_once 'core/controllers/FingerprintController.php';
            (new FingerprintController())->setDeleteModeAjax();
            break;
        case 'fingerprint_cancel_enroll':
            require_once 'core/controllers/FingerprintController.php';
            (new FingerprintController())->cancelEnrollAjax();
            break;

        // --- Backup (Superadmin) ---
        case 'backup_verify_password':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->verifyPassword();
            break;
        case 'backup_verify_2fa':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->verify2FA();
            break;
        case 'backup_check_fingerprint':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->checkFingerprint();
            break;
        case 'backup_download':
            require_once 'core/controllers/BackupController.php';
            (new BackupController())->download();
            break;

        // --- Notifikasi AJAX ---
        case 'notifikasi_poll':
            require_once 'core/controllers/NotifikasiController.php';
            (new NotifikasiController())->poll();
            break;
        case 'notifikasi_read':
            require_once 'core/controllers/NotifikasiController.php';
            (new NotifikasiController())->read();
            break;
        case 'notifikasi_read_all':
            require_once 'core/controllers/NotifikasiController.php';
            (new NotifikasiController())->readAll();
            break;
        case 'notifikasi_delete_all':
            require_once 'core/controllers/NotifikasiController.php';
            (new NotifikasiController())->deleteAll();
            break;
        case 'notifikasi_delete':
            require_once 'core/controllers/NotifikasiController.php';
            (new NotifikasiController())->delete();
            break;

        // --- Chart API ---
        case 'get_chart_activity':
            require_once 'core/models/Ukm.php';
            require_once 'core/models/Kehadiran.php';
            $ukmModel = new Ukm();
            $activeUkms = $ukmModel->getActive();
            $kehadiranModel = new Kehadiran();
            $period = $_GET['period'] ?? 'month';
            $chartLabels = [];
            $chartDataObj = [];
            foreach ($activeUkms as $u) {
                $chartLabels[] = $u['singkatan'] ?? $u['nama'];
                $chartDataObj[] = $kehadiranModel->getAttendanceRateByPeriod($u['id'], $period);
            }
            header('Content-Type: application/json');
            echo json_encode(['labels' => $chartLabels, 'dataRates' => $chartDataObj]);
            exit;

        // --- Sistem Surat & Inventaris ---
        case 'arsip_surat_store':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->store();
            break;
        case 'arsip_surat_update':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->update();
            break;
        case 'arsip_surat_delete':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->delete();
            break;
        case 'arsip_surat_update_tanggal':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->updateTanggal();
            break;
        case 'arsip_surat_duplicate':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->duplicate();
            break;
        case 'arsip_surat_export':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->export();
            break;
        case 'arsip_surat_save_kop':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->saveKop();
            break;
        case 'surat_template_store':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->storeTemplate();
            break;
        case 'surat_template_delete':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->deleteTemplate();
            break;
        case 'panitia_tetap_save':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->savePanitia();
            break;
        case 'panitia_tetap_delete':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->deletePanitia();
            break;
        case 'surat_global_save':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->saveGlobalSurat();
            break;
        case 'barang_store':
            require_once 'core/controllers/BarangController.php';
            (new BarangController())->store();
            break;
        case 'barang_update':
            require_once 'core/controllers/BarangController.php';
            (new BarangController())->update();
            break;
        case 'barang_delete':
            require_once 'core/controllers/BarangController.php';
            (new BarangController())->delete();
            break;
        case 'lampiran_pinjam_store':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->storeLampiran();
            break;
        case 'lampiran_pinjam_delete':
            require_once 'core/controllers/SuratController.php';
            (new SuratController())->deleteLampiran();
            break;

        default:
            redirect('index.php?page=home');
    }
    exit; // Action handlers always redirect, this is a safety net
}

// ===== Page Router (GET page rendering) =====
$page = $_GET['page'] ?? 'home';

// --- Helper: Load data untuk admin pages ---
function loadAdminData(string $modelFile, string $className, ?int $ukmId = null): array
{
    require_once "core/models/{$modelFile}";
    $model = new $className();
    return method_exists($model, 'getAll') 
        ? $model->getAll($ukmId) 
        : [];
}

switch ($page) {
    // ==========================================
    // PUBLIC PAGES (tanpa login)
    // ==========================================
    case 'home':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Berita.php';
        require_once 'core/models/Kehadiran.php';
        
        $ukmModel = new Ukm();
        $activeUkms = $ukmModel->getActive();
        $kehadiranModel = new Kehadiran();
        
        $chartLabels = [];
        $chartDataObj = [];
        
        foreach ($activeUkms as $u) {
            $chartLabels[] = $u['singkatan'] ?? $u['nama'];
            $chartDataObj[] = $kehadiranModel->getAttendanceRateByPeriod($u['id'], 'month');
        }

        View::renderPublic('public/home', [
            'title'      => $APP_NAME . ' - Sistem Absensi IoT',
            'ukmList'    => $activeUkms,
            'beritaList' => (new Berita())->getPublished(),
            'chartLabels'=> json_encode($chartLabels),
            'chartData'  => json_encode($chartDataObj)
        ]);
        break;
        
    case 'katalog_ukm':
        require_once 'core/models/Ukm.php';
        $ukmModel = new Ukm();
        View::renderPublic('public/katalog_ukm', [
            'title'    => 'Semua ' . $ENTITY . ' / Kelas - Sistem Absensi IoT',
            'ukmList'  => $ukmModel->getActive(),
        ]);
        break;

    case 'detail_ukm':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Berita.php';
        require_once 'core/models/Anggota.php';
        $ukmModel = new Ukm();
        $ukmId    = (int)($_GET['id'] ?? 0);
        $ukmData  = $ukmModel->getById($ukmId);
        
        // Prevent access to non-active UKMs via public detail page
        if (!$ukmData || $ukmData['status'] !== 'aktif') {
            redirect('index.php?page=katalog_ukm');
        }

        View::renderPublic('public/detail_ukm', [
            'title'        => htmlspecialchars($ukmData['nama']) . ' - Sistem Absensi IoT',
            'ukm'          => $ukmData,
            'beritaList'   => (new Berita())->getPublished($ukmId),
            'totalAnggota' => (new Anggota())->count($ukmId),
        ]);
        break;
        
    case 'statistik_ukm':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Anggota.php';
        require_once 'core/models/Event.php';
        require_once 'core/models/Kehadiran.php';
        $ukmId = (int)($_GET['ukm_id'] ?? 0);
        $filterEventId = (int)($_GET['event_id'] ?? 0);
        $ukmModel = new Ukm();
        $kehadiranModel = new Kehadiran();
        
        $viewData = [
            'title'          => 'Statistik ' . $ENTITY . ' - Sistem Absensi IoT',
            'ukm'            => $ukmModel->getById($ukmId),
            'anggotaList'    => (new Anggota())->getAll($ukmId),
            'eventList'      => (new Event())->getAll($ukmId),
            'totalAnggota'   => (new Anggota())->count($ukmId),
            'totalEvent'     => (new Event())->count($ukmId),
            'eventStats'     => $kehadiranModel->getStatsByUkm($ukmId),
            'totalKehadiran' => $kehadiranModel->countByUkm($ukmId),
            'rataKehadiran'  => $kehadiranModel->getAttendanceRate($ukmId),
        ];
        
        // Jika filter event_id aktif, load daftar kehadiran per event
        if ($filterEventId > 0) {
            $viewData['kehadiranList'] = $kehadiranModel->getByEvent($filterEventId);
        }
        
        View::renderPublic('public/statistik_ukm', $viewData);
        break;

    case 'kepengurusan_ukm':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Anggota.php';
        require_once 'core/models/Periode.php';
        $ukmId = (int)($_GET['ukm_id'] ?? 0);
        $periodeId = (int)($_GET['periode_id'] ?? 0);
        $periodeModel = new Periode();
        if ($periodeId === 0) {
            $activePeriode = $periodeModel->getActive($ukmId);
            $periodeId = $activePeriode ? $activePeriode['id'] : 0;
        }
        $targetPeriodeObj = $periodeModel->getById($periodeId);

        View::renderPublic('public/kepengurusan_ukm', [
            'title'        => 'Kepengurusan ' . $ENTITY . ' - Sistem Absensi IoT',
            'ukm'          => (new Ukm())->getById($ukmId),
            'anggotaList'  => (new Anggota())->getAll($ukmId, $periodeId),
            'periode'      => $targetPeriodeObj
        ]);
        break;

    case 'arsip_ukm':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Periode.php';
        $ukmId = (int)($_GET['ukm_id'] ?? 0);
        View::renderPublic('public/arsip_ukm', [
            'title'        => 'Arsip Kepengurusan ' . $ENTITY . ' - Sistem Absensi IoT',
            'ukm'          => (new Ukm())->getById($ukmId),
            'periodeList'  => (new Periode())->getAll($ukmId),
        ]);
        break;

    case 'berita_ukm':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Berita.php';
        require_once 'core/models/Periode.php';
        $ukmId = (int)($_GET['ukm_id'] ?? 0);
        $periodeId = (int)($_GET['periode_id'] ?? 0);
        
        $periodeModel = new Periode();
        if ($periodeId === 0) {
            $activePeriode = $periodeModel->getActive($ukmId);
            $periodeId = $activePeriode ? $activePeriode['id'] : 0;
        }

        View::renderPublic('public/berita_ukm', [
            'title'            => 'Berita ' . $ENTITY . ' - Sistem Absensi IoT',
            'ukm'              => (new Ukm())->getById($ukmId),
            'beritaList'       => (new Berita())->getPublished($ukmId, $periodeId),
            'periodeList'      => $periodeModel->getAll($ukmId),
            'currentPeriodeId' => $periodeId,
        ]);
        break;

    case 'kontak_ukm':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Pengaturan.php';
        $ukmId = (int)($_GET['ukm_id'] ?? 0);
        // Convert pengaturan rows ke key=>nilai map untuk akses mudah di view
        $settingsRaw = (new Pengaturan())->getAll($ukmId);
        $settingsMap = [];
        foreach ($settingsRaw as $row) { $settingsMap[$row['kunci']] = $row['nilai']; }
        View::renderPublic('public/kontak_ukm', [
            'title'    => 'Hubungi Kami - Sistem Absensi IoT',
            'ukm'      => (new Ukm())->getById($ukmId),
            'settings' => $settingsMap,
        ]);
        break;


    case 'detail_berita':
        require_once 'core/models/Berita.php';
        $id = (int)($_GET['id'] ?? 0);
        $berita = (new Berita())->getById($id);
        View::renderPublic('public/detail_berita', [
            'title'  => ($berita ? htmlspecialchars($berita['judul']) : 'Berita') . ' - Sistem Absensi IoT',
            'berita' => $berita,
        ]);
        break;

    case 'daftar_anggota':
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Pendaftaran.php';
        require_once 'core/models/Pengaturan.php';
        require_once 'core/models/Periode.php';
        
        $ukmModel = new Ukm();
        $pendaftaranModel = new Pendaftaran();
        $pengaturanModel = new Pengaturan();
        $periodeModel = new Periode();
        
        $targetUkmId = (int)($_GET['ukm_id'] ?? 0);
        
        $riwayatPendaftaran = null;
        $settingsMap = [];
        $hasPeriodeAktif = true; // default: anggap ada, supaya tidak tampil warning kalau UKM belum dipilih
        if ($targetUkmId > 0) {
            $riwayatPendaftaran = $pendaftaranModel->getLatestBySession(session_id(), $targetUkmId);
            
            if ($riwayatPendaftaran && in_array($riwayatPendaftaran['status'], ['diterima', 'ditolak'])) {
                $pendaftaranModel->clearSessionLink($riwayatPendaftaran['id']);
            }
            
            $settingsRaw = $pengaturanModel->getAll($targetUkmId);
            foreach ($settingsRaw as $row) {
                $settingsMap[$row['kunci']] = $row['nilai'];
            }

            // Cek apakah UKM punya periode aktif
            $activePeriode = $periodeModel->getActive($targetUkmId);
            $hasPeriodeAktif = (bool) $activePeriode;
        }

        View::renderPublic('public/daftar_anggota', [
            'title'              => 'Pendaftaran Anggota - Sistem Absensi IoT',
            'ukmList'            => $ukmModel->getActive(),
            'targetUkmId'        => $targetUkmId,
            'riwayatPendaftaran' => $riwayatPendaftaran,
            'settings'           => $settingsMap,
            'hasPeriodeAktif'    => $hasPeriodeAktif,
        ]);
        break;

    case 'daftar_sukses':
        View::renderPublic('public/daftar_sukses', [
            'title' => 'Pendaftaran Berhasil - Sistem Absensi IoT'
        ]);
        break;

    case 'tentang':
        require_once 'core/Database.php';
        require_once 'core/models/PengaturanUmum.php';
        require_once 'core/models/Admin.php';
        require_once 'core/models/Anggota.php';
        
        $db = Database::getConnection();
        $settings = (new PengaturanUmum())->getAll();
        
        // Ambil superadmin sebagai "Tim Pengembang"
        $teams = (new AdminModel())->getByRole('superadmin');
        
        // Hitung statistik real
        $totalAnggota = (new Anggota())->count();
        $totalKehadiran = (int)$db->query("SELECT COUNT(*) FROM absensi")->fetchColumn();

        View::renderPublic('public/tentang', [
            'title'    => 'Tentang Kami - ' . htmlspecialchars($settings['app_name'] ?? 'Sistem Absensi IoT'),
            'settings' => $settings,
            'teams'    => $teams,
            'stats'    => [
                'total_anggota'   => $totalAnggota,
                'total_kehadiran' => $totalKehadiran
            ]
        ]);
        break;

    // ==========================================
    // AUTH PAGES
    // ==========================================
    case 'login':
        View::renderAuth('auth/login', [
            'title' => 'Login - Sistem Absensi IoT'
        ]);
        break;
        
    case 'verify_2fa':
        Session::requirePending();
        View::renderAuth('auth/verify_2fa', [
            'title' => 'Verifikasi OTP - Sistem Absensi IoT'
        ]);
        break;

    // ==========================================
    // ADMIN PAGES (memerlukan login)
    // ==========================================
    case 'dashboard':
        Session::requireLogin();
        $role  = Session::get('admin_role');
        $ukmId = Session::get('ukm_id');
        
        // Fitur Peeking untuk Superadmin
        $isPeeking = false;
        if ($role === 'superadmin' && isset($_GET['ukm_id'])) {
            $ukmId = (int)$_GET['ukm_id'];
            $viewFile = 'admin/dashboard_admin';
            $isPeeking = true;
        } else {
            $viewFile = ($role === 'superadmin') ? 'admin/dashboard_superadmin' : 'admin/dashboard_admin';
        }
        
        // Load summary data
        require_once 'core/models/Anggota.php';
        require_once 'core/models/Event.php';
        require_once 'core/models/Berita.php';
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Kehadiran.php';
        
        $eventModel = new Event();
        $kehadiranModel = new Kehadiran();
        
        // Data untuk Grafik Admin UKM (Trend 7 Event Terakhir)
        $attendanceStats = [];
        if (($role === 'admin' || $isPeeking) && $ukmId) {
            $rawStats = $kehadiranModel->getStatsByUkm($ukmId);
            $attendanceStats = array_reverse(array_slice($rawStats, 0, 7)); // Ambil 7 terbaru, urutkan dari lama ke baru
        }
        
        // Data untuk Grafik Superadmin (Perbandingan UKM)
        $globalUkmStats = [];
        if ($role === 'superadmin') {
            $globalUkmStats = $kehadiranModel->getGlobalUkmStats();
        }

        View::renderAdmin($viewFile, [
            'title'           => 'Dashboard - Sistem Absensi IoT',
            'totalAnggota'    => (new Anggota())->count($ukmId),
            'totalEvent'      => $eventModel->count($ukmId),
            'totalBerita'     => (new Berita())->count($ukmId),
            'totalUkm'        => (new Ukm())->count(),
            'eventList'       => $eventModel->getAll($ukmId),
            'anggotaList'     => (new Anggota())->getAll($ukmId),
            'ukmList'         => (new Ukm())->getAll(),
            'ukm'             => $ukmId ? (new Ukm())->getById($ukmId) : null,
            'attendanceStats' => $attendanceStats,
            'globalUkmStats'  => $globalUkmStats,
            'isPeeking'       => $isPeeking,
            'todayKehadiran'  => $ukmId ? $kehadiranModel->countTodayByUkm($ukmId) : 0,
            'activeEvent'     => $ukmId ? $eventModel->getActiveNow($ukmId) : null,
        ]);
        break;

    case 'profil':
        Session::requireLogin();
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Anggota.php';
        require_once 'core/models/Pengaturan.php';

        $role = Session::get('admin_role');

        if ($role === 'superadmin') {
            // Super admin: ambil ukm_id dari GET → lalu dari session (last_ukm_id) → redirect jika tidak ada
            $ukmId = (int)($_GET['ukm_id'] ?? Session::get('last_ukm_id') ?? 0);
            if (!$ukmId) {
                setFlash('info', 'Pilih ' . $ENTITY . ' yang ingin dikonfigurasi terlebih dahulu.');
                redirect('index.php?page=ukm');
            }
            // Simpan sebagai last_ukm_id agar diingat next kunjungan
            Session::set('last_ukm_id', $ukmId);
        } else {
            // Admin biasa: hanya bisa akses UKM miliknya sendiri
            $ukmId = (int)Session::get('ukm_id');
        }

        // Convert pengaturan ke key=>nilai map
        $settingsRaw = (new Pengaturan())->getAll($ukmId);
        $settingsMap = [];
        foreach ($settingsRaw as $row) { $settingsMap[$row['kunci']] = $row['nilai']; }

        View::renderAdmin('admin/profil', [
            'title'        => 'Profil ' . $ENTITY . ' | OmniPresence IoT',
            'ukm'          => (new Ukm())->getById($ukmId),
            'totalAnggota' => (new Anggota())->count($ukmId),
            'settings'     => $settingsMap,
        ]);
        break;


    case 'anggota':
        Session::requireLogin();
        require_once 'core/models/Anggota.php';
        require_once 'core/models/Ukm.php';
        $isSuperAdmin = Session::get('admin_role') === 'superadmin';

        // For superadmin: support filter by ukm_id via GET param
        $filterUkmId = $_GET['ukm_id'] ?? $_GET['filter_ukm_id'] ?? Session::get('anggota_last_ukm_id') ?? '';
        if ($isSuperAdmin && is_numeric($filterUkmId) && $filterUkmId > 0) {
            $fetchUkmId  = (int)$filterUkmId;
            $fetchPeriode = null;
            Session::set('anggota_last_ukm_id', $fetchUkmId);
        } elseif ($isSuperAdmin) {
            $fetchUkmId  = null;
            $fetchPeriode = null;
        } else {
            $fetchUkmId  = (int)Session::get('ukm_id');
            $fetchPeriode = (int)Session::get('periode_id');
        }

        View::renderAdmin('admin/anggota', [
            'title'             => 'Halaman Anggota - The Ledger',
            'anggotaList'       => (new Anggota())->getAll($fetchUkmId, $fetchPeriode),
            'ukmList'           => $isSuperAdmin ? (new Ukm())->getAll() : [],
            'selectedFilterUkm' => $filterUkmId,
        ]);
        break;

    case 'berita':
        Session::requireLogin();
        require_once 'core/models/Berita.php';
        require_once 'core/models/Ukm.php';
        $isSuperAdmin = Session::get('admin_role') === 'superadmin';
        
        $filterUkmId = $_GET['filter_ukm_id'] ?? '';
        if ($isSuperAdmin && is_numeric($filterUkmId)) {
            $fetchUkmId = (int)$filterUkmId;
        } else {
            $fetchUkmId = $isSuperAdmin ? null : (int)Session::get('ukm_id');
        }
        $periodeId = $isSuperAdmin ? null : (int)Session::get('periode_id');

        View::renderAdmin('admin/berita', [
            'title'      => 'Manajemen Berita - Sistem Absensi IoT',
            'beritaList' => (new Berita())->getAll($fetchUkmId, $periodeId),
            'ukmList'    => $isSuperAdmin ? (new Ukm())->getAll() : []
        ]);
        break;

    case 'event':
        Session::requireLogin();
        require_once 'core/models/Event.php';
        require_once 'core/models/Ukm.php';
        $isSuperAdmin = Session::get('admin_role') === 'superadmin';
        
        $filterUkmId = $_GET['filter_ukm_id'] ?? '';
        if ($isSuperAdmin && is_numeric($filterUkmId)) {
            $ukmId = (int)$filterUkmId;
        } else {
            $ukmId = $isSuperAdmin ? null : (int)Session::get('ukm_id');
        }
        
        $eventModel = new Event();
        $eventModel->generateRoutineEvents(); // Trigger JIT Generator
        
        View::renderAdmin('admin/event', [
            'title'     => 'Kegiatan Absensi - Sistem Absensi IoT',
            'eventList' => $eventModel->getWithAttendanceStats($ukmId),
            'ukmList'   => $isSuperAdmin ? (new Ukm())->getAll() : [],
        ]);
        break;

    case 'kelola_periode':
        Session::requireSuperAdmin();
        require_once 'core/models/Periode.php';
        require_once 'core/models/Ukm.php';
        
        $ukmId = (int)($_GET['ukm_id'] ?? 0);
        if (!$ukmId) {
            setFlash('info', 'Pilih ' . $ENTITY . ' terlebih dahulu.');
            redirect('index.php?page=ukm');
        }

        View::renderAdmin('admin/kelola_periode', [
            'title'       => 'Kelola Periode ' . $ENTITY . ' - Super Admin',
            'ukm'         => (new Ukm())->getById($ukmId),
            'periodeList' => (new Periode())->getAll($ukmId),
        ]);
        break;

    case 'pengaturan':
        Session::requireLogin();
        $adminId = Session::get('admin_id');
        require_once 'core/models/Admin.php';
        require_once 'core/models/AdminSession.php';
        
        $sessionModel = new AdminSessionModel();
        $activeSessions = $sessionModel->getSessionsByAdminId((int)$adminId);
        
        View::renderAdmin('admin/pengaturan', [
            'title'   => 'Pengaturan - Sistem Absensi IoT',
            'admin'   => (new AdminModel())->getById((int)$adminId),
            'sessions'=> $activeSessions,
            'current_session_id' => session_id()
        ]);
        break;

    case 'log_keamanan':
        Session::requireSuperAdmin();
        require_once 'core/models/LogKeamanan.php';
        
        $limit     = 50;
        $pageNo    = max(1, (int)($_GET['p'] ?? 1));
        $offset    = ($pageNo - 1) * $limit;
        
        $search    = $_GET['search'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate   = $_GET['end_date'] ?? '';

        $logModel  = new LogKeamananModel();
        $logs      = $logModel->getAllPaginated($limit, $offset, $search, $startDate, $endDate);
        $totalRows = $logModel->countTotal($search, $startDate, $endDate);
        $totalPages= ceil($totalRows / $limit);

        View::renderAdmin('admin/log_keamanan', [
            'title'      => 'Log Aktivitas Keamanan',
            'logs'       => $logs,
            'pageNo'     => $pageNo,
            'totalPages' => $totalPages,
            'search'     => $search,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
        break;

    case 'backup':
    case 'backup_database':
        require_once 'core/controllers/BackupController.php';
        (new BackupController())->index();
        break;

    case 'tambah_event':
        Session::requireLogin();
        require_once 'core/models/Ukm.php';
        $ukmList = Session::get('admin_role') === 'superadmin' ? (new Ukm())->getAll() : [];
        View::renderAdmin('admin/tambah_event', [
            'title'   => 'Tambah Kegiatan',
            'ukmList' => $ukmList,
        ]);
        break;

    case 'edit_event':
        Session::requireLogin();
        require_once 'core/models/Event.php';
        require_once 'core/models/Ukm.php';
        $id = (int)($_GET['id'] ?? 0);
        $ukmList = Session::get('admin_role') === 'superadmin' ? (new Ukm())->getAll() : [];
        View::renderAdmin('admin/edit_event', [
            'title'   => 'Edit Kegiatan',
            'event'   => (new Event())->getById($id),
            'ukmList' => $ukmList,
        ]);
        break;

    case 'detail_event':
        Session::requireLogin();
        require_once 'core/models/Event.php';
        require_once 'core/models/Kehadiran.php';
        require_once 'core/models/Anggota.php';
        $id = (int)($_GET['id'] ?? 0);
        $kehadiranModel = new Kehadiran();
        $eventData = (new Event())->getById($id);
        View::renderAdmin('admin/detail_event', [
            'title'          => 'Detail Kehadiran Kegiatan',
            'event'          => $eventData,
            'kehadiranList'  => $kehadiranModel->getByEvent($id),
            'stats'          => $kehadiranModel->getStatsByEvent($id),
            'anggotaList'    => $eventData ? (new Anggota())->getActive($eventData['ukm_id']) : [],
        ]);
        break;

    case 'tambah_kepengurusan':
        Session::requireLogin();
        View::renderAdmin('admin/tambah_kepengurusan', ['title' => 'Tambah Kepengurusan']);
        break;

    case 'edit_kepengurusan':
        Session::requireLogin();
        require_once 'core/models/Kepengurusan.php';
        $id = (int)($_GET['id'] ?? 0);
        View::renderAdmin('admin/edit_kepengurusan', [
            'title'        => 'Edit Kepengurusan',
            'kepengurusan' => (new Kepengurusan())->getById($id),
        ]);
        break;

    case 'tambah_anggota':
        Session::requireLogin();
        require_once 'core/models/Ukm.php';
        require_once 'core/models/JabatanKustom.php';
        $isSuperAdmin = Session::get('admin_role') === 'superadmin';
        $ukmList = $isSuperAdmin ? (new Ukm())->getAll() : [];
        // Untuk admin biasa, preload jabatan kustom UKM-nya. Superadmin: kosong dulu (diinsert via AJAX jika perlu).
        $targetUkmForJabatan = $isSuperAdmin ? (int)($_GET['ukm_id'] ?? 0) : (int)Session::get('ukm_id');
        $jabatanKustom = $targetUkmForJabatan ? (new JabatanKustom())->getByUkm($targetUkmForJabatan) : [];
        View::renderAdmin('admin/tambah_anggota', [
            'title'         => 'Tambah Anggota',
            'ukmList'       => $ukmList,
            'jabatanKustom' => $jabatanKustom,
        ]);
        break;

    case 'edit_anggota':
        Session::requireLogin();
        require_once 'core/models/Anggota.php';
        require_once 'core/models/Ukm.php';
        require_once 'core/models/JabatanKustom.php';
        $id = (int)($_GET['id'] ?? 0);
        $anggota = (new Anggota())->getById($id);
        $isSuperAdmin = Session::get('admin_role') === 'superadmin';
        $ukmList = $isSuperAdmin ? (new Ukm())->getAll() : [];
        // Load jabatan kustom untuk UKM anggota ini
        $jkUkmId = $anggota ? (int)$anggota['ukm_id'] : (int)Session::get('ukm_id');
        $jabatanKustom = $jkUkmId ? (new JabatanKustom())->getByUkm($jkUkmId) : [];
        View::renderAdmin('admin/edit_anggota', [
            'title'         => 'Edit Anggota',
            'anggota'       => $anggota,
            'ukmList'       => $ukmList,
            'jabatanKustom' => $jabatanKustom,
        ]);
        break;

    case 'tambah_berita':
        Session::requireLogin();
        require_once 'core/models/Ukm.php';
        $ukmList = Session::get('admin_role') === 'superadmin' ? (new Ukm())->getAll() : [];
        View::renderAdmin('admin/tambah_berita', [
            'title'   => 'Buat Berita Baru',
            'ukmList' => $ukmList
        ]);
        break;

    case 'edit_berita':
        Session::requireLogin();
        require_once 'core/models/Berita.php';
        require_once 'core/models/Ukm.php';
        $id = (int)($_GET['id'] ?? 0);
        $ukmList = Session::get('admin_role') === 'superadmin' ? (new Ukm())->getAll() : [];
        View::renderAdmin('admin/edit_berita', [
            'title'   => 'Edit Berita',
            'berita'  => (new Berita())->getById($id),
            'ukmList' => $ukmList
        ]);
        break;

    case 'tambah_admin':
        Session::requireSuperAdmin();
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Periode.php';
        $allUkm = (new Ukm())->getAll();
        $periodeModel = new Periode();
        $periodeMap = [];
        foreach ($allUkm as $u) {
            $periodeMap[$u['id']] = $periodeModel->getAll($u['id']);
        }
        View::renderAdmin('admin/tambah_admin', [
            'title'      => 'Tambah Admin',
            'ukmList'    => $allUkm,
            'periodeMap' => $periodeMap,
        ]);
        break;

    case 'edit_admin':
        Session::requireSuperAdmin();
        require_once 'core/models/Admin.php';
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Periode.php';
        $id = (int)($_GET['id'] ?? 0);
        $allUkm = (new Ukm())->getAll();
        $periodeModel = new Periode();
        $periodeMap = [];
        foreach ($allUkm as $u) {
            $periodeMap[$u['id']] = $periodeModel->getAll($u['id']);
        }
        View::renderAdmin('admin/edit_admin', [
            'title'      => 'Edit Admin',
            'admin'      => (new AdminModel())->getById($id),
            'ukmList'    => $allUkm,
            'periodeMap' => $periodeMap,
        ]);
        break;

    case 'kelola_admin':
        Session::requireSuperAdmin();
        require_once 'core/models/Admin.php';
        View::renderAdmin('admin/kelola_admin', [
            'title'     => 'Kelola Admin - ' . $APP_NAME,
            'adminList' => (new AdminModel())->getAll(),
        ]);
        break;

    case 'verifikasi_pendaftar':
        Session::requireLogin();
        require_once 'core/models/Pendaftaran.php';
        require_once 'core/models/Pengaturan.php';
        $ukmId = Session::get('admin_role') === 'superadmin' ? null : (int)Session::get('ukm_id');
        $periodeId = Session::get('admin_role') === 'superadmin' ? null : (int)Session::get('periode_id');
        
        $pendaftaranList = (new Pendaftaran())->getAll($ukmId, $periodeId);
        
        $pendaftaranModel = new Pendaftaran();
        foreach ($pendaftaranList as &$p) {
            $p['answers'] = $pendaftaranModel->getAnswers($p['id']);
        }
        unset($p);

        View::renderAdmin('admin/verifikasi_pendaftar', [
            'title'           => 'Verifikasi Pendaftar',
            'pendaftaranList' => $pendaftaranList
        ]);
        break;

    case 'pendaftaran':
        Session::requireLogin();
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Pengaturan.php';

        $role = Session::get('admin_role');
        $ukmList = [];
        $settingsMap = [];
        
        if ($role === 'superadmin') {
            $ukmList = (new Ukm())->getAll();
            $ukmId = (int)($_GET['ukm_id'] ?? Session::get('pendaftaran_last_ukm_id') ?? 0);
            
            // Simpan pendaftaran_last_ukm_id di sesi
            if ($ukmId > 0) {
                Session::set('pendaftaran_last_ukm_id', $ukmId);
            }
        } else {
            $ukmId = (int)Session::get('ukm_id');
        }

        if ($ukmId > 0) {
            $settingsRaw = (new Pengaturan())->getAll($ukmId);
            foreach ($settingsRaw as $row) { $settingsMap[$row['kunci']] = $row['nilai']; }
        }

        View::renderAdmin('admin/pendaftaran', [
            'title'       => 'Konfigurasi Pendaftaran',
            'ukmList'     => $ukmList,
            'ukmId'       => $ukmId,
            'settings'    => $settingsMap,
            'role'        => $role
        ]);
        break;

    case 'tambah_ukm':
        Session::requireSuperAdmin();
        View::renderAdmin('admin/tambah_ukm', ['title' => 'Daftarkan ' . $ENTITY . ' Baru']);
        break;

    case 'ukm':
        Session::requireSuperAdmin();
        require_once 'core/models/Ukm.php';
        View::renderAdmin('admin/ukm', [
            'title'   => 'Semua ' . $ENTITY . ' - ' . $APP_NAME,
            'ukmList' => (new Ukm())->getAll(),
        ]);
        break;

    case 'jabatan':
        Session::requireLogin();
        require_once 'core/models/JabatanKustom.php';
        require_once 'core/models/Ukm.php';
        $isSuperAdmin = Session::get('admin_role') === 'superadmin';

        if ($isSuperAdmin) {
            $selectedUkmId = (int)($_GET['ukm_id'] ?? Session::get('jabatan_last_ukm_id') ?? 0);
            if ($selectedUkmId) Session::set('jabatan_last_ukm_id', $selectedUkmId);
            $ukm = $selectedUkmId ? (new Ukm())->getById($selectedUkmId) : null;
            $jabatanList = $selectedUkmId ? (new JabatanKustom())->getByUkm($selectedUkmId) : [];
            $ukmListForDropdown = (new Ukm())->getAll();
        } else {
            $selectedUkmId = (int)Session::get('ukm_id');
            $ukm = (new Ukm())->getById($selectedUkmId);
            $jabatanList = (new JabatanKustom())->getByUkm($selectedUkmId);
            $ukmListForDropdown = [];
        }

        View::renderAdmin('admin/jabatan', [
            'title'         => 'Kelola Jabatan - ' . ($ukm['nama'] ?? $ENTITY),
            'ukm'           => $ukm,
            'jabatanList'   => $jabatanList,
            'ukmList'       => $ukmListForDropdown,
            'selectedUkmId' => $selectedUkmId,
        ]);
        break;

    case 'konfigurasi_umum':
        Session::requireSuperAdmin();
        require_once 'core/models/PengaturanUmum.php';
        $settingsUmum = (new PengaturanUmum())->getAll();
        View::renderAdmin('admin/konfigurasi_umum', [
            'title'    => 'Konfigurasi Umum - Super Admin',
            'settings' => $settingsUmum,
        ]);
        break;

    // --- Sistem Surat & Inventaris ---
    case 'arsip_surat':
    case 'buat_surat':
    case 'arsip_manual':
    case 'pengaturan_surat':
    case 'master_barang':
    case 'cetak_lampiran':
    case 'arsip_lampiran':
        Session::requireLogin();
        require_once 'core/models/Surat.php';
        require_once 'core/models/Ukm.php';
        require_once 'core/models/Periode.php';
        require_once 'core/models/Barang.php';
        require_once 'core/models/Pengaturan.php';

        $role = Session::get('admin_role');
        
        // Resolve ukm_id
        if ($role === 'superadmin') {
            $ukm_id = (int)($_GET['ukm_id'] ?? Session::get('last_ukm_id') ?? 0);
            if ($ukm_id > 0) {
                if ($ukm_id !== (int)Session::get('last_ukm_id')) {
                    Session::set('periode_id', 0);
                }
                Session::set('last_ukm_id', $ukm_id);
            }
        } else {
            $ukm_id = (int)Session::get('ukm_id');
        }

        // Resolve periode_id
        if (isset($_GET['periode_id'])) {
            $periode_id = (int)$_GET['periode_id'];
            Session::set('periode_id', $periode_id);
        } else {
            $periode_id = (int)Session::get('periode_id');
        }

        if ($periode_id === 0) {
            // Coba ambil periode aktif UKM tersebut
            $activePeriode = (new Periode())->getActive($ukm_id);
            $periode_id = $activePeriode ? (int)$activePeriode['id'] : 0;
            
            if ($periode_id > 0) {
                Session::set('periode_id', $periode_id);
            }

            if ($ukm_id > 0 && $periode_id === 0 && $page !== 'master_barang') {
                setFlash('error', 'Organisasi ini belum memiliki periode aktif. Silakan buat periode terlebih dahulu.');
                redirect('index.php?page=periode&ukm_id=' . $ukm_id);
            }
        }

        $suratModel = new SuratModel();
        $barangModel = new BarangModel();
        $ukmModel = new Ukm();
        $pengaturanModel = new Pengaturan();
        
        $ukm = $ukmModel->getById($ukm_id);
        $kop_surat = $pengaturanModel->get($ukm_id, 'kop_surat');
        $isSuperAdmin = ($role === 'superadmin');
        
        $activePeriode = (new Periode())->getActive($ukm_id);
        $activePeriodeId = $activePeriode ? (int)$activePeriode['id'] : 0;
        
        $semua_periode = (new Periode())->getAll($ukm_id);
        $can_edit = $isSuperAdmin || ($periode_id === $activePeriodeId);

        $viewData = [
            'ukm'              => $ukm,
            'ukm_id'           => $ukm_id,
            'periode_id'       => $periode_id,
            'active_periode_id'=> $activePeriodeId,
            'semua_periode'    => $semua_periode,
            'can_edit'         => $can_edit,
            'isSuperAdmin'     => $isSuperAdmin,
            'kop_surat'        => $kop_surat
        ];

        if ($page === 'arsip_surat') {
            $jenis = $_GET['jenis'] ?? 'L';
            View::renderAdmin('admin/surat/arsip', array_merge($viewData, [
                'title'      => 'Arsip Surat',
                'suratList'  => $suratModel->getAllArsip($ukm_id, $periode_id, $jenis),
                'jenis'      => $jenis,
                'latest_id'  => $suratModel->getMaxId($ukm_id, $periode_id, $jenis)
            ]));
        } elseif ($page === 'buat_surat') {
            if (!$can_edit) {
                setFlash('error', 'Anda tidak dapat menambah/mengubah surat pada periode riwayat.');
                redirect('index.php?page=arsip_surat&ukm_id=' . $ukm_id);
            }
            $next_urut_L = str_pad($suratModel->getMaxSequence($ukm_id, $periode_id, 'L') + 1, 3, '0', STR_PAD_LEFT);
            $next_urut_D = str_pad($suratModel->getMaxSequence($ukm_id, $periode_id, 'D') + 1, 3, '0', STR_PAD_LEFT);
            $jenis = $_GET['jenis'] ?? 'L';
            $next_urut = ($jenis === 'D') ? $next_urut_D : $next_urut_L;

            View::renderAdmin('admin/surat/buat', array_merge($viewData, [
                'title'      => 'Buat Surat Otomatis',
                'next_urut'  => $next_urut,
                'next_urut_L' => $next_urut_L,
                'next_urut_D' => $next_urut_D,
                'templates'  => $suratModel->getTemplates($ukm_id, $periode_id),
                'panitia'    => $suratModel->getPanitia($ukm_id, $periode_id),
                'edit_data'  => isset($_GET['edit']) ? $suratModel->getArsipById((int)$_GET['edit'], $ukm_id) : null,
                'clone_data' => isset($_GET['clone']) ? $suratModel->getArsipById((int)$_GET['clone'], $ukm_id) : null,
                'lampiran_internal_list' => $suratModel->getArsipLampiran($ukm_id, $periode_id)
            ]));
        } elseif ($page === 'arsip_manual') {
            if (!$can_edit) {
                setFlash('error', 'Anda tidak dapat menambah/mengubah surat pada periode riwayat.');
                redirect('index.php?page=arsip_surat&ukm_id=' . $ukm_id);
            }
            View::renderAdmin('admin/surat/manual', array_merge($viewData, [
                'title'      => 'Catat Surat Manual',
                'edit_data'  => isset($_GET['edit']) ? $suratModel->getArsipById((int)$_GET['edit'], $ukm_id) : null,
                'type'       => $_GET['type'] ?? 'M'
            ]));
        } elseif ($page === 'pengaturan_surat') {
            $settingsRaw = (new Pengaturan())->getAll($ukm_id);
            $settingsMap = [];
            foreach ($settingsRaw as $row) { $settingsMap[$row['kunci']] = $row['nilai']; }
            
            View::renderAdmin('admin/surat/pengaturan', array_merge($viewData, [
                'title'     => 'Pengaturan Surat & Panitia',
                'templates' => $suratModel->getTemplates($ukm_id, $periode_id),
                'panitia_inti' => $suratModel->getPanitia($ukm_id, $periode_id, 'inti'),
                'panitia_list' => $suratModel->getPanitia($ukm_id, $periode_id, 'panitia'),
                'settings'  => $settingsMap,
                'global_settings' => $suratModel->getAllGlobalSettings()
            ]));
        } elseif ($page === 'master_barang') {
            View::renderAdmin('admin/surat/master_barang', array_merge($viewData, [
                'title'      => 'Master Inventaris Barang',
                'items'      => $barangModel->getAll($ukm_id)
            ]));
        } elseif ($page === 'cetak_lampiran') {
            View::renderAdmin('admin/surat/cetak_lampiran', array_merge($viewData, [
                'title'      => 'Buat Lampiran Peminjaman',
                'items'      => $barangModel->getAll($ukm_id)
            ]));
        } elseif ($page === 'arsip_lampiran') {
            View::renderAdmin('admin/surat/arsip_lampiran', array_merge($viewData, [
                'title'      => 'Arsip Lampiran Peminjaman',
                'arsip'      => $suratModel->getArsipLampiran($ukm_id, $periode_id)
            ]));
        }
        break;

    // ==========================================
    // 404 - Default
    // ==========================================
    default:
        View::renderPublic('public/home', [
            'title' => $APP_NAME . ' - Sistem Absensi IoT'
        ]);
        break;
}
