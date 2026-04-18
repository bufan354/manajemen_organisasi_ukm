<?php
require_once __DIR__ . '/../Database.php';

class LoginAttemptModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAttempt(string $ip): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
        return $stmt->fetch();
    }

    public function recordFailedAttempt(string $ip, string $email): void
    {
        $attempt = $this->getAttempt($ip);
        if ($attempt) {
            $newCount = $attempt['fail_count'] + 1;
            
            // Lock if failed 5 times
            $lockedUntil = null;
            if ($newCount >= 5) {
                // Lock for 15 minutes
                $lockedUntil = date('Y-m-d H:i:s', time() + (15 * 60));
            }
            
            $stmt = $this->db->prepare("UPDATE login_attempts SET fail_count = ?, last_attempt = NOW(), locked_until = ?, email = ? WHERE ip_address = ?");
            $stmt->execute([$newCount, $lockedUntil, $email, $ip]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO login_attempts (ip_address, email, fail_count, last_attempt) VALUES (?, ?, 1, NOW())");
            $stmt->execute([$ip, $email]);
        }
    }

    public function clearAttempt(string $ip): void
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
    }
}
