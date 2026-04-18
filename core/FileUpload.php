<?php
/**
 * File Upload & Deletion Helper
 * Menyimpan gambar ke folder server, mengembalikan relative path.
 * Juga menghapus file fisik saat record dihapus.
 */
class FileUpload
{
    private static string $baseDir = 'uploads';
    private static array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static int $maxSize = 5 * 1024 * 1024; // 5MB

    /**
     * Upload file gambar
     *
     * @param array  $file     $_FILES['field_name']
     * @param string $category Subfolder tujuan (anggota, berita, ukm, dll)
     * @return string|false    Relative path jika berhasil, false jika gagal
     */
    public static function upload(array $file, string $category): string|false
    {
        // Validasi error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validasi tipe file
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::$allowedTypes)) {
            return false;
        }

        // Validasi ukuran
        if ($file['size'] > self::$maxSize) {
            return false;
        }

        // Buat nama file unik
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid($category . '_', true) . '.' . strtolower($ext);

        // Pastikan direktori ada
        $targetDir = self::$baseDir . '/' . $category;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;

        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath; // Simpan path ini ke database
        }

        return false;
    }

    /**
     * Hapus file fisik dari server
     *
     * @param string|null $filePath Relative path yang tersimpan di database
     * @return bool
     */
    public static function delete(?string $filePath): bool
    {
        if (empty($filePath)) return false;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Cek apakah ada file yang diupload pada field tertentu
     */
    public static function hasFile(string $fieldName): bool
    {
        return isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK;
    }
}
