<?php
/**
 * Session & Authentication Helper
 */
class Session
{
    /** Memulai session jika belum aktif */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Hardened session cookie settings
            $timeout = 1800; // 30 minutes
            session_set_cookie_params([
                'lifetime' => $timeout,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            ini_set('session.gc_maxlifetime', $timeout);
            session_start();
        }
    }

    /** Cek apakah user sudah login */
    public static function isLoggedIn(): bool
    {
        self::start();
        return isset($_SESSION['admin_id']);
    }

    /** Simpan data user ke session setelah login */
    public static function login(array $admin): void
    {
        self::start();
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['ukm_id']     = $admin['ukm_id'];
        $_SESSION['periode_id'] = $admin['periode_id'] ?? null;
        $_SESSION['admin_foto'] = $admin['foto_path'];
        
        if ($admin['role'] === 'superadmin') {
            $_SESSION['is_active_periode'] = true;
        } else if (!empty($admin['periode_id'])) {
            require_once __DIR__ . '/models/Periode.php';
            $p = (new Periode())->getById((int)$admin['periode_id']);
            $_SESSION['is_active_periode'] = $p ? (bool)$p['is_active'] : false;
        } else {
            $_SESSION['is_active_periode'] = false;
        }
    }

    /** Hapus session (logout) */
    public static function logout(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    /** Ambil data dari session */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /** Set data ke session */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /** Proteksi halaman - redirect jika belum login */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /** Proteksi halaman superadmin only */
    public static function requireSuperAdmin(): void
    {
        self::requireLogin();
        if (self::get('admin_role') !== 'superadmin') {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    // --- 2FA Partial Session Helpers --- //
    public static function setPending(array $admin): void
    {
        self::start();
        $_SESSION['auth_pending_admin'] = $admin;
    }

    public static function getPending(): ?array
    {
        self::start();
        return $_SESSION['auth_pending_admin'] ?? null;
    }

    public static function clearPending(): void
    {
        self::start();
        unset($_SESSION['auth_pending_admin']);
    }

    public static function requirePending(): void
    {
        if (!self::getPending()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    // --- CSRF Protection Helpers --- //
    
    /** Generate or get existing CSRF token */
    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Validate CSRF token */
    public static function validateCsrf(?string $token): bool
    {
        self::start();
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
