-- ============================================================
-- Skema Database Tambahan: Sistem Surat & Inventaris
-- ============================================================

USE absensi_iot;

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Tabel: pengaturan_surat_global (Setting Default Lembaga)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan_surat_global (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nama_key        VARCHAR(100) NOT NULL UNIQUE,
    nama_val        TEXT DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: pengaturan (Setting Spesifik per UKM)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT NOT NULL,
    kunci           VARCHAR(100) NOT NULL,
    nilai           TEXT DEFAULT NULL,
    UNIQUE KEY unique_setting (ukm_id, kunci),
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: barang_master
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS barang_master (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT NOT NULL,
    nama_barang     VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: lampiran_pinjam
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS lampiran_pinjam (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT NOT NULL,
    periode_id      INT NOT NULL,
    nama_acara      VARCHAR(255) NOT NULL,
    tanggal_kegiatan VARCHAR(100) NOT NULL,
    tahun           YEAR NOT NULL,
    barang_json     JSON NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: arsip_surat
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS arsip_surat (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT NOT NULL,
    periode_id      INT NOT NULL,
    parent_id       INT DEFAULT NULL,
    jenis_surat     ENUM('L', 'D', 'M') NOT NULL,
    tanggal_dikirim DATE DEFAULT NULL,
    nomor_surat     VARCHAR(255) NOT NULL,
    perihal         VARCHAR(255) NOT NULL,
    tujuan          TEXT NOT NULL,
    tempat_tanggal  VARCHAR(255) DEFAULT NULL,
    konten_surat    JSON DEFAULT NULL,
    file_surat      VARCHAR(255) DEFAULT NULL,
    created_by      INT DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES arsip_surat(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: surat_templates
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS surat_templates (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT NOT NULL,
    periode_id      INT NOT NULL,
    label           VARCHAR(255) NOT NULL,
    jenis           ENUM('perihal', 'tujuan', 'kegiatan', 'tempat') NOT NULL,
    isi_teks        TEXT NOT NULL,
    perihal_default VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Tabel: panitia_tetap
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS panitia_tetap (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ukm_id          INT NOT NULL,
    periode_id      INT NOT NULL,
    nama            VARCHAR(255) NOT NULL,
    jabatan         VARCHAR(255) NOT NULL,
    type            ENUM('inti', 'panitia') DEFAULT 'panitia',
    ttd_path        VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE CASCADE,
    FOREIGN KEY (periode_id) REFERENCES periode(id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
