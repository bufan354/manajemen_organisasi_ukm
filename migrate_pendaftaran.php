<?php
/**
 * Migration v2: Fix kolom periode_id di tabel pendaftaran
 * Ubah periode_id menjadi nullable (drop FK sementara jika perlu)
 * agar pendaftaran bisa submit meski UKM belum punya periode aktif.
 *
 * INSTRUKSI: Akses via browser:
 *   https://ukminstbunas.cloud/migrate_pendaftaran.php
 * Hapus file ini setelah selesai!
 */

require_once __DIR__ . '/core/Database.php';

$pdo = Database::getConnection();

echo "<pre style='font-family:monospace;font-size:14px;'>";
echo "=== MIGRASI FIX: periode_id di tabel pendaftaran ===\n\n";

try {
    // Cek apakah periode_id sudah nullable
    $stmt = $pdo->prepare(
        "SELECT IS_NULLABLE, COLUMN_TYPE
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'pendaftaran'
           AND COLUMN_NAME = 'periode_id'"
    );
    $stmt->execute();
    $colInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Status sekarang:\n";
    echo "  IS_NULLABLE : " . ($colInfo['IS_NULLABLE'] ?? 'N/A') . "\n";
    echo "  COLUMN_TYPE : " . ($colInfo['COLUMN_TYPE'] ?? 'N/A') . "\n\n";

    if ($colInfo['IS_NULLABLE'] === 'YES') {
        echo "[SKIP] Kolom 'periode_id' sudah nullable, tidak perlu diubah.\n";
    } else {
        echo "[FIX]  Mengubah 'periode_id' menjadi nullable...\n";

        // Nonaktifkan FK checks sementara
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // Ubah kolom jadi nullable, hapus NOT NULL
        $pdo->exec("ALTER TABLE pendaftaran MODIFY COLUMN periode_id INT DEFAULT NULL");

        // Aktifkan kembali FK checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        echo "[OK]   Kolom 'periode_id' berhasil diubah menjadi nullable (INT DEFAULT NULL).\n";
    }

    // Cek ulang
    $stmt->execute();
    $colInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nStatus setelah migrasi:\n";
    echo "  IS_NULLABLE : " . ($colInfo['IS_NULLABLE'] ?? 'N/A') . "\n";
    echo "  COLUMN_TYPE : " . ($colInfo['COLUMN_TYPE'] ?? 'N/A') . "\n";

    echo "\n========================================\n";
    echo "✅ SELESAI! Sekarang coba submit form pendaftaran.\n";
    echo "========================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
}

echo "\n⚠️  PENTING: Hapus file ini dari server setelah selesai!\n";
echo "</pre>";
