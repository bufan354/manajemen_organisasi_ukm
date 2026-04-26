<?php
require_once __DIR__ . '/../Database.php';

/**
 * Model: Pendaftaran
 * CRUD untuk registrasi calon anggota via halaman publik
 */
class Pendaftaran
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ambil semua pendaftaran (opsional filter per UKM dan Periode) */
    public function getAll(?int $ukmId = null, ?int $periodeId = null): array
    {
        $query = "SELECT p.*, u.singkatan AS ukm_nama 
                 FROM pendaftaran p
                 LEFT JOIN ukm u ON p.ukm_id = u.id
                 WHERE p.deleted_at IS NULL";
        $params = [];
        $conditions = [];

        if ($ukmId) {
            $conditions[] = "p.ukm_id = ?";
            $params[] = $ukmId;
        }
        if ($periodeId) {
            $conditions[] = "p.periode_id = ?";
            $params[] = $periodeId;
        }

        if (count($conditions) > 0) {
            $query .= " AND " . implode(" AND ", $conditions);
        }
        $query .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Ambil pendaftaran by ID */
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM pendaftaran WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Cek apakah email sudah terdaftar di UKM tsb dengan status aktif/tunggu */
    public function isDuplicate(string $email, int $ukmId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM pendaftaran WHERE email = ? AND ukm_id = ? AND status IN ('pending', 'diterima')");
        $stmt->execute([$email, $ukmId]);
        return (bool)$stmt->fetch();
    }

    /** Tambah pendaftaran baru (dari form publik) */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pendaftaran (ukm_id, periode_id, nama, email, no_wa, kelas, jurusan, jawaban_kuisioner, alasan, status, session_id) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)"
        );
        $stmt->execute([
            $data['ukm_id'],
            $data['periode_id'] ?? null,  // null-safe: jika tidak ada periode aktif
            $data['nama'],
            $data['email'],
            $data['no_wa'],
            $data['kelas'] ?? null,
            $data['jurusan'] ?? null,
            $data['jawaban_kuisioner'] ?? null,
            $data['alasan'] ?? null,
            $data['session_id'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Ambil riwayat / status pendaftaran terbaru berdasarkan session browser */
    public function getLatestBySession(string $sessionId, int $ukmId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM pendaftaran WHERE session_id = ? AND ukm_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$sessionId, $ukmId]);
        return $stmt->fetch();
    }

    /** Melepas ikatan pendaftaran dari session pelacak agar browser bisa dipakai mendaftar lagi */
    public function clearSessionLink(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE pendaftaran SET session_id = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Update status pendaftaran (terima/tolak) */
    public function updateStatus(int $id, string $status, ?string $alasanTolak = null): bool
    {
        $stmt = $this->db->prepare("UPDATE pendaftaran SET status = ?, alasan_penolakan = ? WHERE id = ?");
        return $stmt->execute([$status, $alasanTolak, $id]);
    }

    /** Hapus pendaftaran (Soft Delete) */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE pendaftaran SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Simpan jawaban kuisioner secara individual ke tabel jawaban */
    public function createAnswer(int $pendaftaranId, string $pertanyaan, string $jawaban): bool
    {
        $stmt = $this->db->prepare("INSERT INTO pendaftaran_jawaban (pendaftaran_id, pertanyaan_teks, jawaban_teks) VALUES (?, ?, ?)");
        return $stmt->execute([$pendaftaranId, $pertanyaan, $jawaban]);
    }

    /** Ambil semua jawaban kuisioner untuk pendaftaran tertentu */
    public function getAnswers(int $pendaftaranId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pendaftaran_jawaban WHERE pendaftaran_id = ?");
        $stmt->execute([$pendaftaranId]);
        return $stmt->fetchAll();
    }

    /** Hitung total pendaftaran (excluding deleted) */
    public function count(?int $ukmId = null, ?string $status = null): int
    {
        $sql = "SELECT COUNT(*) FROM pendaftaran WHERE deleted_at IS NULL";
        $params = [];

        if ($ukmId) {
            $sql .= " AND ukm_id = ?";
            $params[] = $ukmId;
        }
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
