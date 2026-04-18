<?php
require_once __DIR__ . '/../models/Pengaturan.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: Pengaturan
 * Handle key-value settings per UKM
 */
class PengaturanController
{
    private Pengaturan $model;

    public function __construct()
    {
        $this->model = new Pengaturan();
    }

    /** Simpan pengaturan */
    public function save(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=pengaturan');
        }

        $ukmId = (int)Session::get('ukm_id');

        // Ambil semua field yang dikirim via POST
        $settings = $_POST['settings'] ?? [];
        foreach ($settings as $kunci => $nilai) {
            $this->model->set($ukmId, sanitize($kunci), sanitize($nilai));
        }

        logSecurityActivity('Perbarui Pengaturan', ['ukm_id' => $ukmId, 'kunci_diperbarui' => array_keys($settings)]);

        setFlash('success', 'Pengaturan berhasil disimpan.');
        redirect('index.php?page=pengaturan');
    }
}
