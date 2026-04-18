<?php
/**
 * Database Connection Singleton (PDO)
 * Membaca kredensial dari file .env
 */
class Database
{
    private static ?PDO $instance = null;

    /** Mencegah instansiasi langsung */
    private function __construct() {}

    /**
     * Mendapatkan koneksi PDO (Singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $env = self::loadEnv();

            $host = $env['DB_HOST'] ?? 'localhost';
            $name = $env['DB_NAME'] ?? 'absensi_iot';
            $user = $env['DB_USER'] ?? 'root';
            $pass = $env['DB_PASS'] ?? '';
            $port = $env['DB_PORT'] ?? '3306';

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                self::$instance->exec("SET time_zone = '+07:00'");
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Parse file .env di root project
     */
    private static function loadEnv(): array
    {
        $envPath = __DIR__ . '/../.env';
        $env = [];

        if (!file_exists($envPath)) {
            die("File .env tidak ditemukan. Silakan buat file .env di root project.");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Lewati komentar
            if (str_starts_with(trim($line), '#')) continue;

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key   = trim($parts[0]);
                $value = trim($parts[1], " \t\n\r\0\x0B\"'");
                $env[$key] = $value;
            }
        }

        return $env;
    }
}
