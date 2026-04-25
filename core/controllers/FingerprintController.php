<?php
require_once __DIR__ . '/../models/Anggota.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../Database.php';

/**
 * Controller: Fingerprint -> GLOBAL DEVICE ARCHITECTURE
 * - 1 ESP32 device serves all UKMs
 * - 'action' column dictates "enroll" or "delete" modes
 */
class FingerprintController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        // Models are initialized here if needed locally, 
        // but session guards must be per-method to allow public API access
    }

    // ============================================================
    // API ENDPOINTS (dipanggil ESP32 via /api/fingerprint.php)
    // ============================================================

    public function registerApi(): void
    {
        header('Content-Type: application/json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $token = $data['token'] ?? null;
        $fingerprintId = isset($data['fingerprint_id']) ? (int)$data['fingerprint_id'] : null;

        if (!$token) {
             echo json_encode(['status' => 'error', 'message' => 'Missing token']);
             exit;
        }

        $stmt = $this->db->prepare(
            "SELECT id, anggota_id, ukm_id, token, action FROM fingerprint_pending WHERE token = ? AND action = 'enroll' AND status = 'pending' AND expires_at > NOW()"
        );
        $stmt->execute([$token]);
        $pending = $stmt->fetch();

        if (!$pending) {
            echo json_encode(['status' => 'error', 'message' => 'Token tidak valid atau sudah kedaluwarsa']);
            exit;
        }

        if (!$fingerprintId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing fingerprint_id']);
            exit;
        }

        $anggotaId = $pending['anggota_id'];

        if ($anggotaId) {
            $stmt = $this->db->prepare(
                "UPDATE anggota 
                 SET fingerprint_id = ?, 
                     fingerprint_registered_at = IFNULL(fingerprint_registered_at, NOW()), 
                     fingerprint_updated_at = NOW() 
                 WHERE id = ?"
            );
            $success = $stmt->execute([$fingerprintId, $anggotaId]);
        } else {
            $success = false;
        }

        if ($success) {
            $stmt = $this->db->prepare("UPDATE fingerprint_pending SET status = 'done' WHERE token = ?");
            $stmt->execute([$token]);
            echo json_encode(['status' => 'success', 'fingerprint_id' => $fingerprintId]);
        } else {
            $stmt = $this->db->prepare("UPDATE fingerprint_pending SET status = 'failed' WHERE token = ?");
            $stmt->execute([$token]);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
        }
        exit;
    }

    public function verifyApi(): void
    {
        header('Content-Type: application/json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $fingerprintId = isset($data['fingerprint_id']) ? (int)$data['fingerprint_id'] : null;
        
        if ($fingerprintId === null) {
            echo json_encode(['status' => 'error', 'message' => 'Missing fingerprint_id']);
            exit;
        }

        // 3. Lanjut ke absensi ANGGOTA
        $stmt = $this->db->prepare("SELECT id, nama, ukm_id FROM anggota WHERE fingerprint_id = ? AND status = 'aktif'");
        $stmt->execute([$fingerprintId]);
        $anggota = $stmt->fetch();

        if (!$anggota) {
            echo json_encode(['status' => 'not_found', 'message' => 'Jari tidak dikenal']);
            exit;
        }

        $stmt = $this->db->prepare(
            "SELECT id FROM events 
             WHERE ukm_id = ? AND status_absensi = 1 
             AND waktu_mulai <= NOW() AND (waktu_selesai IS NULL OR waktu_selesai >= NOW()) 
             ORDER BY waktu_mulai DESC LIMIT 1"
        );
        $stmt->execute([$anggota['ukm_id']]);
        $event = $stmt->fetch();

        if ($event) {
            require_once __DIR__ . '/../models/Kehadiran.php';
            $kehadiran = new Kehadiran();
            $result = $kehadiran->recordAttendance($event['id'], $anggota['id'], 'fingerprint');
            
            if ($result === 'already') {
                echo json_encode([
                    'status'     => 'already',
                    'anggota_id' => $anggota['id'],
                    'nama'       => $anggota['nama'],
                    'message'    => 'Sudah tercatat hadir di event ini'
                ]);
            } else {
                echo json_encode([
                    'status'     => 'matched', 
                    'anggota_id' => $anggota['id'], 
                    'nama'       => $anggota['nama'],
                    'message'    => 'Kehadiran berhasil dicatat'
                ]);
            }
        } else {
            echo json_encode([
                'status'     => 'no_event', 
                'nama'       => $anggota['nama'],
                'message'    => 'Tidak ada event aktif untuk UKM ini'
            ]);
        }
        exit;
    }

    public function deleteApi(): void
    {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $token = $data['token'] ?? null;
        $fingerprintId = isset($data['fingerprint_id']) ? (int)$data['fingerprint_id'] : null;

        if (!$token || !$fingerprintId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing token or fingerprint_id']);
            exit;
        }

        $stmt = $this->db->prepare(
            "SELECT anggota_id FROM fingerprint_pending WHERE token = ? AND action = 'delete' AND status = 'pending' AND expires_at > NOW()"
        );
        $stmt->execute([$token]);
        $pending = $stmt->fetch();

        if ($pending) {
            $anggotaId = $pending['anggota_id'];

            if ($anggotaId) {
                $stmt = $this->db->prepare(
                    "UPDATE anggota 
                     SET fingerprint_id = NULL, fingerprint_template = NULL, fingerprint_updated_at = NOW() 
                     WHERE id = ?"
                );
                $stmt->execute([$anggotaId]);
            }

            $stmt = $this->db->prepare("UPDATE fingerprint_pending SET status = 'done' WHERE token = ?");
            $stmt->execute([$token]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Token invalid or expired']);
        }
        exit;
    }

    public function getModeApi(): void
    {
        header('Content-Type: application/json');

        // Auto-cleanup expired pending records
        $this->db->exec("UPDATE fingerprint_pending SET status = 'cancelled' WHERE status = 'pending' AND expires_at <= NOW()");

        // 1. Cek enroll/delete pending dari SEMUA UKM -> PRIORITAS UTAMA
        $query = "SELECT id, anggota_id, action, fingerprint_id, token FROM fingerprint_pending 
                  WHERE status = 'pending' AND expires_at > NOW() 
                  ORDER BY created_at ASC LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $pending = $stmt->fetch();

        if ($pending) {
            $fingerprintId = (int)$pending['fingerprint_id'];
            $targetId = 0;
            $userType = 'anggota';

            if ($pending['anggota_id']) {
                $targetId = (int)$pending['anggota_id'];
            }

            echo json_encode([
                'mode'           => $pending['action'], // 'enroll' or 'delete'
                'target_id'      => $targetId,
                'user_type'      => $userType,
                'fingerprint_id' => $fingerprintId,
                'token'          => $pending['token']
            ]);
            exit;
        }

        // Pengecekan verifikasi BACKUP (ADMIN) via sidik jari dihapus untuk penyederhanaan.


        require_once __DIR__ . '/../models/Event.php';
        $eventModel = new Event();
        
        // Selalu jalankan generator rutin agar tidak ada delay saat ESP polling pertama kali di hari HP
        // Generator internal sudah punya proteksi cek apakah data sudah ada (JIT), jadi aman dipanggil setiap saat.
        $eventModel->generateRoutineEvents();


        // 2. Cek event aktif saat ini untuk SEMUA UKM
        $stmt = $this->db->query(
            "SELECT id FROM events 
             WHERE status_absensi = 1 
             AND waktu_mulai <= NOW() AND (waktu_selesai IS NULL OR waktu_selesai >= NOW()) 
             LIMIT 1"
        );
        $activeEvent = $stmt->fetch();

        if ($activeEvent) {
            echo json_encode([
                'mode' => 'verify'
            ]);
            exit;
        }

        // 3. Standby
        echo json_encode(['mode' => 'standby']);
        exit;
    }


    // ============================================================
    // AJAX ENDPOINTS (dipanggil Web Admin via index.php)
    // ============================================================

    public function checkStatusAjax(): void
    {
        header('Content-Type: application/json');
        Session::requireLogin();
        
        // Update session activity for web requests
        require_once __DIR__ . '/../models/AdminSession.php';
        (new AdminSessionModel())->updateActivity(session_id());

        $id = (int)($_GET['id'] ?? 0);
        $since = (int)($_GET['since'] ?? 0);

        if ($id > 0) {
            $stmt = $this->db->prepare(
                "SELECT status FROM fingerprint_pending 
                 WHERE anggota_id = ? AND status = 'done' 
                 AND created_at >= FROM_UNIXTIME(?) 
                 ORDER BY created_at DESC LIMIT 1"
            );
            $stmt->execute([$id, $since]);
            $pending = $stmt->fetch();

            if ($pending) {
                echo json_encode(['status' => 'success', 'updated' => true]);
                exit;
            }

            $stmt = $this->db->prepare("SELECT UNIX_TIMESTAMP(fingerprint_updated_at) AS updated_ts FROM anggota WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            if ($row && $row['updated_ts'] >= $since && $row['updated_ts'] > 0) {
                echo json_encode(['status' => 'success', 'updated' => true]);
                exit;
            }
        }
        
        echo json_encode(['status' => 'success', 'updated' => false]);
        exit;
    }

    public function setEnrollModeAjax(): void
    {
        header('Content-Type: application/json');
        Session::requireLogin();
        
        // Update session activity for web requests
        require_once __DIR__ . '/../models/AdminSession.php';
        (new AdminSessionModel())->updateActivity(session_id());
        
        $anggotaId = (int)($_POST['anggota_id'] ?? 0);
        $adminId   = (int)($_POST['admin_id'] ?? 0);

        if ($anggotaId > 0) {
            $stmt = $this->db->prepare("SELECT ukm_id FROM anggota WHERE id = ?");
            $stmt->execute([$anggotaId]);
            $anggota = $stmt->fetch();

            if (!$anggota) {
                echo json_encode(['status' => 'error', 'message' => 'Anggota tidak ditemukan']);
                exit;
            }
            $ukmId = (int)$anggota['ukm_id'];
            
            $stmt = $this->db->prepare("UPDATE fingerprint_pending SET status = 'cancelled' WHERE anggota_id = ? AND status = 'pending'");
            $stmt->execute([$anggotaId]);

            $token = bin2hex(random_bytes(16));
            $stmt = $this->db->prepare("INSERT INTO fingerprint_pending (anggota_id, ukm_id, token, action, expires_at) VALUES (?, ?, ?, 'enroll', DATE_ADD(NOW(), INTERVAL 120 SECOND))");
            $stmt->execute([$anggotaId, $ukmId, $token]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Layanan sidik jari untuk SuperAdmin telah dinonaktifkan. Silakan hubungi pengelola sistem jika ini adalah kesalahan.']);
            exit;
        }

        echo json_encode(['status' => 'success', 'token' => $token]);
        exit;
    }

    public function setDeleteModeAjax(): void
    {
        header('Content-Type: application/json');
        try {
            Session::requireLogin();
            
            $anggotaId = (int)($_POST['anggota_id'] ?? 0);
            $adminId   = (int)($_POST['admin_id'] ?? 0);
            $token     = bin2hex(random_bytes(16));

            if ($anggotaId > 0) {
                $stmt = $this->db->prepare("SELECT ukm_id, fingerprint_id FROM anggota WHERE id = ?");
                $stmt->execute([$anggotaId]);
                $anggota = $stmt->fetch();

                if (!$anggota || empty($anggota['fingerprint_id'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Anggota tidak memiliki sidik jari terdaftar']);
                    exit;
                }
                $ukmId = (int)$anggota['ukm_id'];

                // 1. Bersihkan di Database Web (Instan)
                $stmt = $this->db->prepare("UPDATE anggota SET fingerprint_id = NULL, fingerprint_template = NULL, fingerprint_updated_at = NOW() WHERE id = ?");
                $stmt->execute([$anggotaId]);

                // 2. Batalkan antrean pending lama jika ada
                $this->db->prepare("UPDATE fingerprint_pending SET status = 'cancelled' WHERE anggota_id = ? AND status = 'pending'")->execute([$anggotaId]);

                // 3. Jadwalkan penghapusan ke ESP32 (Simpan ID Fisik agar tidak hilang)
                $stmt = $this->db->prepare("INSERT INTO fingerprint_pending (anggota_id, ukm_id, fingerprint_id, token, action, expires_at) VALUES (?, ?, ?, ?, 'delete', DATE_ADD(NOW(), INTERVAL 120 SECOND))");
                $stmt->execute([$anggotaId, $ukmId, $anggota['fingerprint_id'], $token]);

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Layanan sidik jari untuk SuperAdmin telah dinonaktifkan.']);
                exit;
            }

            echo json_encode(['status' => 'success', 'token' => $token]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'SQL/Server Error: ' . $e->getMessage()]);
            exit;
        } catch (Error $e) {
            echo json_encode(['status' => 'error', 'message' => 'Fatal Error: ' . $e->getMessage()]);
            exit;
        }
    }

    public function cancelEnrollAjax(): void
    {
        header('Content-Type: application/json');
        Session::requireLogin();
        
        $anggotaId = (int)($_POST['anggota_id'] ?? 0);

        if ($anggotaId > 0) {
            $stmt = $this->db->prepare("UPDATE fingerprint_pending SET status = 'cancelled' WHERE anggota_id = ? AND status = 'pending'");
            $stmt->execute([$anggotaId]);
        } else {
            $ukmId = (int)Session::get('ukm_id');
            if ($ukmId > 0) {
                // Cancel ONLY for this UKM, not breaking the global device if others are enrolling
                $stmt = $this->db->prepare(
                    "UPDATE fingerprint_pending SET status = 'cancelled' WHERE ukm_id = ? AND status = 'pending'"
                );
                $stmt->execute([$ukmId]);
            }
        }

        echo json_encode(['status' => 'success']);
        exit;
    }
}
