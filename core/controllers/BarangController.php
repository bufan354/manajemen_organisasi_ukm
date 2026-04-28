<?php
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

class BarangController
{
    private BarangModel $model;

    public function __construct()
    {
        $this->model = new BarangModel();
    }

    public function store()
    {
        Session::requireLogin();
        $ukm_id = (int)(Session::get('ukm_id') ?? Session::get('last_ukm_id'));
        $nama = sanitize($_POST['nama_barang'] ?? '');
        $satuan = sanitize($_POST['satuan'] ?? 'Pcs');

        if ($this->model->create($ukm_id, $nama, $satuan)) {
            logSecurityActivity('ADD_BARANG', ['nama' => $nama, 'satuan' => $satuan, 'ukm_id' => $ukm_id]);
            setFlash('success', 'Barang berhasil ditambahkan.');
        } else {
            setFlash('error', 'Gagal menambah barang.');
        }
        redirect('index.php?page=master_barang');
    }

    public function update()
    {
        Session::requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        $ukm_id = (int)(Session::get('ukm_id') ?? Session::get('last_ukm_id'));
        $nama = sanitize($_POST['nama_barang'] ?? '');
        $satuan = sanitize($_POST['satuan'] ?? 'Pcs');

        if ($this->model->update($id, $ukm_id, $nama, $satuan)) {
            logSecurityActivity('EDIT_BARANG', ['id' => $id, 'nama_baru' => $nama, 'satuan_baru' => $satuan]);
            setFlash('success', 'Barang berhasil diperbarui.');
        } else {
            setFlash('error', 'Gagal memperbarui barang.');
        }
        redirect('index.php?page=master_barang');
    }

    public function delete()
    {
        Session::requireLogin();
        $id = (int)($_POST['id'] ?? 0);
        $ukm_id = (int)(Session::get('ukm_id') ?? Session::get('last_ukm_id'));

        if ($this->model->delete($id, $ukm_id)) {
            logSecurityActivity('DELETE_BARANG', ['id' => $id]);
            setFlash('success', 'Barang berhasil dihapus.');
        } else {
            setFlash('error', 'Gagal menghapus barang.');
        }
        redirect('index.php?page=master_barang');
    }
}
