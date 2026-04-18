<?php
require_once __DIR__ . '/../models/JabatanKustom.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../helpers.php';

/**
 * Controller: JabatanKustom
 * Handle CRUD jabatan kustom per UKM
 */
class JabatanController
{
    private JabatanKustom $model;

    public function __construct()
    {
        $this->model = new JabatanKustom();
    }

    /**
     * Resolve UKM ID yang boleh dikelola oleh user yang login.
     * Superadmin: bisa dari POST/GET, admin biasa: hanya UKM sendiri.
     */
    private function resolveUkmId(?int $postUkmId = null): int
    {
        if (Session::get('admin_role') === 'superadmin') {
            return (int)($postUkmId ?? 0);
        }
        return (int)Session::get('ukm_id');
    }

    /** Tambah jabatan baru */
    public function store(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=jabatan');
        }

        $ukmId      = $this->resolveUkmId((int)($_POST['ukm_id'] ?? 0));
        $namaJabatan = trim(sanitize($_POST['nama_jabatan'] ?? ''));
        $level       = (int)($_POST['level'] ?? 4);

        if (!$ukmId || !$namaJabatan) {
            setFlash('error', 'Nama jabatan tidak boleh kosong.');
            redirect('index.php?page=jabatan' . ($ukmId ? "&ukm_id={$ukmId}" : ''));
        }

        // Validasi: nama tidak boleh sama dengan jabatan standar
        $standarNames = array_map(fn($j) => strtolower($j['hierarki']), JabatanKustom::JABATAN_STANDAR);
        if (in_array(strtolower($namaJabatan), $standarNames)) {
            setFlash('error', "\"$namaJabatan\" adalah jabatan standar dan tidak perlu ditambahkan sebagai kustom.");
            redirect('index.php?page=jabatan' . ($ukmId ? "&ukm_id={$ukmId}" : ''));
        }

        // Validasi: cegah duplikat
        if ($this->model->nameExists($ukmId, $namaJabatan)) {
            setFlash('error', "Jabatan \"$namaJabatan\" sudah ada untuk UKM ini.");
            redirect('index.php?page=jabatan' . ($ukmId ? "&ukm_id={$ukmId}" : ''));
        }

        $this->model->create($ukmId, $namaJabatan, $level);
        logSecurityActivity('Tambah Jabatan Kustom', ['ukm_id' => $ukmId, 'jabatan' => $namaJabatan]);
        setFlash('success', "Jabatan \"$namaJabatan\" berhasil ditambahkan.");
        redirect('index.php?page=jabatan' . ($ukmId ? "&ukm_id={$ukmId}" : ''));
    }

    /** Update jabatan kustom */
    public function update(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=jabatan');
        }

        $id          = (int)($_POST['id'] ?? 0);
        $namaJabatan = trim(sanitize($_POST['nama_jabatan'] ?? ''));
        $level       = (int)($_POST['level'] ?? 4);

        $jabatan = $this->model->getById($id);
        if (!$jabatan) {
            setFlash('error', 'Jabatan tidak ditemukan.');
            redirect('index.php?page=jabatan');
        }

        $ukmId = (int)$jabatan['ukm_id'];

        // Cek hak akses: admin biasa hanya boleh edit jabatan milik UKMnya
        if (Session::get('admin_role') !== 'superadmin' && $ukmId !== (int)Session::get('ukm_id')) {
            setFlash('error', 'Akses ditolak.');
            redirect('index.php?page=jabatan');
        }

        if (!$namaJabatan) {
            setFlash('error', 'Nama jabatan tidak boleh kosong.');
            redirect("index.php?page=jabatan&ukm_id={$ukmId}");
        }

        // Validasi duplikat (exclude ID ini sendiri)
        if ($this->model->nameExists($ukmId, $namaJabatan, $id)) {
            setFlash('error', "Jabatan \"$namaJabatan\" sudah ada untuk UKM ini.");
            redirect("index.php?page=jabatan&ukm_id={$ukmId}");
        }

        // Jika nama berubah, update juga kolom hierarki di tabel anggota
        $oldNama = $jabatan['nama_jabatan'];
        $this->model->update($id, $namaJabatan, $level);

        if ($oldNama !== $namaJabatan) {
            // Sinkronisasi: update anggota yang pakai jabatan lama
            require_once __DIR__ . '/../Database.php';
            $db = Database::getConnection();
            $stmt = $db->prepare(
                "UPDATE anggota SET hierarki = ? WHERE ukm_id = ? AND hierarki = ?"
            );
            $stmt->execute([$namaJabatan, $ukmId, $oldNama]);
        }

        logSecurityActivity('Edit Jabatan Kustom', ['id' => $id, 'lama' => $oldNama, 'baru' => $namaJabatan]);
        setFlash('success', "Jabatan berhasil diperbarui menjadi \"$namaJabatan\".");
        redirect("index.php?page=jabatan&ukm_id={$ukmId}");
    }

    /** Hapus jabatan kustom */
    public function delete(): void
    {
        Session::requireLogin();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            redirect('index.php?page=jabatan');
        }

        $jabatan = $this->model->getById($id);
        if (!$jabatan) {
            setFlash('error', 'Jabatan tidak ditemukan.');
            redirect('index.php?page=jabatan');
        }

        $ukmId = (int)$jabatan['ukm_id'];

        // Cek hak akses
        if (Session::get('admin_role') !== 'superadmin' && $ukmId !== (int)Session::get('ukm_id')) {
            setFlash('error', 'Akses ditolak.');
            redirect('index.php?page=jabatan');
        }

        // Cek apakah jabatan masih dipakai
        if ($this->model->isUsedByAnggota($id)) {
            setFlash('error', "Jabatan \"{$jabatan['nama_jabatan']}\" tidak dapat dihapus karena masih digunakan oleh anggota. Ubah jabatan anggota tersebut terlebih dahulu.");
            redirect("index.php?page=jabatan&ukm_id={$ukmId}");
        }

        $this->model->delete($id);
        logSecurityActivity('Hapus Jabatan Kustom', ['id' => $id, 'jabatan' => $jabatan['nama_jabatan']]);
        setFlash('success', "Jabatan \"{$jabatan['nama_jabatan']}\" berhasil dihapus.");
        redirect("index.php?page=jabatan&ukm_id={$ukmId}");
    }
}
