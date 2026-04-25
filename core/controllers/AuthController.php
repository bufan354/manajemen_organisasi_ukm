<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/LoginAttempt.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Auth
 * Login, Logout, Verifikasi
 */
class AuthController
{
    private AdminModel $adminModel;
    private LoginAttemptModel $attemptModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->attemptModel = new LoginAttemptModel();
    }

    /** Proses login */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=login');
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        // Cek Rate Limit
        $attempt = $this->attemptModel->getAttempt($ip);
        if ($attempt && $attempt['locked_until']) {
            if (strtotime($attempt['locked_until']) > time()) {
                setFlash('error', 'Keamanan: Alamat IP Anda diblokir sementara karena terlalu banyak percobaan gagal. Silakan coba 15 menit lagi.');
                redirect('index.php?page=login');
            }
        }

        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('error', 'Email dan password wajib diisi.');
            redirect('index.php?page=login');
        }

        $admin = $this->adminModel->verifyPassword($email, $password);

        if ($admin) {
            // Berhasil tahap 1
            $this->attemptModel->clearAttempt($ip);
            
            if (empty($admin['is_2fa_active'])) {
                // Langsung login karena 2FA opsional dan belum disetup
                $this->completeLogin($admin);
            } else {
                // 2FA Aktif, arahkan ke halaman verifikasi OTP
                Session::setPending($admin);
                Session::set('otp_fails_count', 0);
                redirect('index.php?page=verify_2fa');
            }
        } else {
            // Gagal tahap 1
            $this->attemptModel->recordFailedAttempt($ip, $email);
            $attemptInfo = $this->attemptModel->getAttempt($ip);
            $failCount = $attemptInfo ? $attemptInfo['fail_count'] : 1;
            $sisa = 5 - $failCount;
            
            logSecurityActivity('Login Gagal - Kredensial Salah', ['email' => $email]);
            
            if ($sisa > 0) {
                setFlash('error', "Email atau password salah. Sisa percobaan: {$sisa} kali lagi.");
            } else {
                setFlash('error', "Terlalu banyak percobaan gagal. Akses diblokir 15 menit.");
            }
            
            redirect('index.php?page=login');
        }
    }

    /** Verifikasi OTP Login (Jika 2FA Aktif) */
    public function verify2FA(): void
    {
        $admin = Session::getPending();
        if (!$admin) {
            redirect('index.php?page=login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=verify_2fa');
        }

        $otp = $_POST['otp'] ?? '';
        if (empty($otp) || !preg_match('/^\d{6}$/', $otp)) {
            setFlash('error', 'Format OTP tidak valid. Harus 6 digit angka.');
            redirect('index.php?page=verify_2fa');
        }

        require_once __DIR__ . '/../GoogleAuthenticator.php';
        $ga = new PHPGangsta_GoogleAuthenticator();

        if ($ga->verifyCode($admin['totp_secret'], $otp, 2)) { // Toleransi 2*30 dtk
            // OTP Valid
            $this->completeLogin($admin);
        } else {
            // Gagal
            $fails = (int)Session::get('otp_fails_count', 0);
            $fails++;
            Session::set('otp_fails_count', $fails);

            if ($fails >= 3) {
                // Kunci / Blokir sementara 2FA session
                Session::clearPending();
                setFlash('error', 'Terlalu banyak percobaan OTP gagal. Silakan login kembali.');
                redirect('index.php?page=login');
            }

            setFlash('error', 'Kode OTP salah. Sisa percobaan: ' . (3 - $fails));
            redirect('index.php?page=verify_2fa');
        }
    }

    /** Aktifkan 2FA via Dashboard (Pengaturan) */
    public function setup2faDashboard(): void
    {
        Session::requireLogin();
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        if (!empty($admin['is_2fa_active'])) {
            setFlash('error', '2FA sudah aktif.');
            redirect('index.php?page=pengaturan');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../GoogleAuthenticator.php';
            $ga = new PHPGangsta_GoogleAuthenticator();
            
            $secret = Session::get('setup_totp_secret');
            $code = $_POST['otp'] ?? '';
            
            if ($ga->verifyCode($secret, $code, 2)) {
                $this->adminModel->updateTotpSettings($adminId, $secret, 1);
                Session::set('setup_totp_secret', null); // clear session
                Session::set('2fa_unlocked_setup', null); // clear unlock state
                setFlash('success', 'Autentikasi Dua Langkah berhasil diaktifkan!');
            } else {
                setFlash('error', 'Kode OTP tidak valid atau kadaluarsa.');
            }
            redirect('index.php?page=pengaturan#keamanan');
        }
    }

    /** Menginisialisasi Setup 2FA (Verifikasi Password Dulu) */
    public function initSetup2faDashboard(): void
    {
        Session::requireLogin();
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        if (!empty($admin['is_2fa_active'])) {
            setFlash('error', '2FA sudah aktif.');
            redirect('index.php?page=pengaturan');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            if (password_verify($password, $admin['password'])) {
                Session::set('2fa_unlocked_setup', true);
                
                // Generate secret awal agar bisa ditampilkan sebagai teks
                require_once __DIR__ . '/../GoogleAuthenticator.php';
                $ga = new PHPGangsta_GoogleAuthenticator();
                Session::set('setup_totp_secret', $ga->createSecret());
                
            } else {
                setFlash('error', 'Password salah! Tidak dapat memulai pengaturan 2FA.');
            }
            redirect('index.php?page=pengaturan#keamanan');
        }
    }

    /** Menghapus/Reset 2FA via Dashboard dengan Konfirmasi Password */
    public function reset2faDashboard(): void
    {
        Session::requireLogin();
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            if (password_verify($password, $admin['password'])) {
                $this->adminModel->updateTotpSettings($adminId, '', 0);
                setFlash('success', 'Autentikasi Dua Langkah berhasil DIBERHENTIKAN.');
            } else {
                setFlash('error', 'Password salah! Gagal memberhentikan 2FA.');
            }
            redirect('index.php?page=pengaturan#keamanan');
        }
    }

    /** Generate QR Code image murni PHP (dikirim ke frontend via img as SVG) */
    public function qrImage2fa($returnSvgString = false): ?string
    {
        Session::requireLogin();
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        require_once __DIR__ . '/../GoogleAuthenticator.php';
        $ga = new PHPGangsta_GoogleAuthenticator();
        
        $secret = Session::get('setup_totp_secret');
        if (!$secret) {
            $secret = $ga->createSecret();
            Session::set('setup_totp_secret', $secret);
        }
        
        $name = 'AbsensiIoT:'.$admin['email'];
        $issuer = 'AbsensiIoT';
        $urlencoded = 'otpauth://totp/'.$name.'?secret='.$secret.'&issuer='.$issuer;

        require_once __DIR__ . '/../phpqrcode/qrlib.php';
        
        if ($returnSvgString) {
            ob_start();
            QRcode::svg($urlencoded, false, 'M', 5, 2);
            $svg = ob_get_clean();
            return $svg;
        }

        // Output image as SVG to bypass missing PHP GD module issue
        QRcode::svg($urlencoded, false, 'M', 5, 2);
        exit;
    }

    /** Membatalkan proses Setup 2FA */
    public function cancelSetup2faDashboard(): void
    {
        Session::requireLogin();
        Session::set('2fa_unlocked_setup', null);
        Session::set('setup_totp_secret', null);
        redirect('index.php?page=pengaturan#keamanan');
    }

    /** Mengunduh Backup Kunci 2FA dalam format HTML/Teks Offline */
    public function downloadBackup2fa(): void
    {
        Session::requireLogin();
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);
        
        $secret = Session::get('setup_totp_secret');
        if (!$secret) {
            redirect('index.php?page=pengaturan#keamanan');
        }

        $svgCode = $this->qrImage2fa(true);
        $waktu = date('Y-m-d H:i:s');

        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="2FA-Backup-'.$admin['email'].'.html"');
        
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Backup 2FA - {$admin['email']}</title>
            <style>
                body { font-family: sans-serif; background: #fdfdfd; color: #111; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
                .qr-container { display: flex; justify-content: center; background: white; padding: 20px; border-radius: 10px; border: 2px dashed #aaa; margin: 20px 0; }
                .key-box { background: #eef2ff; color: #004191; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; border-radius: 8px; border: 1px solid #c7d2fe; }
                .warning { color: #b91c1c; font-size: 14px; margin-top: 30px; font-weight: bold; text-align: center;}
                svg { width: 250px; height: 250px; }
            </style>
        </head>
        <body>
            <h2 style="text-align: center;">Backup Kunci Keamanan 2-Langkah</h2>
            <p><strong>Akun:</strong> {$admin['email']}</p>
            <p><strong>Waktu Generate:</strong> {$waktu}</p>
            <hr>
            <p>Pindai Kode QR di bawah ini menggunakan aplikasi Authenticator Anda:</p>
            <div class="qr-container">
                {$svgCode}
            </div>
            <p style="text-align: center;">Atau masukkan Setup Key / Kunci Manual ini:</p>
            <div class="key-box">
                {$secret}
            </div>
            <p class="warning">PENTING: Simpan file ini di tempat yang aman dan jangan berikan kepada siapapun. Ini adalah kunci akses cadangan Anda!</p>
            <script>window.print();</script>
        </body>
        </html>
        HTML;
        exit;
    }

    /** Melengkapi sesi sesungguhnya */
    private function completeLogin(array $admin): void
    {
        Session::login($admin);
            
            require_once 'core/models/AdminSession.php';
            $sessionModel = new AdminSessionModel();
            
            // Clean up any old sessions system-wide for good measure
            $sessionModel->clearExpiredSessions();
            
            // Cek apakah perangkat baru
            $currentIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            $existingSessions = $sessionModel->getSessionsByAdminId($admin['id']);
            $isNewDevice = true;
            foreach ($existingSessions as $s) {
                if ($s['ip_address'] === $currentIp && $s['user_agent'] === $currentUserAgent) {
                    $isNewDevice = false;
                    break;
                }
            }

            // Register this session
            $sessionModel->insertSession(
                $admin['id'],
                session_id(),
                $currentIp,
                $currentUserAgent
            );

            // Trigger notifikasi jika perangkat baru
            if ($isNewDevice) {
                // Parse browser & OS from user agent for a friendlier message
                $browserInfo = $this->parseUserAgent($currentUserAgent);
                $deviceDesc = $browserInfo . ' · IP ' . htmlspecialchars($currentIp);
                $waktuLogin = date('d M Y, H:i');

                // Notifikasi untuk yang bersangkutan
                $judul = "Login dari Perangkat Baru";
                $pesan = "Akun Anda login dari perangkat baru: {$deviceDesc} pada {$waktuLogin}. Jika ini bukan Anda, segera amankan akun.";
                $link = "index.php?page=pengaturan#login_sessions";
                addNotifikasi($admin['id'], 'login_perangkat_baru', $judul, $pesan, $link, $admin['ukm_id'] ?? null);

                // Jika user yang login adalah admin UKM, beri tahu juga Superadmin sebagai tembusan keamanan
                if ($admin['role'] === 'admin') {
                    require_once __DIR__ . '/../models/Ukm.php';
                    $ukmData = ($admin['ukm_id']) ? (new Ukm())->getById($admin['ukm_id']) : null;
                    $ukmNama = $ukmData ? $ukmData['nama'] : 'ID ' . ($admin['ukm_id'] ?? '-');
                    
                    $superadmins = $this->adminModel->getByRole('superadmin');
                    foreach ($superadmins as $sa) {
                        $pesanSa = "Admin {$admin['nama']} ({$ukmNama}) login dari perangkat baru: {$deviceDesc} pada {$waktuLogin}.";
                        addNotifikasi($sa['id'], 'login_perangkat_baru', 'Keamanan: Login Admin Baru', $pesanSa, 'index.php?page=log_keamanan', null);
                    }
                }
            }

            logSecurityActivity('Login Berhasil', ['admin_id' => $admin['id'], 'role' => $admin['role'], 'email' => $admin['email']]);
            setFlash('success', 'Selamat datang, ' . $admin['nama'] . '!');
            redirect('index.php?page=dashboard');
    }

    /** Proses logout */
    public function logout(): void
    {
        $adminId = Session::get('admin_id');
        $email = Session::get('admin_email');
        
        require_once 'core/models/AdminSession.php';
        $sessionModel = new AdminSessionModel();
        $sessionModel->revokeSession(session_id());
        
        logSecurityActivity('Logout Sistem', ['admin_id' => $adminId, 'email' => $email]);
        Session::logout();
        setFlash('success', 'Berhasil logout.');
        redirect('index.php?page=login');
    }

    /** Revoke a specific session (Dari pengaturan) */
    public function revokeSession(): void
    {
        Session::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=pengaturan');
        }

        $sidToRevoke = $_POST['session_id'] ?? '';
        
        if (!empty($sidToRevoke)) {
            require_once 'core/models/AdminSession.php';
            $sessionModel = new AdminSessionModel();
            
            // Verifikasi kepemilikan session agar tidak sembarangan
            $myAdminId = Session::get('admin_id');
            $sessions = $sessionModel->getSessionsByAdminId($myAdminId);
            
            $isValid = false;
            foreach ($sessions as $s) {
                if ($s['session_id'] === $sidToRevoke) {
                    $isValid = true;
                    break;
                }
            }
            
            if ($isValid) {
                $sessionModel->revokeSession($sidToRevoke);
                logSecurityActivity('Cabut Sesi Perangkat', ['session_id' => $sidToRevoke]);
                setFlash('success', 'Sesi perangkat berhasil diputuskan.');
            } else {
                setFlash('error', 'Sesi tidak ditemukan atau akses ditolak.');
            }
        }
        
        redirect('index.php?page=pengaturan');
    }

    /** Revoke semua sesi lain milik admin ini (kecuali sesi aktif saat ini) */
    public function revokeAllSessions(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=pengaturan');
        }

        require_once 'core/models/AdminSession.php';
        $sessionModel = new AdminSessionModel();
        $myAdminId    = (int) Session::get('admin_id');
        $currentSid   = session_id();

        $sessions = $sessionModel->getSessionsByAdminId($myAdminId);
        $count = 0;
        foreach ($sessions as $s) {
            if ($s['session_id'] !== $currentSid) {
                $sessionModel->revokeSession($s['session_id']);
                $count++;
            }
        }

        logSecurityActivity('Cabut Semua Sesi Lain', ['jumlah' => $count]);
        setFlash('success', $count > 0
            ? "{$count} sesi perangkat lain berhasil diputuskan."
            : 'Tidak ada sesi lain yang aktif.');

        redirect('index.php?page=pengaturan');
    }

    /**
     * Simple user agent parser to extract Browser + OS for notification messages
     */
    private function parseUserAgent(string $ua): string
    {
        // Detect browser
        $browser = 'Unknown Browser';
        if (preg_match('/Edg\//i', $ua))          $browser = 'Microsoft Edge';
        elseif (preg_match('/OPR\//i', $ua))       $browser = 'Opera';
        elseif (preg_match('/Chrome\//i', $ua))    $browser = 'Chrome';
        elseif (preg_match('/Firefox\//i', $ua))   $browser = 'Firefox';
        elseif (preg_match('/Safari\//i', $ua) && !preg_match('/Chrome/i', $ua)) $browser = 'Safari';
        elseif (preg_match('/MSIE|Trident/i', $ua)) $browser = 'Internet Explorer';

        // Detect OS
        $os = 'Unknown OS';
        if (preg_match('/Windows NT 10/i', $ua))       $os = 'Windows 10/11';
        elseif (preg_match('/Windows/i', $ua))          $os = 'Windows';
        elseif (preg_match('/Macintosh|Mac OS/i', $ua)) $os = 'macOS';
        elseif (preg_match('/Android/i', $ua))          $os = 'Android';
        elseif (preg_match('/iPhone|iPad/i', $ua))      $os = 'iOS';
        elseif (preg_match('/Linux/i', $ua))            $os = 'Linux';

        return "{$browser} di {$os}";
    }
}
