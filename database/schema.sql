-- ============================================================
-- Skema Database: Sistem Absensi IoT
-- Engine: InnoDB | Charset: utf8mb4
-- Versi: 2.0 (Full Schema - Termasuk semua migrasi)
-- ============================================================

CREATE DATABASE IF NOT EXISTS absensi_iot
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE absensi_iot;

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Tabel: ukm
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS ukm (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nama             VARCHAR(200) NOT NULL,
    singkatan        VARCHAR(50)  NOT NULL,
    kategori         VARCHAR(100) DEFAULT NULL,   -- Fokus/Bidang UKM (Seni, Olahraga, dll)
    slogan           VARCHAR(255) DEFAULT NULL,
    deskripsi        TEXT         DEFAULT NULL,
    logo_path        VARCHAR(255) DEFAULT NULL,   -- Path logo (crop center lingkaran)
    header_path      VARCHAR(255) DEFAULT NULL,   -- Path gambar header/banner
    lokasi           VARCHAR(200) DEFAULT NULL,   -- Lokasi markas UKM
    koordinat        VARCHAR(100) DEFAULT NULL,   -- Koordinat Google Maps (Lat, Lng)
    tanggal_berdiri  DATE         DEFAULT NULL,
    status           ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at       TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: periode (periode kepengurusan ukm)
-- Dipindahkan ke atas agar FK dari admins & anggota tidak gagal
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS periode (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id        INT          NOT NULL,
    tahun_mulai   YEAR         NOT NULL,
    bulan_mulai   INT          DEFAULT NULL,
    tahun_selesai YEAR         NOT NULL,
    bulan_selesai INT          DEFAULT NULL,
    is_active     TINYINT(1)   DEFAULT 0,
    nama          VARCHAR(150) NOT NULL,
    deskripsi     TEXT         DEFAULT NULL,
    dokumen_path  VARCHAR(255) DEFAULT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: admins (Admin UKM & Superadmin)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT          DEFAULT NULL,
    nama            VARCHAR(150) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('superadmin','admin') NOT NULL DEFAULT 'admin',
    periode_id      INT DEFAULT NULL,
    foto_path       VARCHAR(255) DEFAULT NULL,
    is_2fa_active   TINYINT(1)  DEFAULT 0,
    totp_secret     VARCHAR(64)  DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE SET NULL,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: anggota
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS anggota (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id      INT          NOT NULL,
    periode_id  INT          NOT NULL,
    nama        VARCHAR(150) NOT NULL,
    nim         VARCHAR(30)  DEFAULT NULL,
    email       VARCHAR(150) DEFAULT NULL,
    hierarki    VARCHAR(100) NOT NULL DEFAULT 'Anggota',  -- bisa: Ketua/Wakil/Sekretaris/Bendahara/Anggota atau nama jabatan kustom
    jabatan     VARCHAR(100) DEFAULT 'Anggota',
    status      ENUM('aktif','nonaktif') DEFAULT 'aktif',
    fingerprint_id INT DEFAULT NULL,
    fingerprint_template TEXT DEFAULT NULL,
    fingerprint_registered_at DATETIME DEFAULT NULL,
    fingerprint_updated_at DATETIME DEFAULT NULL,
    foto_path   VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE,
    INDEX idx_fingerprint_id (fingerprint_id),
    INDEX idx_nim_ukm (nim, ukm_id)
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: jabatan_kustom (jabatan dinamis per UKM)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS jabatan_kustom (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id       INT NOT NULL,
    nama_jabatan VARCHAR(100) NOT NULL,
    level        INT DEFAULT 4  COMMENT '1=Pembina, 2=Ketua, 3=BPH, 4=Koordinator/Divisi, 5=Anggota',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    UNIQUE KEY uk_ukm_jabatan (ukm_id, nama_jabatan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------
-- Tabel: berita
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS berita (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id      INT          NOT NULL,
    periode_id  INT          NOT NULL,
    judul       VARCHAR(255) NOT NULL,
    konten      TEXT         NOT NULL,
    kategori    VARCHAR(50)  DEFAULT NULL,
    gambar_path VARCHAR(255) DEFAULT NULL,
    penulis     VARCHAR(100) DEFAULT NULL,
    status      ENUM('draft','published') DEFAULT 'draft',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: events
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT          NOT NULL,
    is_routine      TINYINT(1)   NOT NULL DEFAULT 0,   -- 1 = Event Rutin (Master), 0 = Event Biasa
    hari_rutin      VARCHAR(20)  DEFAULT NULL,          -- Hari berulang: '1,3,5' (Senin,Rabu,Jum)
    parent_id       INT          DEFAULT NULL,          -- ID master jika ini anak event rutin
    periode_id      INT          DEFAULT NULL,          -- Opsional: event bisa terikat ke periode
    nama            VARCHAR(200) NOT NULL,
    deskripsi       TEXT         DEFAULT NULL,
    waktu_mulai     DATETIME     NOT NULL,
    waktu_selesai   DATETIME     NOT NULL,
    lokasi          VARCHAR(200) DEFAULT NULL,
    status_absensi  TINYINT(1)   DEFAULT 0,
    status          ENUM('scheduled', 'postponed', 'cancelled') DEFAULT 'scheduled',
    alasan          TEXT         DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE SET NULL,
    INDEX idx_active_event (ukm_id, status_absensi, waktu_mulai, waktu_selesai),
    INDEX idx_routine (ukm_id, is_routine)
) ENGINE=InnoDB;

-- (Tabel periode sudah dibuat di atas, sebelum admins)

-- -----------------------------------------------------------
-- Tabel: pendaftaran (registrasi calon anggota via web)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pendaftaran (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id             INT          NOT NULL,
    periode_id         INT          DEFAULT NULL,
    nama               VARCHAR(150) NOT NULL,
    email              VARCHAR(150) NOT NULL,
    no_wa              VARCHAR(20)  NOT NULL,
    kelas              VARCHAR(20)  DEFAULT NULL,
    jurusan            VARCHAR(100) DEFAULT NULL,
    jawaban_kuisioner  JSON         DEFAULT NULL,
    alasan             TEXT         DEFAULT NULL,
    status             ENUM('pending','diterima','ditolak') DEFAULT 'pending',
    session_id         VARCHAR(255) DEFAULT NULL,
    alasan_penolakan   TEXT         DEFAULT NULL,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at         TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: pendaftaran_jawaban (Snapshot jawaban kuisioner)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pendaftaran_jawaban (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id     INT          NOT NULL,
    pertanyaan_teks    TEXT         NOT NULL,
    jawaban_teks       TEXT         NOT NULL,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Tabel: pengaturan (key-value per UKM)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id  INT          NOT NULL,
    kunci   VARCHAR(100) NOT NULL,
    nilai   TEXT         DEFAULT NULL,
    UNIQUE KEY unique_setting (ukm_id, kunci),
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: absensi (log kehadiran per event)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS absensi (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    event_id    INT NOT NULL,
    anggota_id  INT NOT NULL,
    waktu_hadir DATETIME DEFAULT CURRENT_TIMESTAMP,
    metode      ENUM('fingerprint','rfid','manual') DEFAULT 'manual',
    FOREIGN KEY (event_id)   REFERENCES events(id)  ON DELETE CASCADE,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id)  ON DELETE CASCADE,
    UNIQUE KEY unique_absensi (event_id, anggota_id),
    INDEX idx_event_anggota (event_id, anggota_id)
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Seed: Superadmin default
-- Password: admin123 (bcrypt hash)
-- -----------------------------------------------------------
INSERT INTO admins (nama, email, password, role) VALUES
('Super Admin', 'admin@iotabsensi.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- -----------------------------------------------------------
-- Tabel: log_keamanan (Pencatatan aktivitas sensitif)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_keamanan (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    waktu       DATETIME DEFAULT CURRENT_TIMESTAMP,
    aktivitas   VARCHAR(255) NOT NULL,
    user_id     INT NULL,
    ip_address  VARCHAR(45) NULL,
    user_agent  TEXT NULL,
    detail      TEXT NULL,
    INDEX idx_waktu (waktu),
    FOREIGN KEY (user_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: admin_sessions (Login sessions)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    login_time DATETIME NOT NULL,
    last_activity DATETIME NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX (session_id),
    INDEX (last_activity)
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: fingerprint_pending (sinkronisasi mode enroll ESP32)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS fingerprint_pending (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id      INT NOT NULL,
    ukm_id          INT NOT NULL,
    action          ENUM('enroll','delete') NOT NULL DEFAULT 'enroll',
    fingerprint_id  INT NULL,                         -- ID slot sensor (diisi saat delete)
    status          ENUM('pending','processing','done','failed','cancelled') DEFAULT 'pending',
    token           VARCHAR(64) NOT NULL UNIQUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at      TIMESTAMP NOT NULL,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
    INDEX idx_status_expires (status, expires_at),
    INDEX idx_ukm_status (ukm_id, status)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,          -- penerima notifikasi (admin atau superadmin)
    ukm_id INT NULL,               -- jika notifikasi spesifik UKM, bisa null untuk superadmin
    jenis VARCHAR(50) NOT NULL,    -- 'pendaftaran_baru', 'login_perangkat_baru'
    judul VARCHAR(255) NOT NULL,
    pesan TEXT NOT NULL,
    link VARCHAR(255) NULL,        -- URL tujuan saat diklik
    is_dibaca BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX (user_id, is_dibaca, created_at)
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: pengaturan_umum (pengaturan global sistem)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan_umum (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    key_name   VARCHAR(100) UNIQUE NOT NULL,
    value      TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Tabel: login_attempts (Rate limiting login)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS login_attempts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    ip_address   VARCHAR(45) NOT NULL,
    email        VARCHAR(150) DEFAULT NULL,
    fail_count   INT DEFAULT 1,
    locked_until DATETIME DEFAULT NULL,
    last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Seed: Data default pengaturan_umum
-- -----------------------------------------------------------
INSERT IGNORE INTO pengaturan_umum (key_name, value) VALUES
    ('app_name',            'Sistem Absensi IoT'),
    ('entitas_nama',        'UKM'),
    ('hero_judul',          'SISTEM ABSENSI & MANAJEMEN ORGANISASI'),
    ('hero_deskripsi',      'Optimalkan efisiensi kehadiran dengan teknologi berbasis Fingerprint + ESP32. Monitoring real-time untuk transparansi organisasi digital.'),
    ('hero_btn1_label',     'Jelajahi UKM'),
    ('hero_btn1_link',      'index.php?page=katalog_ukm'),
    ('hero_btn2_label',     'Dokumentasi API'),
    ('hero_btn2_link',      'index.php?page=tentang'),
    ('hero_gambar',         ''),
    ('hero_overlay_opacity','20');

SET FOREIGN_KEY_CHECKS = 1;
