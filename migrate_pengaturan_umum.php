<?php
require_once __DIR__ . '/core/Session.php';
Session::requireSuperAdmin();
/**
 * Migration: Pengaturan Umum (Global Settings)
 * 1. Buat tabel pengaturan_umum
 * 2. Seed data default (label entitas + hero section)
 */
require_once 'core/Database.php';

$db = Database::getConnection();
$errors = [];
$success = [];

// --- 1. Buat tabel pengaturan_umum ---
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS pengaturan_umum (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            key_name   VARCHAR(100) UNIQUE NOT NULL,
            value      TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $success[] = 'Tabel <code>pengaturan_umum</code> berhasil dibuat (atau sudah ada).';
} catch (PDOException $e) {
    $errors[] = 'Gagal membuat tabel pengaturan_umum: ' . $e->getMessage();
}

// --- 2. Seed data default ---
$defaults = [
    'entitas_nama'       => 'UKM',
    'hero_judul'         => 'SISTEM ABSENSI & MANAJEMEN ORGANISASI',
    'hero_deskripsi'     => 'Optimalkan efisiensi kehadiran dengan teknologi berbasis Fingerprint + ESP32. Monitoring real-time untuk transparansi organisasi digital.',
    'hero_btn1_label'    => 'Jelajahi UKM',
    'hero_btn1_link'     => 'index.php?page=katalog_ukm',
    'hero_btn2_label'    => 'Dokumentasi API',
    'hero_btn2_link'     => 'index.php?page=tentang',
    'hero_gambar'        => '',
    'hero_overlay_opacity' => '20',
];

try {
    $stmt = $db->prepare("
        INSERT IGNORE INTO pengaturan_umum (key_name, value)
        VALUES (?, ?)
    ");
    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
    }
    $success[] = 'Data default berhasil di-seed ke <code>pengaturan_umum</code> (hanya yang belum ada).';
} catch (PDOException $e) {
    $errors[] = 'Gagal seed data default: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration: Pengaturan Umum</title>
    <style>
        body { font-family: monospace; background: #111; color: #eee; padding: 2rem; }
        h1 { color: #60a5fa; margin-bottom: 1rem; }
        .ok  { background: #052e16; border-left: 4px solid #22c55e; padding: .75rem 1rem; margin: .5rem 0; border-radius: 4px; }
        .err { background: #2d0000; border-left: 4px solid #ef4444; padding: .75rem 1rem; margin: .5rem 0; border-radius: 4px; }
        a { color: #60a5fa; }
    </style>
</head>
<body>
    <h1>🗄️ Migration: Pengaturan Umum (Global Settings)</h1>

    <?php foreach ($success as $msg): ?>
        <div class="ok">✅ <?= $msg ?></div>
    <?php endforeach; ?>

    <?php foreach ($errors as $err): ?>
        <div class="err">❌ <?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <?php if (empty($errors)): ?>
        <p style="margin-top:1.5rem; color:#86efac;">
            ✔ Migrasi selesai tanpa error.<br>
            <a href="index.php?page=konfigurasi_umum">→ Buka halaman Konfigurasi Umum</a>
        </p>
    <?php endif; ?>
</body>
</html>
