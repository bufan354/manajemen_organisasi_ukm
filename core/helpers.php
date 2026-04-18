<?php
/**
 * Helper functions: redirect, flash messages, sanitize
 */

/** Redirect ke URL */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/** Set flash message (ditampilkan sekali) */
function setFlash(string $type, string $message): void
{
    Session::start();
    $_SESSION['flash'] = [
        'type'    => $type,    // success, error, warning, info
        'message' => $message,
    ];
}

/** Ambil dan hapus flash message */
function getFlash(): ?array
{
    Session::start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Sanitize input string - trim and strip tags for basic safety */
function sanitize(string $input): string
{
    return trim(strip_tags($input));
}

/** Output-safe escaping untuk HTML (gunakan ini di view sebagai pengganti htmlspecialchars) */
function h(?string $input): string
{
    return htmlspecialchars($input ?? '', ENT_QUOTES, 'UTF-8');
}

/** Get current CSRF token */
function csrf_token(): string
{
    return Session::csrfToken();
}

/** Output hidden CSRF input field */
function csrf_field(): string
{
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/** Render flash message HTML (untuk di-include di view) */
function renderFlash(): string
{
    $flash = getFlash();
    if (!$flash) return '';

    $colors = [
        'success' => 'bg-emerald-50 border-emerald-500 text-emerald-800',
        'error'   => 'bg-red-50 border-red-500 text-red-800',
        'warning' => 'bg-amber-50 border-amber-500 text-amber-800',
        'info'    => 'bg-blue-50 border-blue-500 text-blue-800',
    ];

    $icons = [
        'success' => 'check_circle',
        'error'   => 'error',
        'warning' => 'warning',
        'info'    => 'info',
    ];

    $type  = $flash['type'];
    $msg   = htmlspecialchars($flash['message']);
    $color = $colors[$type] ?? $colors['info'];
    $icon  = $icons[$type] ?? $icons['info'];

    return <<<HTML
    <div class="flex items-center gap-3 p-4 rounded-xl border-l-4 {$color} mb-6 shadow-sm animate-fade-in" id="flash-msg">
        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">{$icon}</span>
        <p class="text-sm font-medium flex-1">{$msg}</p>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
    HTML;
}

/**
 * Log aktivitas keamanan ke database log_keamanan
 */
function logSecurityActivity(string $aktivitas, array $detail = []): void
{
    require_once __DIR__ . '/models/LogKeamanan.php';
    require_once __DIR__ . '/Session.php';
    Session::start();
    
    $userId = Session::get('admin_id') ?: null;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $detailJson = !empty($detail) ? json_encode($detail) : null;
    
    (new LogKeamananModel())->insert($aktivitas, (int)$userId ?: null, $ipAddress, $userAgent, $detailJson);
}

/**
 * Ambil nilai pengaturan umum (global) dengan cache di session.
 * Pertama kali dipanggil → query semua dari DB lalu simpan di $_SESSION.
 * Panggilan berikutnya → ambil dari cache session.
 */
function getSetting(string $key, ?string $default = null): ?string
{
    Session::start();
    
    // Jika cache belum ada, load semua dari DB
    if (!isset($_SESSION['pengaturan_umum'])) {
        require_once __DIR__ . '/models/PengaturanUmum.php';
        $_SESSION['pengaturan_umum'] = (new PengaturanUmum())->getAll();
    }
    
    return $_SESSION['pengaturan_umum'][$key] ?? $default;
}

/**
 * Shortcut untuk mendapatkan nama entitas (default: "UKM").
 * Digunakan di seluruh view untuk mengganti hardcoded "UKM".
 */
function getEntityLabel(): string
{
    return getSetting('entitas_nama', 'UKM');
}

/**
 * Helper: Tambah Notifikasi
 */
function addNotifikasi(int $user_id, string $jenis, string $judul, string $pesan, ?string $link = null, ?int $ukm_id = null): void
{
    require_once __DIR__ . '/models/Notifikasi.php';
    (new NotifikasiModel())->create([
        'user_id' => $user_id,
        'ukm_id'  => $ukm_id,
        'jenis'   => $jenis,
        'judul'   => $judul,
        'pesan'   => $pesan,
        'link'    => $link
    ]);
}

/** Get environment variable from .env */
function getEnvVar(string $key, $default = null)
{
    static $env = null;
    if ($env === null) {
        $envPath = __DIR__ . '/../.env';
        $env = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $env[trim($parts[0])] = trim($parts[1], " \t\n\r\0\x0B\"'");
                }
            }
        }
    }
    return $env[$key] ?? $default;
}

/** Format phone number to WhatsApp international format (62...) */
function formatWhatsAppPhone(string $phone): string
{
    // Remove non-numeric characters
    $formatted = preg_replace('/[^0-9]/', '', $phone);
    
    // Replace leading '0' with '62' (Indonesia code)
    if (str_starts_with($formatted, '0')) {
        $formatted = '62' . substr($formatted, 1);
    }
    
    return $formatted;
}
