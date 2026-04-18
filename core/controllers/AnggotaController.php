<?php
require_once __DIR__ . '/../models/Anggota.php';
require_once __DIR__ . '/../FileUpload.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Anggota
 * Handle CRUD operations untuk Anggota UKM
 */
class AnggotaController
{
    private Anggota $model;

    public function __construct()
    {
        $this->model = new Anggota();
    }

    /** Tambah anggota baru */
    public function store(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=anggota');
        }

        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=anggota');
        }

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? 0)
            : (int)Session::get('ukm_id');

        $data = [
            'ukm_id'    => $ukmId,
            'nama'      => sanitize($_POST['nama'] ?? ''),
            'nim'       => sanitize($_POST['nim'] ?? ''),
            'email'     => sanitize($_POST['email'] ?? ''),
            'hierarki'  => sanitize($_POST['hierarki'] ?? 'Anggota'),
            'jabatan'   => sanitize($_POST['jabatan'] ?? 'Anggota'),
            'status'    => sanitize($_POST['status'] ?? 'aktif'),
            'fingerprint_id' => null,
            'foto_path' => null,
        ];

        // Auto-inject periode_id: admin UKM → dari session, superadmin → dari periode aktif UKM
        if (Session::get('admin_role') === 'superadmin') {
            require_once 'core/models/Periode.php';
            $activePeriode = (new Periode())->getActive($ukmId);
            $data['periode_id'] = $activePeriode ? $activePeriode['id'] : 0;
        } else {
            $data['periode_id'] = (int)Session::get('periode_id');
        }

        if (FileUpload::hasFile('foto')) {
            $path = FileUpload::upload($_FILES['foto'], 'anggota');
            if ($path) $data['foto_path'] = $path;
        }

        $this->model->create($data);
        logSecurityActivity('Tambah Anggota', ['nama' => $data['nama'], 'nim' => $data['nim']]);
        setFlash('success', 'Anggota berhasil ditambahkan.');
        redirect('index.php?page=anggota');
    }

    /** Update anggota */
    public function update(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=anggota');
        }

        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=anggota');
        }

        $id = (int)($_POST['id'] ?? 0);
        $existing = $this->model->getById($id);
        if (!$existing) {
            setFlash('error', 'Anggota tidak ditemukan.');
            redirect('index.php?page=anggota');
        }

        if (Session::get('admin_role') !== 'superadmin' && (int)$existing['ukm_id'] !== (int)Session::get('ukm_id')) {
            setFlash('error', 'Akses ditolak: Anggota bukan dari UKM Anda.');
            redirect('index.php?page=anggota');
        }

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? $existing['ukm_id'])
            : (int)Session::get('ukm_id');

        $data = [
            'ukm_id'    => $ukmId,
            'nama'      => sanitize($_POST['nama'] ?? ''),
            'nim'       => sanitize($_POST['nim'] ?? ''),
            'email'     => sanitize($_POST['email'] ?? ''),
            'hierarki'  => sanitize($_POST['hierarki'] ?? 'Anggota'),
            'jabatan'   => sanitize($_POST['jabatan'] ?? 'Anggota'),
            'status'    => sanitize($_POST['status'] ?? 'aktif'),
            'fingerprint_id' => $existing['fingerprint_id'] ?? null,
            'foto_path' => $existing['foto_path'],
            'periode_id' => $existing['periode_id'], // Preserve existing periode binding
        ];

        if (FileUpload::hasFile('foto')) {
            if (!empty($existing['foto_path'])) {
                FileUpload::delete($existing['foto_path']);
            }
            $path = FileUpload::upload($_FILES['foto'], 'anggota');
            if ($path) $data['foto_path'] = $path;
        }

        $this->model->update($id, $data);
        logSecurityActivity('Ubah Anggota', ['id' => $id, 'nama' => $data['nama'], 'nim' => $data['nim']]);
        setFlash('success', 'Data anggota berhasil diperbarui.');
        redirect('index.php?page=anggota');
    }

    /** Hapus anggota */
    public function delete(): void
    {
        Session::requireLogin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=anggota');
        }

        if ($id > 0) {
            $existing = $this->model->getById($id);
            if ($existing && Session::get('admin_role') !== 'superadmin' && (int)$existing['ukm_id'] !== (int)Session::get('ukm_id')) {
                setFlash('error', 'Akses ditolak: Anggota bukan dari UKM Anda.');
                redirect('index.php?page=anggota');
            }

            $this->model->delete($id); // Model otomatis hapus foto
            logSecurityActivity('Hapus Anggota', ['id' => $id]);
            setFlash('success', 'Anggota berhasil dihapus.');
        }
        redirect('index.php?page=anggota');
    }
}
