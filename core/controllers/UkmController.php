<?php
require_once __DIR__ . '/../models/Ukm.php';
require_once __DIR__ . '/../models/Pengaturan.php';
require_once __DIR__ . '/../FileUpload.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: UKM
 * Handle CRUD operations untuk UKM
 */
class UkmController
{
    private Ukm $model;

    public function __construct()
    {
        $this->model = new Ukm();
    }

    /** Tambah UKM baru */
    public function store(): void
    {
        Session::requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=ukm');
        }

        $nama = trim($_POST['nama'] ?? '');
        if (empty($nama)) {
            setFlash('error', 'Nama UKM wajib diisi.');
            redirect('index.php?page=tambah_ukm');
        }

        $data = [
            'nama'           => sanitize($_POST['nama'] ?? ''),
            'singkatan'      => sanitize($_POST['singkatan'] ?? ''),
            'kategori'       => sanitize($_POST['kategori'] ?? ''),
            'slogan'         => sanitize($_POST['slogan'] ?? ''),
            'deskripsi'      => sanitize($_POST['deskripsi'] ?? ''),
            'lokasi'         => sanitize($_POST['lokasi'] ?? ''),
            'tanggal_berdiri'=> sanitize($_POST['tanggal_berdiri'] ?? '') ?: null,
            'logo_path'      => null,
            'header_path'    => null,
        ];

        // Upload logo jika ada
        if (FileUpload::hasFile('logo')) {
            $path = FileUpload::upload($_FILES['logo'], 'ukm');
            if ($path) $data['logo_path'] = $path;
        }

        // Upload header jika ada
        if (FileUpload::hasFile('header')) {
            $path = FileUpload::upload($_FILES['header'], 'ukm');
            if ($path) $data['header_path'] = $path;
        }

        $ukmId = $this->model->create($data);

        // ── Auto-create periode kepengurusan pertama ───────────────────────
        // Setiap UKM baru langsung punya 1 periode aktif (tahun berjalan),
        // sehingga tidak perlu setup manual hanya untuk bisa menerima pendaftar.
        require_once 'core/models/Periode.php';
        $tahunIni = (int)date('Y');
        $periodeModel = new Periode();
        $periodeModel->add($ukmId, [
            'nama'          => 'Kepengurusan ' . $tahunIni . '/' . ($tahunIni + 1),
            'tahun_mulai'   => $tahunIni,
            'tahun_selesai' => $tahunIni + 1,
            'deskripsi'     => 'Periode kepengurusan awal yang dibuat otomatis saat UKM didaftarkan.',
        ]);
        // Ambil ID periode yang baru dibuat lalu aktifkan
        $periodes = $periodeModel->getAll($ukmId);
        if (!empty($periodes)) {
            $periodeModel->setActive($ukmId, $periodes[0]['id']);
        }
        // ──────────────────────────────────────────────────────────────────

        logSecurityActivity('Tambah Data UKM Baru', ['ukm' => $data['nama']]);
        setFlash('success', 'UKM berhasil ditambahkan beserta periode kepengurusan awal.');
        redirect('index.php?page=ukm');
    }

    /** Update UKM */
    public function update(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profil');
        }

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            setFlash('error', 'ID UKM tidak valid.');
            redirect('index.php?page=profil');
        }

        // Authorization: superadmin atau admin yang mengelola UKM-nya sendiri
        if (Session::get('admin_role') !== 'superadmin' && Session::get('ukm_id') != $id) {
            setFlash('error', 'Akses ditolak.');
            redirect('index.php?page=dashboard');
        }

        $existing = $this->model->getById($id);
        if (!$existing) {
            setFlash('error', 'UKM tidak ditemukan.');
            redirect('index.php?page=ukm');
        }

        // Validasi field wajib
        $nama = trim($_POST['nama'] ?? '');
        if (empty($nama)) {
            setFlash('error', 'Nama UKM wajib diisi.');
            if (Session::get('admin_role') === 'superadmin') {
                redirect("index.php?page=profil&ukm_id={$id}");
            } else {
                redirect('index.php?page=profil');
            }
        }

        $data = [
            'nama'            => sanitize($_POST['nama'] ?? ''),
            'singkatan'       => sanitize($_POST['singkatan'] ?? ''),
            'kategori'        => sanitize($_POST['kategori'] ?? ''),
            'slogan'          => sanitize($_POST['slogan'] ?? ''),
            'deskripsi'       => sanitize($_POST['deskripsi'] ?? ''),
            'lokasi'          => sanitize($_POST['lokasi'] ?? ''),
            'tanggal_berdiri' => sanitize($_POST['tanggal_berdiri'] ?? '') ?: null,
            'logo_path'       => $existing['logo_path'],
            'header_path'     => $existing['header_path'] ?? null,
        ];

        // Upload logo baru jika ada
        if (FileUpload::hasFile('logo')) {
            if (!empty($existing['logo_path'])) FileUpload::delete($existing['logo_path']);
            $path = FileUpload::upload($_FILES['logo'], 'ukm');
            if ($path) $data['logo_path'] = $path;
        }

        // Upload header baru jika ada
        if (FileUpload::hasFile('header')) {
            if (!empty($existing['header_path'])) FileUpload::delete($existing['header_path']);
            $path = FileUpload::upload($_FILES['header'], 'ukm');
            if ($path) $data['header_path'] = $path;
        }

        $this->model->update($id, $data);

        // Simpan settings kontak & sosial media ke tabel pengaturan
        $pengaturan = new Pengaturan();
        $pengaturan->setMany($id, [
            'email_admin'    => sanitize($_POST['email_admin']    ?? ''),
            'whatsapp'       => sanitize($_POST['whatsapp']       ?? ''),
            'instagram_url'  => sanitize($_POST['instagram_url']  ?? ''),
            'facebook_url'   => sanitize($_POST['facebook_url']   ?? ''),
            'twitter_url'    => sanitize($_POST['twitter_url']    ?? ''),
            'youtube_url'    => sanitize($_POST['youtube_url']    ?? ''),
            'tiktok_url'     => sanitize($_POST['tiktok_url']     ?? ''),
        ]);

        // Simpan last_ukm_id ke session agar super admin bisa balik ke UKM yang sama
        if (Session::get('admin_role') === 'superadmin') {
            Session::set('last_ukm_id', $id);
            logSecurityActivity('Ubah Profil UKM (Superadmin)', ['ukm_id' => $id]);
            setFlash('success', 'Profil UKM berhasil diperbarui.');
            redirect("index.php?page=profil&ukm_id=" . $id);
        } else {
            logSecurityActivity('Ubah Profil UKM', ['ukm_id' => $id]);
            setFlash('success', 'Profil UKM berhasil diperbarui.');
            redirect('index.php?page=profil');
        }
    }

    /** Hapus UKM */
    public function delete(): void
    {
        Session::requireSuperAdmin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
            logSecurityActivity('Hapus Data UKM', ['ukm_id' => $id]);
            setFlash('success', 'UKM berhasil dihapus.');
        }
        redirect('index.php?page=ukm');
    }

    /** Toggle Status UKM */
    public function toggleStatus(): void
    {
        Session::requireSuperAdmin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->toggleStatus($id);
            logSecurityActivity('Ubah Status UKM', ['ukm_id' => $id]);
            setFlash('success', 'Status UKM berhasil diperbarui.');
        }
        redirect('index.php?page=ukm');
    }
}
