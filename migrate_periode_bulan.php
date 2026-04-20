<?php
/**
 * Migration v3: Tambah kolom bulan di tabel periode
 */
require_once __DIR__ . '/core/Database.php';

$pdo = Database::getConnection();

echo "<pre>";
echo "=== MIGRASI: Tambah bulan di periode ===\n";

try {
    // Cek apakah bulan_mulai sudah ada
    $stmt = $pdo->query("SHOW COLUMNS FROM periode LIKE 'bulan_mulai'");
    if ($stmt->rowCount() > 0) {
        echo "[SKIP] Kolom 'bulan_mulai' sudah ada.\n";
    } else {
        $pdo->exec("ALTER TABLE periode ADD COLUMN bulan_mulai INT NULL DEFAULT NULL AFTER tahun_mulai");
        echo "[OK] Kolom 'bulan_mulai' ditambahkan.\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM periode LIKE 'bulan_selesai'");
    if ($stmt->rowCount() > 0) {
        echo "[SKIP] Kolom 'bulan_selesai' sudah ada.\n";
    } else {
        $pdo->exec("ALTER TABLE periode ADD COLUMN bulan_selesai INT NULL DEFAULT NULL AFTER tahun_selesai");
        echo "[OK] Kolom 'bulan_selesai' ditambahkan.\n";
    }

    echo "\n✅ MIGRASI SELESAI!\n";

} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
}
echo "</pre>";
