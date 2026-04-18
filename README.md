# Sistem Absensi IoT (Fingerprint & Real-time Dashboard)

Sistem Absensi berbasis IoT yang menggabungkan perangkat keras ESP32, sensor sidik jari AS608, dan dashboard web real-time untuk manajemen kehadiran Unit Kegiatan Mahasiswa (UKM).

## 🚀 Fitur Utama
- **Real-time Monitoring**: Dashboard admin yang diperbarui secara otomatis ketika anggota melakukan absensi.
- **Manajemen Multi-UKM**: Superadmin dapat mengelola banyak UKM dalam satu sistem.
- **Sistem Notifikasi**: Notifikasi admin untuk setiap aktivitas kehadiran dan pendaftaran.
- **Keamanan Berlapis**: Dilindungi dengan CSRF Protection, API Key Authentication, dan Two-Factor Authentication (2FA).
- **Export Data**: Laporan kehadiran dapat diekspor ke format Excel yang rapi dan profesional.
- **Hardware Integration**: Sinkronisasi otomatis antara database server dan sensor sidik jari (Enroll/Delete/Verify).

## 🛠️ Stack Teknologi
- **Web**: PHP 8.x (Vanilla/MVC Pattern), MySQL, Tailwind CSS, Chart.js.
- **Hardware**: ESP32, Arduino C++, WiFiManager (Dynamic WiFi Config).

## 📋 Persiapan Instalasi

### 1. Web Server
1. Clone repositori ini: `git clone https://github.com/username/absensi-iot.git`
2. Buat database baru di MySQL (misal: `absensi_iot`).
3. Import schema database: `database/schema.sql`.
4. Salin `.env.example` menjadi `.env` dan sesuaikan kredensial database serta `API_KEY` Anda.
5. Pastikan folder `uploads/` memiliki izin akses tulis (write permission).

### 2. Hardware (ESP32)
1. Buka folder `hardware/ESP32_Fingerprint/` menggunakan Arduino IDE.
2. Salin `config.h.example` menjadi `config.h`.
3. Masukkan `SERVER_URLS` dan `API_KEY` (harus sama dengan yang ada di file `.env`).
4. Pastikan library berikut sudah terinstal di Arduino IDE:
   - `Adafruit Fingerprint Sensor Library`
   - `WiFiManager`
   - `LiquidCrystal_I2C`
5. Upload kode ke ESP32.

## 🔐 Keamanan
Project ini sudah dilengkapi dengan mekanisme keamanan untuk mencegah kebocoran data sensitif:
- Data kredensial disimpan dalam file `.env` yang tidak ikut ter-upload di repositori publik.
- Komunikasi ESP32 dan Server menggunakan header `X-API-KEY`.
- Semua form admin memiliki proteksi CSRF.

## 📄 Lisensi
Distributed under the MIT License. See `LICENSE` for more information.

---
*Dibuat untuk keperluan Tugas Akhir / Manajemen UKM.*
