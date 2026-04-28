# Sistem Manajemen Organisasi Terintegrasi (SMO)
### Manajemen UKM & Organisasi berbasis IoT Modern

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.x-777bb4.svg)](https://www.php.net/)
[![Tailwind CSS](https://img.shields.io/badge/Styling-Tailwind_CSS-38b2ac.svg)](https://tailwindcss.com/)
[![IoT Powered](https://img.shields.io/badge/IoT-ESP32_Ready-orange.svg)](https://www.espressif.com/)

Sistem Manajemen Organisasi Terintegrasi adalah platform *all-in-one* yang dirancang untuk mendigitalisasi seluruh aspek operasional organisasi atau Unit Kegiatan Mahasiswa (UKM). Dari manajemen pendaftaran anggota baru hingga monitoring kehadiran biometrik secara real-time, sistem ini memberikan solusi menyeluruh untuk efisiensi dan transparansi organisasi.

![Hero Image](assets/common/img/home.png)

## 🚀 Modul Unggulan

Sistem ini dikembangkan dengan arsitektur modular yang mencakup berbagai aspek krusial manajemen organisasi:

### 1. 👥 Manajemen Keanggotaan & Hierarki
- **Struktur Organisasi**: Manajemen kepengurusan per periode dengan tingkatan jabatan yang dinamis.
- **Buku Induk Anggota**: Database pusat untuk data akademik dan personal anggota.
- **Riwayat Jabatan**: Pelacakan peran anggota dari periode ke periode.

### 2. 📝 Open Recruitment (Oprec) Digital
- **Formulir Kustom**: Admin dapat membuat kuisioner tambahan sesuai kebutuhan seleksi masing-masing UKM.
- **Snapshot Integritas**: Menyimpan teks pertanyaan asli saat pendaftaran untuk menjaga akurasi data jangka panjang.
- **Dashboard Verifikasi**: Alur persetujuan atau penolakan pendaftar dengan sistem notifikasi dan template alasan penolakan.

### 3. 📰 Publikasi & Berita
- **CMS Berita**: Publikasi kegiatan, artikel, dan pengumuman organisasi secara terstruktur.
- **Status Penerbitan**: Manajemen draft dan publikasi untuk alur redaksi yang lebih rapi.

### 4. ⚡ IoT Attendance System (Feature Unggulan)
- **Monitoring Real-time**: Dashboard kehadiran yang diperbarui secara instan saat sidik jari dipindai.
- **Biometrik Terpusat**: Manajemen data sidik jari (Enroll & Delete) langsung dari dashboard admin.
- **Sinkronisasi ESP32**: Komunikasi aman antara perangkat keras dan server menggunakan API Key dan Header khusus.

### 5. 🔐 Keamanan & Administrasi
- **Two-Factor Authentication (2FA)**: Perlindungan tambahan untuk akun Admin dan Superadmin menggunakan TOTP.
- **Audit Log**: Pencatatan aktivitas sensitif untuk memantau penggunaan sistem.
- **Soft Delete**: Sistem arsip data pendaftaran agar histori tidak hilang meski data telah dihapus dari daftar aktif.

### 6. 📧 Manajemen Persuratan & Arsip Digital (Premium)
- **Otomasi Nomor Surat**: Sistem penomoran otomatis yang cerdas, mendukung klasifikasi surat (Internal/Eksternal) dengan sistem reset otomatis per periode kepengurusan.
- **Smart Grouping & Cloning**: Fitur untuk mengelompokkan surat induk dan turunannya (misal: Undangan & Lampiran) serta fitur duplikasi surat instan.
- **Multi-Period Archiving**: Pemisahan arsip dokumen antar periode kepengurusan dengan penegakan akses *read-only* untuk periode riwayat guna menjaga integritas data.
- **Manajemen Template Dinamis**: Database template untuk Tujuan, Perihal, dan Lokasi yang dapat dikustomisasi untuk efisiensi administrasi.
- **Opsi Pengesahan Terintegrasi**: Logika pengesahan (TTD & Cap/Stempel) yang saling eksklusif antara pihak Rektorat, BEM, atau internal UKM.
- **Print Layout & Export**: Output cetak surat premium dengan dukungan Kop Surat kustom per organisasi dan fitur ekspor data arsip ke format Excel.
- **Storage Optimization**: Sistem *Cascading Delete* yang membersihkan file fisik (PDF & Lampiran) secara otomatis saat sebuah periode dihapus.

## 🛠 Arsitektur Teknologi

- **Backend**: PHP 8.x (Custom MVC Architecture)
- **Frontend**: Tailwind CSS, Vanilla JS, Chart.js
- **Database**: MySQL (optimized for large scale relations)
- **Hardware Interface**: ESP32 with AS608 Optical Fingerprint Sensor
- **Security**: CSRF Protection, Password Hashing, API Auth, Rate Limiting

## 📦 Instalasi

### Prasyarat
- PHP 8.1 atau lebih tinggi
- MySQL / MariaDB
- Web Server (Apache / Nginx)

### Langkah-langkah
1. Clone repositori:
   ```bash
   git clone https://github.com/bufan354/manajemen_organisasi_ukm.git
   ```
2. Konfigurasi Database:
   - Buat database baru di MySQL.
   - Import file `database/schema.sql` (Core) dan `database/surat_schema.sql` (Module Persuratan).
3. Atur Lingkungan:
   - Salin file `.env.example` ke `.env`.
   - Sesuaikan kredensial database dan API Key.
4. Selesai! Akses aplikasi melalui browser Anda.

## 🤝 Kontribusi

Kami menerima kontribusi dalam bentuk pelaporan bug, saran fitur, atau pull request. Pastikan untuk selalu melakukan `git pull` sebelum memulai perubahan.

---
Dikembangkan dengan ❤️ oleh **Tim Pengembang Organisasi Digital**.
