<?php
require_once __DIR__ . '/../models/Notifikasi.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Notifikasi
 */
class NotifikasiController
{
    private NotifikasiModel $model;

    public function __construct()
    {
        $this->model = new NotifikasiModel();
    }

    /**
     * Polling endpoint to fetch latest notifications and unread count
     */
    public function poll(): void
    {
        Session::requireLogin();
        header('Content-Type: application/json');

        $userId = (int)Session::get('admin_id');
        if ($userId <= 0) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $unreadCount = $this->model->getUnreadCount($userId);
        $notifications = $this->model->getAllByUser($userId, 10);

        // Sanitize strings manually for JSON output to prevent XSS in rendering
        $cleanNotifications = [];
        foreach ($notifications as $n) {
            $cleanNotifications[] = [
                'id' => $n['id'],
                'jenis' => h($n['jenis']),
                'judul' => h($n['judul']),
                'pesan' => h($n['pesan']),
                'link' => $n['link'] ? h($n['link']) : '#',
                'is_dibaca' => (bool)$n['is_dibaca'],
                'created_at' => $n['created_at'],
                // Buat versi relative time sederhana
                'waktu' => $this->timeElapsedString($n['created_at'])
            ];
        }

        echo json_encode([
            'unread_count' => $unreadCount,
            'notifications' => $cleanNotifications
        ]);
        exit;
    }

    /**
     * Mark a specific notification as read
     */
    public function read(): void
    {
        Session::requireLogin();
        header('Content-Type: application/json');

        $userId = (int)Session::get('admin_id');
        $id = (int)($_POST['id'] ?? json_decode(file_get_contents('php://input'), true)['id'] ?? 0);

        if ($userId > 0 && $id > 0) {
            $success = $this->model->markAsRead($id, $userId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        }
        exit;
    }

    /**
     * Mark all notifications as read
     */
    public function readAll(): void
    {
        Session::requireLogin();
        header('Content-Type: application/json');

        $userId = (int)Session::get('admin_id');
        if ($userId > 0) {
            $success = $this->model->markAllAsRead($userId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Delete all notifications for the logged-in user
     */
    public function deleteAll(): void
    {
        Session::requireLogin();
        header('Content-Type: application/json');

        $userId = (int)Session::get('admin_id');
        if ($userId > 0) {
            $success = $this->model->deleteAll($userId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Delete a specific notification
     */
    public function delete(): void
    {
        Session::requireLogin();
        header('Content-Type: application/json');

        $userId = (int)Session::get('admin_id');
        $id = (int)($_POST['id'] ?? json_decode(file_get_contents('php://input'), true)['id'] ?? 0);

        if ($userId > 0 && $id > 0) {
            $success = $this->model->delete($id, $userId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        }
        exit;
    }

    /**
     * Helper to show relative time
     */
    private function timeElapsedString(string $datetime, bool $full = false): string
    {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' lalu' : 'baru saja';
    }
}
