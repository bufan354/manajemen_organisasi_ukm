<?php
require_once __DIR__ . '/../models/Berita.php';
require_once __DIR__ . '/../FileUpload.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Berita
 * Handle CRUD operations untuk Berita
 */
class BeritaController
{
    private Berita $model;

    public function __construct()
    {
        $this->model = new Berita();
    }

    /** Tambah berita baru */
    public function store(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=berita');
        }

        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=berita');
        }

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? 0)
            : (int)Session::get('ukm_id');

        $data = [
            'ukm_id'      => $ukmId,
            'judul'       => sanitize($_POST['judul'] ?? ''),
            'konten'      => $_POST['konten'] ?? '', // HTML content
            'kategori'    => sanitize($_POST['kategori'] ?? ''),
            'penulis'     => sanitize($_POST['penulis'] ?? Session::get('admin_nama')),
            'status'      => isset($_POST['published']) ? 'published' : 'draft',
            'gambar_path' => null,
        ];

        // Auto-inject periode_id: admin UKM → dari session, superadmin → dari periode aktif UKM
        if (Session::get('admin_role') === 'superadmin') {
            require_once 'core/models/Periode.php';
            $activePeriode = (new Periode())->getActive($ukmId);
            $data['periode_id'] = $activePeriode ? $activePeriode['id'] : 0;
        } else {
            $data['periode_id'] = (int)Session::get('periode_id');
        }

        if (FileUpload::hasFile('gambar')) {
            $path = FileUpload::upload($_FILES['gambar'], 'berita');
            if ($path) $data['gambar_path'] = $path;
        }

        $this->model->create($data);
        logSecurityActivity('Tambah Berita', ['judul' => $data['judul'], 'ukm_id' => $data['ukm_id']]);
        setFlash('success', 'Berita berhasil disimpan.');
        redirect('index.php?page=berita');
    }

    /** Update berita */
    public function update(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=berita');
        }

        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=berita');
        }

        $id = (int)($_POST['id'] ?? 0);
        $existing = $this->model->getById($id);
        if (!$existing) {
            setFlash('error', 'Berita tidak ditemukan.');
            redirect('index.php?page=berita');
        }

        $ukmId = Session::get('admin_role') === 'superadmin'
            ? (int)($_POST['ukm_id'] ?? $existing['ukm_id'])
            : (int)Session::get('ukm_id');

        $data = [
            'ukm_id'      => $ukmId,
            'judul'       => sanitize($_POST['judul'] ?? ''),
            'konten'      => $_POST['konten'] ?? '',
            'kategori'    => sanitize($_POST['kategori'] ?? ''),
            'penulis'     => sanitize($_POST['penulis'] ?? $existing['penulis']),
            'status'      => isset($_POST['published']) ? 'published' : 'draft',
            'gambar_path' => $existing['gambar_path'],
            'periode_id'  => $existing['periode_id'], // Preserve existing periode binding
        ];

        if (FileUpload::hasFile('gambar')) {
            if (!empty($existing['gambar_path'])) {
                FileUpload::delete($existing['gambar_path']);
            }
            $path = FileUpload::upload($_FILES['gambar'], 'berita');
            if ($path) $data['gambar_path'] = $path;
        }

        $this->model->update($id, $data);
        logSecurityActivity('Ubah Berita', ['id' => $id, 'judul' => $data['judul']]);
        setFlash('success', 'Berita berhasil diperbarui.');
        redirect('index.php?page=berita');
    }

    /** Hapus berita */
    public function delete(): void
    {
        Session::requireLogin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if (isset($_SESSION['is_active_periode']) && $_SESSION['is_active_periode'] === false) {
            setFlash('error', 'Akses ditolak: Periode saat ini dalam mode read-only (arsip).');
            redirect('index.php?page=berita');
        }

        if ($id > 0) {
            $this->model->delete($id);
            logSecurityActivity('Hapus Berita', ['id' => $id]);
            setFlash('success', 'Berita berhasil dihapus.');
        }
        redirect('index.php?page=berita');
    }
}
