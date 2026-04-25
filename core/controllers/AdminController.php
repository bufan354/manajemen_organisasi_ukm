<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../FileUpload.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: AdminController
 * Handle CRUD untuk Kelola Admin
 */
class AdminController
{
    private AdminModel $model;

    public function __construct()
    {
        $this->model = new AdminModel();
    }

    /** Tambah admin baru */
    public function store(): void
    {
        Session::requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=kelola_admin');
        }

        $data = [
            'ukm_id'     => !empty($_POST['ukm_id']) ? (int)$_POST['ukm_id'] : null,
            'nama'       => sanitize($_POST['nama'] ?? ''),
            'email'      => sanitize($_POST['email'] ?? ''),
            'password'   => $_POST['password'] ?? '',
            'role'       => sanitize($_POST['role'] ?? 'admin'),
            'periode_id' => !empty($_POST['periode_id']) ? (int)$_POST['periode_id'] : null,
            'foto_path'  => null,
        ];

        if (FileUpload::hasFile('foto')) {
            $path = FileUpload::upload($_FILES['foto'], 'admin');
            if ($path) $data['foto_path'] = $path;
        }

        $this->model->create($data);
        logSecurityActivity('Tambah Data Admin', ['email_baru' => $data['email'], 'role' => $data['role']]);
        setFlash('success', 'Admin baru berhasil ditambahkan.');
        redirect('index.php?page=kelola_admin');
    }

    /** Update admin */
    public function update(): void
    {
        Session::requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=kelola_admin');
        }

        $id = (int)($_POST['id'] ?? 0);
        $existing = $this->model->getById($id);
        if (!$existing) {
            setFlash('error', 'Admin tidak ditemukan.');
            redirect('index.php?page=kelola_admin');
        }

        $data = [
            'ukm_id'     => !empty($_POST['ukm_id']) ? (int)$_POST['ukm_id'] : null,
            'nama'       => sanitize($_POST['nama'] ?? ''),
            'email'      => sanitize($_POST['email'] ?? ''),
            'role'       => sanitize($_POST['role'] ?? 'admin'),
            'password'   => $_POST['password'] ?? '',
            'periode_id' => !empty($_POST['periode_id']) ? (int)$_POST['periode_id'] : null,
            'foto_path'  => $existing['foto_path'],
        ];

        if (FileUpload::hasFile('foto')) {
            if (!empty($existing['foto_path'])) {
                FileUpload::delete($existing['foto_path']);
            }
            $path = FileUpload::upload($_FILES['foto'], 'admin');
            if ($path) $data['foto_path'] = $path;
        }

        $this->model->update($id, $data);
        logSecurityActivity('Ubah Data Admin', ['id_admin' => $id, 'email' => $data['email']]);
        setFlash('success', 'Data admin berhasil diperbarui.');
        redirect('index.php?page=kelola_admin');
    }

    /** Hapus admin */
    public function delete(): void
    {
        Session::requireSuperAdmin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
            logSecurityActivity('Hapus Data Admin', ['id_admin' => $id]);
            setFlash('success', 'Admin berhasil dihapus.');
        }
        redirect('index.php?page=kelola_admin');
    }

    /** Update Profil Sendiri dari Pengaturan */
    public function updateProfile(): void
    {
        Session::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=pengaturan');
        }

        $id = (int)Session::get('admin_id');
        $existing = $this->model->getById($id);
        if (!$existing) {
            setFlash('error', 'Gagal memuat data profil.');
            redirect('index.php?page=pengaturan');
        }

        $data = [
            'nama'       => sanitize($_POST['nama'] ?? $existing['nama']),
            'email'      => sanitize($_POST['email'] ?? $existing['email']),
            'foto_path'  => $existing['foto_path'],
            'role'       => $existing['role'],
            'ukm_id'     => $existing['ukm_id'],
            'periode_id' => $existing['periode_id']
        ];

        if (FileUpload::hasFile('foto')) {
            if (!empty($existing['foto_path'])) {
                FileUpload::delete($existing['foto_path']);
            }
            $path = FileUpload::upload($_FILES['foto'], 'admin');
            if ($path) { 
                $data['foto_path'] = $path;
                Session::set('admin_foto', $path); // Update session UI
            }
        }

        $this->model->update($id, $data);
        
        // Update session info
        Session::set('admin_nama', $data['nama']);
        Session::set('admin_email', $data['email']);
        
        logSecurityActivity('Perbarui Profil Diri', ['id_admin' => $id, 'email' => $data['email']]);
        setFlash('success', 'Profil Anda berhasil diperbarui.');
        redirect('index.php?page=pengaturan');
    }

    /** Update Password Sendiri dari Pengaturan */
    public function updatePassword(): void
    {
        Session::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=pengaturan');
        }

        $id = (int)Session::get('admin_id');
        $existing = $this->model->getById($id);

        $password_lama = $_POST['password_lama'] ?? '';
        $password_baru = $_POST['password_baru'] ?? '';
        $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

        // Validate old password
        if (!$this->model->verifyPassword($existing['email'], $password_lama)) {
            setFlash('error', 'Password lama tidak cocok!');
            redirect('index.php?page=pengaturan');
        }

        // Validate new password
        if ($password_baru !== $konfirmasi_password) {
            setFlash('error', 'Konfirmasi password baru tidak cocok!');
            redirect('index.php?page=pengaturan');
        }

        if (strlen($password_baru) < 6) {
            setFlash('error', 'Password baru minimal 6 karakter!');
            redirect('index.php?page=pengaturan');
        }

        // Update password (model->update triggers hashing logic automatically inside)
        $data = [
            'nama'       => $existing['nama'],
            'email'      => $existing['email'],
            'foto_path'  => $existing['foto_path'],
            'role'       => $existing['role'],
            'ukm_id'     => $existing['ukm_id'],
            'periode_id' => $existing['periode_id'],
            'password'   => $password_baru
        ];
        $this->model->update($id, $data);
        
        logSecurityActivity('Perbarui Password Diri', ['id_admin' => $id]);
        setFlash('success', 'Password Anda berhasil diubah. Silakan gunakan password baru pada login berikutnya.');
        redirect('index.php?page=pengaturan');
    }
}
