<?php
require_once __DIR__ . '/core/Session.php';
Session::requireSuperAdmin();
require_once __DIR__ . '/core/Database.php';

$pdo = Database::getConnection();

$sql = "CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    login_time DATETIME NOT NULL,
    last_activity DATETIME NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX (session_id),
    INDEX (last_activity)
) ENGINE=InnoDB;";

$pdo->exec($sql);
echo "Tabel admin_sessions berhasil dibuat.\n";
