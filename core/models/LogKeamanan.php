<?php
require_once __DIR__ . '/../Database.php';

class LogKeamananModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Catat aktivitas keamanan baru
     */
    public function insert(string $aktivitas, ?int $userId, ?string $ipAddress, ?string $userAgent, ?string $detail): bool
    {
        $sql = "INSERT INTO log_keamanan (aktivitas, user_id, ip_address, user_agent, detail) 
                VALUES (:aktivitas, :user_id, :ip_address, :user_agent, :detail)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':aktivitas'  => $aktivitas,
            ':user_id'    => $userId,
            ':ip_address' => $ipAddress,
            ':user_agent' => $userAgent,
            ':detail'     => $detail
        ]);
    }

    /**
     * Ambil data log dengan paginasi dan filter
     */
    public function getAllPaginated(int $limit, int $offset, string $search = '', string $startDate = '', string $endDate = ''): array
    {
        $sql = "SELECT l.*, a.email, a.nama as user_nama 
                FROM log_keamanan l 
                LEFT JOIN admins a ON l.user_id = a.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (l.aktivitas LIKE :search OR a.email LIKE :search OR l.ip_address LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($startDate)) {
            $sql .= " AND DATE(l.waktu) >= :start_date";
            $params[':start_date'] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND DATE(l.waktu) <= :end_date";
            $params[':end_date'] = $endDate;
        }
        
        $sql .= " ORDER BY l.waktu DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        // Pdo bind param for limits
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hitung total log untuk paginasi
     */
    public function countTotal(string $search = '', string $startDate = '', string $endDate = ''): int
    {
        $sql = "SELECT COUNT(*) FROM log_keamanan l LEFT JOIN admins a ON l.user_id = a.id WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (l.aktivitas LIKE :search OR a.email LIKE :search OR l.ip_address LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if (!empty($startDate)) {
            $sql .= " AND DATE(l.waktu) >= :start_date";
            $params[':start_date'] = $startDate;
        }
        if (!empty($endDate)) {
            $sql .= " AND DATE(l.waktu) <= :end_date";
            $params[':end_date'] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Mengambil seluruh data berdasarkan filter untuk Export
     */
    public function getForExport(string $search = '', string $startDate = '', string $endDate = ''): array
    {
        $sql = "SELECT l.id, l.waktu, l.aktivitas, a.email as user_email, a.nama as user_nama, l.ip_address, l.user_agent, l.detail 
                FROM log_keamanan l 
                LEFT JOIN admins a ON l.user_id = a.id 
                WHERE 1=1";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (l.aktivitas LIKE :search OR a.email LIKE :search OR l.ip_address LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if (!empty($startDate)) {
            $sql .= " AND DATE(l.waktu) >= :start_date";
            $params[':start_date'] = $startDate;
        }
        if (!empty($endDate)) {
            $sql .= " AND DATE(l.waktu) <= :end_date";
            $params[':end_date'] = $endDate;
        }
        
        $sql .= " ORDER BY l.waktu DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
