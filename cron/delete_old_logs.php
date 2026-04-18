<?php
/**
 * Cron Job Script: Hapus Log Keamanan Usang
 * Dieksekusi otomatis menggunakan cron job server (misal: 0 1 * * * php /var/www/html/absensi/cron/delete_old_logs.php)
 */

require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    
    // Hapus data yang berumur lebih dari 7 hari
    $sql = "DELETE FROM log_keamanan WHERE waktu < NOW() - INTERVAL 7 DAY";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $deletedCount = $stmt->rowCount();
    echo "[" . date('Y-m-d H:i:s') . "] Sukses menghapus {$deletedCount} baris log usang.\n";
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error pembersihan log: " . $e->getMessage() . "\n";
}
