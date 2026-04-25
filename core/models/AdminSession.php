<?php
require_once __DIR__ . '/../Database.php';

class AdminSessionModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Insert a new session record
     */
    public function insertSession(int $adminId, string $sessionId, string $ipAddress, string $userAgent): void
    {
        $sql = "INSERT INTO admin_sessions (admin_id, session_id, login_time, last_activity, ip_address, user_agent)
                VALUES (:admin_id, :session_id, NOW(), NOW(), :ip_address, :user_agent)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':admin_id'   => $adminId,
            ':session_id' => $sessionId,
            ':ip_address' => substr((string)$ipAddress, 0, 45),
            ':user_agent' => $userAgent
        ]);
    }

    /**
     * Update last_activity for a session
     */
    public function updateActivity(string $sessionId): void
    {
        $sql = "UPDATE admin_sessions SET last_activity = NOW() WHERE session_id = :session_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':session_id' => $sessionId]);
    }

    /**
     * Delete a session by its session_id
     */
    public function revokeSession(string $sessionId): void
    {
        $sql = "DELETE FROM admin_sessions WHERE session_id = :session_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':session_id' => $sessionId]);
    }

    /**
     * Get all active sessions for a specific admin user
     */
    public function getSessionsByAdminId(int $adminId): array
    {
        $sql = "SELECT * FROM admin_sessions WHERE admin_id = :admin_id ORDER BY last_activity DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':admin_id' => $adminId]);
        return $stmt->fetchAll();
    }

    /**
     * Check if a specific session has expired (inactive > 30 mins)
     */
    public function isSessionExpired(string $sessionId): bool
    {
        // Use MySQL native calculation to avoid timezone mismatch between PHP and MySQL
        $sql = "SELECT (TIMESTAMPDIFF(SECOND, last_activity, NOW()) > 1800) as is_expired 
                FROM admin_sessions WHERE session_id = :session_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':session_id' => $sessionId]);
        $result = $stmt->fetch();

        if (!$result) return true; // No session record found

        return (bool)$result['is_expired'];
    }

    /**
     * Clean up all expired sessions for all users proactively 
     */
    public function clearExpiredSessions(): void
    {
        $sql = "DELETE FROM admin_sessions WHERE last_activity < (NOW() - INTERVAL 30 MINUTE)";
        $this->db->exec($sql);
    }
}
