<?php
require_once __DIR__ . '/core/Session.php';
Session::requireSuperAdmin();
/**
 * Migration: Fingerprint Attendance System
 * - Creates fingerprint_pending table (replaces enroll_state.json)
 * - Adds UNIQUE constraint on absensi (event_id, anggota_id)
 * - Adds periode_id column to events if missing
 * 
 * Run: php migrate_fingerprint.php
 */
require_once __DIR__ . '/core/Database.php';

$db = Database::getConnection();

echo "=== Migrasi Sistem Absensi Sidik Jari ===\n\n";

// 1. Create fingerprint_pending table
echo "[1/4] Membuat tabel fingerprint_pending...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS fingerprint_pending (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        anggota_id  INT NOT NULL,
        ukm_id      INT NOT NULL,
        status      ENUM('pending','processing','done','failed','cancelled') DEFAULT 'pending',
        token       VARCHAR(64) NOT NULL UNIQUE,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at  TIMESTAMP NOT NULL,
        FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
        INDEX idx_status_expires (status, expires_at),
        INDEX idx_ukm_status (ukm_id, status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "     ✓ Tabel fingerprint_pending siap.\n";

// 2. Add UNIQUE constraint to absensi table (prevent double attendance)
echo "[2/4] Menambahkan UNIQUE constraint pada tabel absensi...\n";
try {
    // Check if the unique key already exists
    $stmt = $db->query("SHOW INDEX FROM absensi WHERE Key_name = 'unique_absensi'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE absensi ADD UNIQUE KEY unique_absensi (event_id, anggota_id)");
        echo "     ✓ UNIQUE constraint ditambahkan.\n";
    } else {
        echo "     ⊘ UNIQUE constraint sudah ada, skip.\n";
    }
} catch (PDOException $e) {
    echo "     ⚠ " . $e->getMessage() . "\n";
}

// 3. Add periode_id to events table if missing
echo "[3/4] Memeriksa kolom periode_id pada tabel events...\n";
try {
    $stmt = $db->query("SHOW COLUMNS FROM events LIKE 'periode_id'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE events ADD COLUMN periode_id INT DEFAULT NULL AFTER ukm_id");
        $db->exec("ALTER TABLE events ADD FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE SET NULL");
        echo "     ✓ Kolom periode_id ditambahkan.\n";
    } else {
        echo "     ⊘ Kolom periode_id sudah ada, skip.\n";
    }
} catch (PDOException $e) {
    echo "     ⚠ " . $e->getMessage() . "\n";
}

// 4. Cleanup old enroll_state.json
echo "[4/4] Membersihkan file enroll_state.json lama...\n";
$stateFile = __DIR__ . '/uploads/enroll_state.json';
if (file_exists($stateFile)) {
    @unlink($stateFile);
    echo "     ✓ enroll_state.json dihapus.\n";
} else {
    echo "     ⊘ File tidak ditemukan, skip.\n";
}

echo "\n=== Migrasi selesai! ===\n";
