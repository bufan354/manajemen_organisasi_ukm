<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Event
 * Handle CRUD operations untuk Event Absensi
 */
class EventController
{
    private Event $model;

    public function __construct()
    {
        $this->model = new Event();
    }

    /** Tambah event baru */
    public function store(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=event');
        }

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? 0)
            : (int)Session::get('ukm_id');

        $isRoutine = isset($_POST['is_routine']) && $_POST['is_routine'] == '1';
        $waktuMulai = $_POST['waktu_mulai'] ?? '';
        $waktuSelesai = $_POST['waktu_selesai'] ?? '';
        $hariRutin = null;

        if ($isRoutine) {
            // Jika rutin, input waktu adalah tipe time 'HH:MM'
            // Kita gabungkan dengan tanggal dummy
            $waktuMulai = '2000-01-01 ' . ($_POST['jam_mulai'] ?? '00:00') . ':00';
            
            $durasiRutin = (int)($_POST['durasi_rutin'] ?? 120);
            $waktuSelesai = date('Y-m-d H:i:s', strtotime($waktuMulai . " + {$durasiRutin} minutes"));
            
            if (isset($_POST['hari']) && is_array($_POST['hari'])) {
                $hariRutin = implode(',', $_POST['hari']);
            }
        } else {
            // konversi T delimiter dari datetime-local
            $waktuMulai = str_replace('T', ' ', $waktuMulai);
            if (strlen($waktuMulai) == 16) $waktuMulai .= ':00';
            
            $durasi = (int)($_POST['durasi'] ?? 120);
            $waktuSelesai = date('Y-m-d H:i:s', strtotime($waktuMulai . " + {$durasi} minutes"));
        }

        $data = [
            'ukm_id'         => $ukmId,
            'is_routine'     => $isRoutine ? 1 : 0,
            'hari_rutin'     => $hariRutin,
            'parent_id'      => null,
            'nama'           => sanitize($_POST['nama'] ?? ''),
            'deskripsi'      => sanitize($_POST['deskripsi'] ?? ''),
            'waktu_mulai'    => $waktuMulai,
            'waktu_selesai'  => $waktuSelesai,
            'lokasi'         => sanitize($_POST['lokasi'] ?? ''),
            'status_absensi' => isset($_POST['status_absensi']) ? 1 : 0,
        ];

        $this->model->create($data);
        logSecurityActivity('Tambah Kegiatan Baru', ['nama' => $data['nama'], 'waktu_mulai' => $data['waktu_mulai']]);
        
        // --- Generate Redaksi Otomatis ---
        require_once __DIR__ . '/../models/Ukm.php';
        $ukmData = (new Ukm())->getById($ukmId);
        $ukmName = $ukmData ? ($ukmData['singkatan'] ?: $ukmData['nama']) : 'UKM';
        $appName = getSetting('app_name', 'The Digital Curator');
        
        // Format Waktu yang Ramah
        if ($isRoutine) {
            $hariMap = ['1'=>'Senin', '2'=>'Selasa', '3'=>'Rabu', '4'=>'Kamis', '5'=>'Jumat', '6'=>'Sabtu', '0'=>'Minggu'];
            $hariLabels = [];
            if (isset($_POST['hari']) && is_array($_POST['hari'])) {
                foreach ($_POST['hari'] as $h) {
                    if (isset($hariMap[$h])) $hariLabels[] = $hariMap[$h];
                }
            }
            $waktuLengkap = "Setiap " . implode(' & ', $hariLabels) . " (" . ($_POST['jam_mulai'] ?? '') . " - " . ($_POST['jam_selesai'] ?? '') . " WIB)";
        } else {
            $timestamp = strtotime($waktuMulai);
            $hariInggris = date('l', $timestamp);
            $hariIndoMap = [
                'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 
                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
            ];
            $hariIndo = $hariIndoMap[$hariInggris] ?? $hariInggris;
            $waktuLengkap = $hariIndo . ", " . date('d M Y', $timestamp) . " Jam " . date('H:i', $timestamp) . " WIB";
        }

        $redaksi = "📢 [PENGUMUMAN KEGIATAN: {$ukmName}] 📢\n\n";
        $redaksi .= "Halo Rekan-rekan! 👋\n";
        $redaksi .= "Informasi agenda kegiatan {$data['nama']}:\n\n";
        $redaksi .= "📌 Nama Kegiatan: {$data['nama']}\n";
        $redaksi .= "📝 Deskripsi: {$data['deskripsi']}\n";
        $redaksi .= "📅 Waktu: {$waktuLengkap}\n";
        $redaksi .= "📍 Lokasi: {$data['lokasi']}\n\n";
        $redaksi .= "Mohon kehadirannya tepat waktu dan pastikan melakukan tapping absensi pada perangkat IoT yang tersedia di lokasi. \n\n";
        $redaksi .= "Terima kasih atas perhatiannya! 🙏";

        Session::start();
        $_SESSION['redaksi_to_copy'] = $redaksi;

        setFlash('success', 'Kegiatan berhasil ditambahkan.');
        redirect('index.php?page=event');
    }

    /** Update event */
    public function update(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=event');
        }

        $id = (int)($_POST['id'] ?? 0);
        $existing = $this->model->getById($id);
        if (!$existing) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            redirect('index.php?page=event');
        }

        // --- Security Check: Past Events ---
        $isAdmin = Session::get('admin_role') === 'admin';
        $isPast = strtotime($existing['waktu_mulai']) < time();
        $waktuMulaiInput = $_POST['waktu_mulai'] ?? '';
        
        // Cek jika mencoba mengubah tanggal kegiatan yang sudah lewat
        if ($isAdmin && $isPast) {
            $newTs = strtotime(str_replace('T', ' ', $waktuMulaiInput));
            $oldTs = strtotime($existing['waktu_mulai']);
            
            if ($newTs !== $oldTs) {
                setFlash('error', 'Keamanan: Admin tidak diperbolehkan mengubah jadwal kegiatan yang sudah lewat. Silakan hubungi Superadmin jika ada kesalahan jadwal.');
                redirect('index.php?page=event');
            }
        }
        // ----------------------------------

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? $existing['ukm_id'])
            : (int)Session::get('ukm_id');

        $isRoutine = isset($_POST['is_routine']) && $_POST['is_routine'] == '1';
        $waktuMulai = $_POST['waktu_mulai'] ?? '';
        $waktuSelesai = $_POST['waktu_selesai'] ?? '';
        $hariRutin = null;

        if ($isRoutine) {
            $waktuMulai = '2000-01-01 ' . ($_POST['jam_mulai'] ?? '00:00') . ':00';
            $durasiRutin = (int)($_POST['durasi_rutin'] ?? 120);
            $waktuSelesai = date('Y-m-d H:i:s', strtotime($waktuMulai . " + {$durasiRutin} minutes"));
            
            if (isset($_POST['hari']) && is_array($_POST['hari'])) {
                $hariRutin = implode(',', $_POST['hari']);
            }
        } else {
            $waktuMulai = str_replace('T', ' ', $waktuMulai);
            if (strlen($waktuMulai) == 16) $waktuMulai .= ':00';
            
            $durasi = (int)($_POST['durasi'] ?? 120);
            $waktuSelesai = date('Y-m-d H:i:s', strtotime($waktuMulai . " + {$durasi} minutes"));
        }

        $data = [
            'ukm_id'         => $ukmId,
            'is_routine'     => $isRoutine ? 1 : ($existing['is_routine'] ?? 0),
            'hari_rutin'     => $hariRutin,
            'nama'           => sanitize($_POST['nama'] ?? ''),
            'deskripsi'      => sanitize($_POST['deskripsi'] ?? ''),
            'waktu_mulai'    => $waktuMulai,
            'waktu_selesai'  => $waktuSelesai,
            'lokasi'         => sanitize($_POST['lokasi'] ?? ''),
            'status_absensi' => isset($_POST['status_absensi']) ? 1 : 0,
            'status'         => sanitize($_POST['status'] ?? $existing['status'] ?? 'scheduled'),
            'alasan'         => sanitize($_POST['alasan'] ?? $existing['alasan'] ?? null),
        ];

        $this->model->update($id, $data);
        logSecurityActivity('Ubah Detail Kegiatan', ['id' => $id, 'nama' => $data['nama']]);
        setFlash('success', 'Kegiatan berhasil diperbarui.');
        redirect('index.php?page=event');
    }

    /** Hapus event */
    public function delete(): void
    {
        Session::requireLogin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
            logSecurityActivity('Hapus Kegiatan', ['id' => $id]);
            setFlash('success', 'Kegiatan berhasil dihapus.');
        }
        redirect('index.php?page=event');
    }

    /** Export daftar kegiatan */
    public function export(): void
    {
        Session::requireLogin();
        $ukmId = Session::get('admin_role') === 'superadmin' ? (int)($_GET['ukm_id'] ?? 0) : (int)Session::get('ukm_id');
        
        $eventList = $this->model->getWithAttendanceStats($ukmId);

        $headers = ['ID', 'Nama Kegiatan', 'Waktu Mulai', 'Waktu Selesai', 'Lokasi', 'Total Hadir', 'Total Anggota', 'Persentase (%)'];
        $rows = [];
        foreach ($eventList as $ev) {
            $rows[] = [
                $ev['id'],
                $ev['nama'],
                $ev['waktu_mulai'],
                $ev['waktu_selesai'],
                $ev['lokasi'],
                $ev['total_hadir'],
                $ev['total_anggota'],
                $ev['persentase'] . '%'
            ];
        }

        $title = "Rekapitulasi Seluruh Kegiatan";
        $filename = "Data_Kegiatan_" . date('Y-m-d_H-i-s') . ".xls";
        $ukm = null; // Sistem branding

        include __DIR__ . '/../../views/admin/export_excel.php';
        exit;
    }

    /** Export detail kehadiran satu kegiatan */
    public function exportDetail(): void
    {
        Session::requireLogin();
        $eventId = (int)($_GET['id'] ?? 0);
        $event = $this->model->getById($eventId);
        if (!$event) {
            die("Kegiatan tidak ditemukan");
        }

        require_once __DIR__ . '/../models/Anggota.php';
        require_once __DIR__ . '/../models/Kehadiran.php';
        
        $anggotaList = (new Anggota())->getActive($event['ukm_id']);
        $kehadiranList = (new Kehadiran())->getByEvent($eventId);
        
        $kehadiranMap = [];
        foreach ($kehadiranList as $k) {
            $kehadiranMap[$k['anggota_id']] = $k;
        }

        require_once __DIR__ . '/../models/Ukm.php';
        $ukm = (new Ukm())->getById($event['ukm_id']);

        $headers = ['NIM', 'Nama Anggota', 'Jabatan', 'Status', 'Waktu Hadir', 'Metode'];
        $rows = [];
        foreach ($anggotaList as $ang) {
            $hadir = isset($kehadiranMap[$ang['id']]);
            $waktu = $hadir ? $kehadiranMap[$ang['id']]['waktu_hadir'] : '-';
            $metode = $hadir ? ucfirst($kehadiranMap[$ang['id']]['metode']) : '-';
            
            $rows[] = [
                $ang['nim'] ?? '-',
                $ang['nama'],
                $ang['jabatan'] ?? 'Anggota',
                $hadir ? 'HADIR' : 'ALFA',
                $waktu,
                $metode
            ];
        }

        $title = "Rekap Kehadiran Kegiatan";
        $filename = "Absensi_" . preg_replace('/[^A-Za-z0-9\-]/', '_', $event['nama']) . "_" . date('Ymd') . ".xls";
        $metadata = [
            'Nama Kegiatan' => $event['nama'],
            'Waktu' => date('d M Y', strtotime($event['waktu_mulai'])),
            'Lokasi' => $event['lokasi'] ?? '-'
        ];

        include __DIR__ . '/../../views/admin/export_excel.php';
        exit;
    }

    /** Import daftar kegiatan dari CSV */
    public function importCsv(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])) {
            redirect('index.php?page=event');
        }

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? 0)
            : (int)Session::get('ukm_id');

        if ($ukmId === 0) {
            setFlash('error', 'Gagal import: UKM tidak valid.');
            redirect('index.php?page=event');
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        if ($handle !== FALSE) {
            $headerSkipped = false;
            $successCount = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!$headerSkipped) {
                    $headerSkipped = true; // Asumsi baris pertama adalah header
                    continue;
                }
                
                // Format CSV yang diharapkan:
                // [0] => Nama Kegiatan
                // [1] => Waktu Mulai (Y-m-d H:i:s)
                // [2] => Waktu Selesai (Y-m-d H:i:s)
                // [3] => Lokasi
                // [4] => Deskripsi

                if (count($data) >= 2) {
                    $nama = trim($data[0]);
                    $waktuMulai = trim($data[1]);
                    
                    if (!empty($nama) && !empty($waktuMulai)) {
                        $eventData = [
                            'ukm_id' => $ukmId,
                            'nama' => sanitize($nama),
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => isset($data[2]) ? trim($data[2]) : null,
                            'lokasi' => isset($data[3]) ? sanitize($data[3]) : '',
                            'deskripsi' => isset($data[4]) ? sanitize($data[4]) : '',
                            'status_absensi' => 1
                        ];
                        $this->model->create($eventData);
                        $successCount++;
                    }
                }
            }
            fclose($handle);
            logSecurityActivity('Import CSV Kegiatan', ['ukm_id' => $ukmId, 'total_imported' => $successCount]);
            setFlash('success', "$successCount kegiatan berhasil diimpor.");
        } else {
            setFlash('error', 'Gagal membaca file CSV.');
        }

        redirect('index.php?page=event');
    }

    /** Import daftar kehadiran dari CSV */
    public function importKehadiranCsv(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])) {
            redirect('index.php?page=event');
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $event = $this->model->getById($eventId);
        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            redirect('index.php?page=event');
        }

        $ukmId = $event['ukm_id'];
        
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        if ($handle !== FALSE) {
            require_once __DIR__ . '/../models/Anggota.php';
            require_once __DIR__ . '/../models/Kehadiran.php';
            
            $anggotaModel = new Anggota();
            $kehadiranModel = new Kehadiran();
            
            $headerSkipped = false;
            $successCount = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!$headerSkipped) {
                    $headerSkipped = true; // Asumsi baris pertama adalah header
                    continue;
                }
                
                // Format CSV yang diharapkan:
                // [0] => NIM
                
                if (count($data) >= 1) {
                    $nim = trim($data[0]);
                    
                    if (!empty($nim)) {
                        $anggota = $anggotaModel->getByNim($nim, $ukmId);
                        if ($anggota) {
                            $res = $kehadiranModel->recordAttendance($eventId, $anggota['id'], 'manual');
                            if ($res !== 'error') {
                                // Either recorded or already exists
                                $successCount++;
                            }
                        }
                    }
                }
            }
            fclose($handle);
            logSecurityActivity('Import CSV Kehadiran', ['event_id' => $eventId, 'total_processed' => $successCount]);
            setFlash('success', "Kehadiran berhasil diproses untuk $successCount anggota.");
        } else {
            setFlash('error', 'Gagal membaca file CSV.');
        }

        redirect('index.php?page=detail_event&id=' . $eventId);
    }

    /** Undur kegiatan (Postpone) */
    public function postpone(): void
    {
        Session::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php?page=event');

        $id = (int)($_POST['id'] ?? 0);
        $waktuMulai = $_POST['waktu_mulai'] ?? '';
        $durasi = (int)($_POST['durasi'] ?? 120);
        $alasan = sanitize($_POST['alasan'] ?? '');

        $event = $this->model->getById($id);
        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            redirect('index.php?page=event');
        }

        // Format times
        $waktuMulai = str_replace('T', ' ', $waktuMulai);
        if (strlen($waktuMulai) == 16) $waktuMulai .= ':00';
        $waktuSelesai = date('Y-m-d H:i:s', strtotime($waktuMulai . " + {$durasi} minutes"));

        $this->model->updateStatus($id, 'postponed', $alasan, [
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai
        ], 0); // Matikan scan otomatis saat diundur

        logSecurityActivity('Undur Kegiatan', ['id' => $id, 'nama' => $event['nama'], 'waktu_baru' => $waktuMulai]);

        // --- Generate Redaksi Postpone ---
        require_once __DIR__ . '/../models/Ukm.php';
        $ukmData = (new Ukm())->getById($event['ukm_id']);
        $ukmName = $ukmData ? ($ukmData['singkatan'] ?: $ukmData['nama']) : 'UKM';

        $timestamp = strtotime($waktuMulai);
        $hariIndoMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
        $hariIndo = $hariIndoMap[date('l', $timestamp)] ?? date('l', $timestamp);
        $waktuBaru = $hariIndo . ", " . date('d M Y', $timestamp) . " Jam " . date('H:i', $timestamp) . " WIB";

        $redaksi = "⚠️ [PEMBERITAHUAN PERUBAHAN JADWAL: {$ukmName}] ⚠️\n\n";
        $redaksi .= "Halo Rekan-rekan! 👋\n";
        $redaksi .= "Terdapat perubahan jadwal untuk kegiatan:\n\n";
        $redaksi .= "📌 Nama Kegiatan: {$event['nama']}\n";
        $redaksi .= "🕒 DIUNDUR MENJADI: {$waktuBaru}\n";
        if ($alasan) $redaksi .= "📝 Alasan: {$alasan}\n";
        $redaksi .= "📍 Lokasi: {$event['lokasi']}\n\n";
        $redaksi .= "Mohon maaf atas ketidaknyamanannya. Pastikan untuk mencatat waktu terbaru ini. Terima kasih! 🙏";

        Session::start();
        $_SESSION['redaksi_to_copy'] = $redaksi;

        setFlash('success', 'Kegiatan berhasil diundur.');
        redirect('index.php?page=event');
    }

    /** Batalkan kegiatan (Cancel) */
    public function cancel(): void
    {
        Session::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php?page=event');

        $id = (int)($_POST['id'] ?? 0);
        $alasan = sanitize($_POST['alasan'] ?? '');

        $event = $this->model->getById($id);
        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            redirect('index.php?page=event');
        }

        $this->model->updateStatus($id, 'cancelled', $alasan, null, 0); // Matikan scan otomatis saat dibatalkan
        logSecurityActivity('Batalkan Kegiatan', ['id' => $id, 'nama' => $event['nama']]);

        // --- Generate Redaksi Cancel ---
        require_once __DIR__ . '/../models/Ukm.php';
        $ukmData = (new Ukm())->getById($event['ukm_id']);
        $ukmName = $ukmData ? ($ukmData['singkatan'] ?: $ukmData['nama']) : 'UKM';

        $redaksi = "❌ [PEMBERITAHUAN PEMBATALAN: {$ukmName}] ❌\n\n";
        $redaksi .= "Halo Rekan-rekan! 👋\n";
        $redaksi .= "Dengan berat hati kami informasikan bahwa kegiatan berikut:\n\n";
        $redaksi .= "📌 Nama Kegiatan: {$event['nama']}\n";
        $redaksi .= "🚫 STATUS: DIBATALKAN\n";
        if ($alasan) $redaksi .= "📝 Alasan: {$alasan}\n\n";
        $redaksi .= "Kegiatan ini tidak jadi dilaksanakan sesuai jadwal awal. Mohon maaf atas perubahan mendadak ini. 🙏\n\n";
        $redaksi .= "Terima kasih atas pengertiannya.";

        Session::start();
        $_SESSION['redaksi_to_copy'] = $redaksi;

        setFlash('success', 'Kegiatan berhasil dibatalkan.');
        redirect('index.php?page=event');
    }

    /** Generate ulang redaksi untuk kegiatan tanpa mengedit status */
    public function generateRedaksi(): void
    {
        Session::requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        
        $event = $this->model->getById($id);
        if (!$event) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            redirect('index.php?page=event');
        }

        require_once __DIR__ . '/../models/Ukm.php';
        $ukmData = (new Ukm())->getById($event['ukm_id']);
        $ukmName = $ukmData ? ($ukmData['singkatan'] ?: $ukmData['nama']) : 'UKM';

        $timestamp = strtotime($event['waktu_mulai']);
        $hariIndoMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
        $hariIndo = $hariIndoMap[date('l', $timestamp)] ?? date('l', $timestamp);
        $waktuLengkap = $hariIndo . ", " . date('d M Y', $timestamp) . " Jam " . date('H:i', $timestamp) . " WIB";

        $redaksi = "📢 [PENGINGAT KEGIATAN: {$ukmName}] 📢\n\n";
        $redaksi .= "Halo Rekan-rekan! 👋\n";
        $redaksi .= "Jangan lupa untuk menghadiri kegiatan kita:\n\n";
        $redaksi .= "📌 Nama Kegiatan: {$event['nama']}\n";
        if ($event['deskripsi']) $redaksi .= "📝 Deskripsi: {$event['deskripsi']}\n";
        $redaksi .= "📅 Waktu: {$waktuLengkap}\n";
        $redaksi .= "📍 Lokasi: {$event['lokasi']}\n\n";
        $redaksi .= "Mohon kehadirannya tepat waktu ya. Pastikan kalian melakukan absen jari di perangkat IoT yang tersedia. \n\n";
        $redaksi .= "Terima kasih atas partisipasinya! 🙏";

        Session::start();
        $_SESSION['redaksi_to_copy'] = $redaksi;

        setFlash('success', 'Redaksi pengumuman kegiatan berhasil dibuat! Silakan copy-paste ke grup.');
        redirect('index.php?page=event');
    }

    /** Toggle kehadiran manual per anggota (AJAX) */
    public function toggleKehadiranManual(): void
    {
        header('Content-Type: application/json');
        Session::requireLogin();

        $eventId = (int)($_POST['event_id'] ?? 0);
        $anggotaId = (int)($_POST['anggota_id'] ?? 0);
        $action = $_POST['attendance_action'] ?? 'present'; // 'present' or 'absent'
        $reason = sanitize($_POST['reason'] ?? '');

        if ($eventId === 0 || $anggotaId === 0) {
            echo json_encode(['success' => false, 'error' => 'Data tidak lengkap.']);
            exit;
        }

        require_once __DIR__ . '/../models/Kehadiran.php';
        $kehadiranModel = new Kehadiran();

        if ($action === 'present') {
            if (empty($reason)) {
                echo json_encode(['success' => false, 'error' => 'Alasan wajib diisi untuk absensi manual.']);
                exit;
            }
            $res = $kehadiranModel->recordAttendance($eventId, $anggotaId, 'manual', $reason);
            logSecurityActivity('Absensi Manual Anggota', ['event_id' => $eventId, 'anggota_id' => $anggotaId, 'reason' => $reason]);
            echo json_encode(['success' => true, 'message' => 'Kehadiran berhasil dicatat.']);
        } else {
            $res = $kehadiranModel->removeAttendance($eventId, $anggotaId);
            logSecurityActivity('Hapus Absensi Anggota', ['event_id' => $eventId, 'anggota_id' => $anggotaId]);
            echo json_encode(['success' => true, 'message' => 'Kehadiran berhasil dihapus.']);
        }
        exit;
    }
}
