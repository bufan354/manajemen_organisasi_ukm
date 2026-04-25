<?php
require_once __DIR__ . '/core/Session.php';
Session::requireSuperAdmin();
/**
 * Migration: Fitur Jabatan Kustom per UKM
 * 1. Buat tabel jabatan_kustom
 * 2. Ubah kolom anggota.hierarki dari ENUM ke VARCHAR(100)
 */
require_once 'core/Database.php';

$db = Database::getConnection();
$errors = [];
$success = [];

// --- 1. Buat tabel jabatan_kustom ---
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS jabatan_kustom (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            ukm_id       INT NOT NULL,
            nama_jabatan VARCHAR(100) NOT NULL,
            level        INT DEFAULT 4  COMMENT '1=Pembina, 2=Ketua, 3=BPH, 4=Koordinator/Divisi, 5=Anggota',
            created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
            UNIQUE KEY uk_ukm_jabatan (ukm_id, nama_jabatan)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $success[] = 'Tabel <code>jabatan_kustom</code> berhasil dibuat (atau sudah ada).';
} catch (PDOException $e) {
    $errors[] = 'Gagal membuat tabel jabatan_kustom: ' . $e->getMessage();
}

// --- 2. Ubah hierarki dari ENUM ke VARCHAR(100) ---
try {
    // Cek tipe kolom saat ini
    $stmt = $db->query("
        SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'anggota'
          AND COLUMN_NAME = 'hierarki'
    ");
    $colType = $stmt->fetchColumn();

    if (strtolower($colType) === 'enum') {
        $db->exec("
            ALTER TABLE anggota
            MODIFY COLUMN hierarki VARCHAR(100) NOT NULL DEFAULT 'Anggota'
        ");
        $success[] = 'Kolom <code>anggota.hierarki</code> berhasil diubah dari ENUM ke VARCHAR(100).';
    } else {
        $success[] = 'Kolom <code>anggota.hierarki</code> sudah VARCHAR, tidak perlu diubah.';
    }
} catch (PDOException $e) {
    $errors[] = 'Gagal mengubah kolom hierarki: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration: Jabatan Kustom</title>
    <style>
        body { font-family: monospace; background: #111; color: #eee; padding: 2rem; }
        h1 { color: #60a5fa; margin-bottom: 1rem; }
        .ok  { background: #052e16; border-left: 4px solid #22c55e; padding: .75rem 1rem; margin: .5rem 0; border-radius: 4px; }
        .err { background: #2d0000; border-left: 4px solid #ef4444; padding: .75rem 1rem; margin: .5rem 0; border-radius: 4px; }
        a { color: #60a5fa; }
    </style>
</head>
<body>
    <h1>🗄️ Migration: Jabatan Kustom per UKM</h1>

    <?php foreach ($success as $msg): ?>
        <div class="ok">✅ <?= $msg ?></div>
    <?php endforeach; ?>

    <?php foreach ($errors as $err): ?>
        <div class="err">❌ <?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <?php if (empty($errors)): ?>
        <p style="margin-top:1.5rem; color:#86efac;">
            ✔ Migrasi selesai tanpa error.<br>
            <a href="index.php?page=jabatan">→ Buka halaman Kelola Jabatan</a>
        </p>
    <?php endif; ?>
</body>
</html>
