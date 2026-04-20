<?php
/**
 * Migration: Perbaiki struktur tabel pendaftaran
 * Menambahkan kolom yang mungkin belum ada di server produksi.
 *
 * INSTRUKSI: Akses via browser di server:
 *   https://ukminstbunas.cloud/migrate_pendaftaran.php
 * Hapus file ini setelah berhasil dijalankan!
 */

require_once __DIR__ . '/core/Database.php';

$pdo = Database::getConnection();

echo "<pre style='font-family:monospace;font-size:14px;'>";
echo "=== MIGRASI TABEL PENDAFTARAN ===\n\n";

// --------------------------------------------------------
// Helper: Cek apakah kolom sudah ada
// --------------------------------------------------------
function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME   = ?
           AND COLUMN_NAME  = ?"
    );
    $stmt->execute([$table, $column]);
    return (bool) $stmt->fetchColumn();
}

// --------------------------------------------------------
// Helper: Cek apakah tabel sudah ada
// --------------------------------------------------------
function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME   = ?"
    );
    $stmt->execute([$table]);
    return (bool) $stmt->fetchColumn();
}

$errors = [];

// --------------------------------------------------------
// 1. Pastikan tabel pendaftaran ada
// --------------------------------------------------------
if (!tableExists($pdo, 'pendaftaran')) {
    echo "[CREATE] Tabel 'pendaftaran' tidak ditemukan, membuat...\n";
    $pdo->exec("
        CREATE TABLE pendaftaran (
            id                 INT AUTO_INCREMENT PRIMARY KEY,
            ukm_id             INT          NOT NULL,
            periode_id         INT          NOT NULL DEFAULT 0,
            nama               VARCHAR(150) NOT NULL,
            email              VARCHAR(150) NOT NULL,
            no_wa              VARCHAR(20)  NOT NULL,
            kelas              VARCHAR(20)  DEFAULT NULL,
            jurusan            VARCHAR(100) DEFAULT NULL,
            jawaban_kuisioner  TEXT         DEFAULT NULL,
            alasan             TEXT         DEFAULT NULL,
            status             ENUM('pending','diterima','ditolak') DEFAULT 'pending',
            session_id         VARCHAR(255) DEFAULT NULL,
            alasan_penolakan   TEXT         DEFAULT NULL,
            created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK]     Tabel 'pendaftaran' berhasil dibuat.\n\n";
} else {
    echo "[OK]     Tabel 'pendaftaran' sudah ada.\n\n";
}

// --------------------------------------------------------
// 2. Tambahkan kolom yang mungkin belum ada
// --------------------------------------------------------
$migrations = [
    // [kolom, definisi SQL, deskripsi]
    ['kelas',             "VARCHAR(20)  DEFAULT NULL AFTER no_wa",                           "Kolom 'kelas'"],
    ['jurusan',           "VARCHAR(100) DEFAULT NULL AFTER kelas",                           "Kolom 'jurusan'"],
    ['jawaban_kuisioner', "TEXT         DEFAULT NULL AFTER jurusan",                         "Kolom 'jawaban_kuisioner' (untuk pertanyaan custom)"],
    ['alasan',            "TEXT         DEFAULT NULL AFTER jawaban_kuisioner",               "Kolom 'alasan' (motivasi)"],
    ['session_id',        "VARCHAR(255) DEFAULT NULL AFTER status",                          "Kolom 'session_id' (tracking pendaftar)"],
    ['alasan_penolakan',  "TEXT         DEFAULT NULL AFTER session_id",                      "Kolom 'alasan_penolakan'"],
];

foreach ($migrations as [$col, $definition, $label]) {
    if (!columnExists($pdo, 'pendaftaran', $col)) {
        try {
            $pdo->exec("ALTER TABLE pendaftaran ADD COLUMN `{$col}` {$definition}");
            echo "[ADDED]  {$label} berhasil ditambahkan.\n";
        } catch (PDOException $e) {
            $msg = "[ERROR]  Gagal menambahkan {$label}: " . $e->getMessage();
            echo $msg . "\n";
            $errors[] = $msg;
        }
    } else {
        echo "[SKIP]   {$label} sudah ada, dilewati.\n";
    }
}

// --------------------------------------------------------
// 3. Cek & fix kolom periode_id (mungkin NOT NULL tanpa default)
// --------------------------------------------------------
echo "\n--- Memverifikasi struktur kolom kritis ---\n";
$stmt = $pdo->query("DESCRIBE pendaftaran");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo sprintf("  %-25s %s  %s  %s\n",
        $col['Field'],
        $col['Type'],
        $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL',
        $col['Default'] !== null ? "DEFAULT '{$col['Default']}'" : ''
    );
}

// --------------------------------------------------------
// 4. Cek tabel notifikasi (perlu untuk addNotifikasi())
// --------------------------------------------------------
echo "\n--- Verifikasi tabel notifikasi ---\n";
if (!tableExists($pdo, 'notifikasi')) {
    echo "[WARN]   Tabel 'notifikasi' BELUM ADA! Membuat...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifikasi (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            user_id    INT NOT NULL,
            ukm_id     INT NULL,
            jenis      VARCHAR(50)  NOT NULL,
            judul      VARCHAR(255) NOT NULL,
            pesan      TEXT         NOT NULL,
            link       VARCHAR(255) NULL,
            is_dibaca  BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES admins(id) ON DELETE CASCADE,
            INDEX (user_id, is_dibaca, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK]     Tabel 'notifikasi' berhasil dibuat.\n";
} else {
    echo "[OK]     Tabel 'notifikasi' sudah ada.\n";
}

// --------------------------------------------------------
// Summary
// --------------------------------------------------------
echo "\n========================================\n";
if (empty($errors)) {
    echo "✅ MIGRASI SELESAI TANPA ERROR!\n";
    echo "   Coba lagi submit form pendaftaran.\n";
} else {
    echo "⚠️  MIGRASI SELESAI DENGAN " . count($errors) . " ERROR.\n";
    echo "   Periksa pesan error di atas.\n";
}
echo "========================================\n";
echo "\n⚠️  PENTING: Hapus file ini dari server setelah selesai!\n";
echo "   rm /path/to/migrate_pendaftaran.php\n";
echo "</pre>";
