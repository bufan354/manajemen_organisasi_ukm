<?php
require_once 'core/Database.php';
require_once 'core/Session.php';
require_once 'core/FileUpload.php';
require_once 'core/helpers.php';
require_once 'core/models/PengaturanUmum.php';

/**
 * Controller: Konfigurasi Umum
 * Menangani penyimpanan pengaturan global (label entitas, hero section, dll.)
 * Hanya superadmin yang bisa mengakses.
 */
class KonfigurasiUmumController
{
    public function save(): void
    {
        Session::requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=konfigurasi_umum');
        }

        $model = new PengaturanUmum();

        // Ambil dan sanitasi semua field teks
        $data = [
            'app_name'            => sanitize($_POST['app_name'] ?? 'The Digital Curator'),
            'app_subtitle'        => sanitize($_POST['app_subtitle'] ?? 'IoT Admin Panel'),
            'entitas_nama'        => sanitize($_POST['entitas_nama'] ?? 'UKM'),
            'hero_judul'          => sanitize($_POST['hero_judul'] ?? ''),
            'hero_deskripsi'      => sanitize($_POST['hero_deskripsi'] ?? ''),
            'hero_btn1_label'     => sanitize($_POST['hero_btn1_label'] ?? ''),
            'hero_btn1_link'      => sanitize($_POST['hero_btn1_link'] ?? ''),
            'hero_btn2_label'     => sanitize($_POST['hero_btn2_label'] ?? ''),
            'hero_btn2_link'      => sanitize($_POST['hero_btn2_link'] ?? ''),
            'hero_overlay_opacity'=> sanitize($_POST['hero_overlay_opacity'] ?? '20'),
        ];

        // Validasi: entitas_nama tidak boleh kosong
        if (empty($data['entitas_nama'])) {
            $data['entitas_nama'] = 'UKM';
        }

        // Validasi: overlay opacity harus 0-100
        $opacity = (int)$data['hero_overlay_opacity'];
        $data['hero_overlay_opacity'] = (string)max(0, min(100, $opacity));

        // Handle upload gambar hero
        if (isset($_FILES['hero_gambar']) && $_FILES['hero_gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['hero_gambar']['error'] === UPLOAD_ERR_OK) {
                $uploadedPath = FileUpload::upload($_FILES['hero_gambar'], 'hero');
                if ($uploadedPath) {
                    // Hapus gambar lama jika ada
                    $oldImage = $model->get('hero_gambar');
                    if ($oldImage) {
                        FileUpload::delete($oldImage);
                    }
                    $data['hero_gambar'] = $uploadedPath;
                } else {
                    setFlash('warning', 'Gagal memproses gambar hero. Pastikan format valid (JPG/PNG/WEBP) dan coba lagi.');
                }
            } else if ($_FILES['hero_gambar']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['hero_gambar']['error'] === UPLOAD_ERR_FORM_SIZE) {
                setFlash('error', 'Gagal upload: Ukuran gambar terlalu besar. Batas maksimal server adalah 2MB.');
            } else {
                setFlash('error', 'Gagal upload gambar hero (Error Code: ' . $_FILES['hero_gambar']['error'] . ').');
            }
        }

        // Handle hapus gambar jika diminta
        if (!empty($_POST['hapus_hero_gambar'])) {
            $oldImage = $model->get('hero_gambar');
            if ($oldImage) {
                FileUpload::delete($oldImage);
            }
            $data['hero_gambar'] = '';
        }

        // Simpan semua
        $model->setMany($data);

        // Invalidate cache session agar pembaruan langsung terlihat
        unset($_SESSION['pengaturan_umum']);

        logSecurityActivity('konfigurasi_umum_update', [
            'entitas_nama' => $data['entitas_nama'],
            'admin_id'     => Session::get('admin_id'),
        ]);

        if (!isset($_SESSION['flash'])) {
            setFlash('success', 'Konfigurasi umum berhasil disimpan! Label entitas sekarang: "' . htmlspecialchars($data['entitas_nama']) . '"');
        }
        redirect('index.php?page=konfigurasi_umum');
    }
}
