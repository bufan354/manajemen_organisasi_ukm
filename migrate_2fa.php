<?php
require_once __DIR__ . '/core/Session.php';
Session::requireSuperAdmin();
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getConnection();
    
    // 1. Tambah kolom 2FA di tabel admins jika belum ada
    $checkCols = $db->query("SHOW COLUMNS FROM admins LIKE 'totp_secret'")->fetchAll();
    if (empty($checkCols)) {
        $db->exec("ALTER TABLE admins ADD COLUMN totp_secret VARCHAR(255) NULL AFTER password");
        $db->exec("ALTER TABLE admins ADD COLUMN is_2fa_active TINYINT(1) DEFAULT 0 AFTER totp_secret");
        echo "Kolom 2FA berhasil ditambahkan ke tabel admins.<br>";
    } else {
        echo "Kolom 2FA sudah ada.<br>";
    }

    // 2. Buat tabel login_attempts jika belum ada
    $db->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(150) NULL,
            fail_count INT DEFAULT 1,
            last_attempt DATETIME NOT NULL,
            locked_until DATETIME NULL,
            INDEX idx_ip (ip_address)
        ) ENGINE=InnoDB;
    ");
    echo "Tabel login_attempts berhasil dibuat/sudah ada.<br>";

    // Update login_attempts struct just in case we need index on email
    $db->exec("ALTER TABLE admins MODIFY is_2fa_active TINYINT(1) DEFAULT 0;"); // make sure

    echo "<h3 style='color:green'>MIGRASI 2FA SELESAI Sempurna.</h3>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>Error Migrasi: " . $e->getMessage() . "</h3>";
}
