<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Backup
 * Manages secure database backup with multi-stage verification
 */
class BackupController
{
    private AdminModel $adminModel;
    private PDO $db;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->db = Database::getConnection();
        Session::requireSuperAdmin();
        
        // Update aktivitas setiap kali masuk ke controller backup agar session tetap hidup ditable admin_sessions
        require_once __DIR__ . '/../models/AdminSession.php';
        $sid = session_id();
        if ($sid) {
            (new AdminSessionModel())->updateActivity($sid);
        }
    }

    /** View: Halaman Utama Backup (Verifikasi) */
    public function index(): void
    {
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        $stage = Session::get('backup_auth_stage', 'none');
        $lastActivity = Session::get('backup_auth_time', 0);
        $justVerified = Session::get('backup_auth_just_verified', false);
        $now = time();

        // RESET LOGIC: 
        // 1. Jika sesi sudah terlalu lama (> 5 menit)
        // 2. Jika stage bukan 'none' tapi bukan hasil verifikasi baru (proteksi fresh landing dari menu lain)
        // 3. Jika ini adalah GET request normal tanpa flag 'justVerified'
        if ($stage !== 'none') {
            if ($now - $lastActivity > 300 || (!$justVerified && $_SERVER['REQUEST_METHOD'] === 'GET')) {
                Session::set('backup_auth_stage', 'none');
                Session::set('backup_auth_time', 0);
                $stage = 'none';
            }
        }
        
        // Hapus flag 'justVerified' setelah dibaca satu kali
        Session::set('backup_auth_just_verified', false);

        // Stage fingerprint dihapus untuk penyederhanaan - Langsung ke View Auth Utama (Password/2FA) atau Sukses

        View::renderAdmin('admin/backup_auth', [
            'title' => 'Keamanan Backup - Sistem Absensi IoT',
            'admin' => $admin,
            'stage' => $stage
        ]);
    }

    /** Action: Verify Password */
    public function verifyPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php?page=backup');
        
        $password = $_POST['password'] ?? '';
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        if (password_verify($password, $admin['password'])) {
            // Jika 2FA aktif, lanjut ke 2FA. Jika tidak, langsung READY.
            $nextStage = empty($admin['is_2fa_active']) ? 'ready' : 'password_verified';
            Session::set('backup_auth_stage', $nextStage);
            Session::set('backup_auth_time', time());
            Session::set('backup_auth_just_verified', true);
            logSecurityActivity('Backup: Verifikasi Password Berhasil');
        } else {
            setFlash('error', 'Password salah.');
            logSecurityActivity('Backup: Verifikasi Password Gagal');
        }
        session_write_close();
        redirect('index.php?page=backup');
    }

    /** Action: Verify 2FA */
    public function verify2FA(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php?page=backup');
        
        $stage = Session::get('backup_auth_stage');
        if ($stage !== 'password_verified') redirect('index.php?page=backup');

        $otp = $_POST['otp'] ?? '';
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->getById((int)$adminId);

        require_once __DIR__ . '/../GoogleAuthenticator.php';
        $ga = new PHPGangsta_GoogleAuthenticator();

        if ($ga->verifyCode($admin['totp_secret'], $otp, 2)) {
            Session::set('backup_auth_stage', 'ready');
            Session::set('backup_auth_time', time());
            Session::set('backup_auth_just_verified', true);
            logSecurityActivity('Backup: Verifikasi 2FA Berhasil');
        } else {
            setFlash('error', 'Kode OTP salah.');
            logSecurityActivity('Backup: Verifikasi 2FA Gagal');
        }
        session_write_close();
        redirect('index.php?page=backup');
    }

    /** Action: Download Database Backup */
    public function download(): void
    {
        // DEBUG MODE: Paksa tampilkan error jika terjadi 500
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        if (Session::get('backup_auth_stage') !== 'ready') {
            setFlash('error', 'Akses ditolak. Selesaikan verifikasi keamanan terlebih dahulu.');
            redirect('index.php?page=backup');
        }

        try {
            logSecurityActivity('Backup: Mengunduh Database SQL');
            $this->exportDatabase();
            
            // Hapus status otorisasi hanya jika exportDatabase terpanggil sukses (jarang sampai sini karena ada exit)
            Session::set('backup_auth_stage', 'none');
        } catch (Throwable $e) {
            echo "<h1>Database Backup Error</h1>";
            echo "<p>Pesan Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            exit;
        }
    }

    /** Logic: Export Database to .sql */
    private function exportDatabase(): void
    {
        // Berikan waktu lebih panjang untuk proses backup
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        // Tarik kredensial dari .env (bukan konstanta global)
        $dbHost = getEnvVar('DB_HOST', 'localhost');
        $dbUser = getEnvVar('DB_USER', 'root');
        $dbPass = getEnvVar('DB_PASS', '');
        $dbName = getEnvVar('DB_NAME', 'absensi_iot');

        $filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
        
        // Bersihkan output buffer agar file bersih (headers already sent protection)
        while (ob_get_level()) ob_end_clean();
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            // Cek apakah mysqldump tersedia (Prioritas Utama karena paling cepat/hemat memori)
            $mysqldumpAvailable = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                $checkDump = shell_exec('which mysqldump');
                $mysqldumpAvailable = !empty($checkDump);
            }

            if ($mysqldumpAvailable) {
                $command = "mysqldump --opt -h {$dbHost} -u {$dbUser} -p" . escapeshellarg($dbPass) . " {$dbName}";
                passthru($command);
            } else {
                // BACKUP MANUAL (STREAMING MODE): Lebih lambat tapi hemat RAM
                $stmtTables = $this->db->query("SHOW TABLES");
                
                echo "-- SQL Dump (PHP Streaming Mode)\n";
                echo "-- Host: {$dbHost}\n";
                echo "-- Database: {$dbName}\n";
                echo "-- Time: " . date('Y-m-d H:i:s') . "\n\n";
                
                while ($table = $stmtTables->fetch(PDO::FETCH_COLUMN)) {
                    $createOp = $this->db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
                    echo "DROP TABLE IF EXISTS `{$table}`;\n";
                    echo $createOp['Create Table'] . ";\n\n";
                    
                    // Ambil data BARIS-PER-BARIS agar tidak kehabisan RAM
                    $stmtRows = $this->db->query("SELECT * FROM `{$table}`");
                    while ($row = $stmtRows->fetch(PDO::FETCH_ASSOC)) {
                        $keys = array_keys($row);
                        $values = array_map(function($v) {
                            if ($v === null) return 'NULL';
                            return $this->db->quote($v);
                        }, array_values($row));
                        
                        echo "INSERT INTO `{$table}` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $values) . ");\n";
                    }
                    echo "\n";
                    // Paksa output keluar ke browser per tabel
                    flush();
                }
            }
        } catch (Exception $e) {
            echo "\n-- Backup Error: " . $e->getMessage() . "\n";
        }
        exit;
    }
}
