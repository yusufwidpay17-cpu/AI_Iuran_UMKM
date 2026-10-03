-- ====================================================================
-- DATABASE SCHEMA FOR MARKET RENT BILLING SYSTEM (NUSANTARA SME CORE)
-- Target: MariaDB (XAMPP Environment)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS db_tagihan_pasar;
USE db_tagihan_pasar;

-- Disable foreign key checks temporarily to drop tables cleanly if they exist
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS log_pengiriman;
DROP TABLE IF EXISTS tagihan;
DROP TABLE IF EXISTS lapak;
DROP TABLE IF EXISTS pendaftaran_pedagang;
DROP TABLE IF EXISTS pedagang;
DROP TABLE IF EXISTS tarif;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. USERS / PETUGAS TABLE
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Hashed passwords
    role ENUM('superadmin', 'admin', 'petugas') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TARIF (RENT RATE) TABLE
CREATE TABLE tarif (
    id VARCHAR(36) PRIMARY KEY,
    nama_tarif VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    nominal_harian DECIMAL(10,2) NOT NULL,
    berlaku_mulai DATE NOT NULL,
    aktif TINYINT(1) DEFAULT 1 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. PEDAGANG (MERCHANT) TABLE
CREATE TABLE pedagang (
    id VARCHAR(36) PRIMARY KEY,
    nama_pedagang VARCHAR(100) NOT NULL,
    no_ktp VARCHAR(20) UNIQUE NOT NULL,
    no_telepon VARCHAR(20) NULL,
    alamat_lengkap TEXT NULL,
    rt VARCHAR(10) NULL,
    rw VARCHAR(10) NULL,
    desa_kelurahan VARCHAR(100) NULL,
    kecamatan VARCHAR(100) NULL,
    kabupaten_kota VARCHAR(100) NULL,
    provinsi VARCHAR(100) NULL,
    kode_pos VARCHAR(10) NULL,
    nama_usaha VARCHAR(100) NULL,
    kategori_usaha ENUM('kuliner', 'pakaian', 'elektronik', 'sayuran', 'buah', 'sembako', 'lainnya') NOT NULL,
    kategori_usaha_detail VARCHAR(100) NULL,
    no_kios VARCHAR(20) UNIQUE NOT NULL,
    blok_kios VARCHAR(10) NULL DEFAULT '',
    tanggal_daftar DATE NOT NULL,
    status_pedagang ENUM('aktif', 'nonaktif', 'sementara_tutup') NOT NULL DEFAULT 'aktif',
    status_legal ENUM('legal', 'ilegal', 'proses_izin') NOT NULL DEFAULT 'legal',
    foto_ktp VARCHAR(255) NULL,
    foto_izin_usaha VARCHAR(255) NULL,
    catatan TEXT NULL,
    tarif_id VARCHAR(36) NOT NULL,
    saldo_deposit DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tarif_id) REFERENCES tarif(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexing for searching and filtering merchants efficiently
CREATE INDEX idx_pedagang_search ON pedagang (nama_pedagang, no_kios, nama_usaha);
CREATE INDEX idx_pedagang_filter ON pedagang (status_pedagang, status_legal, blok_kios, kategori_usaha);

-- 4. LAPAK TABLE
CREATE TABLE lapak (
    id VARCHAR(36) PRIMARY KEY,
    kode_lapak VARCHAR(20) UNIQUE NOT NULL,
    blok_kios VARCHAR(10) NULL,
    pedagang_id VARCHAR(36) NULL,
    tarif_id VARCHAR(36) NULL,
    kategori ENUM('kuliner', 'pakaian', 'elektronik', 'sayuran', 'buah', 'sembako', 'lainnya') DEFAULT 'kuliner',
    status_lapak ENUM('aktif', 'nonaktif', 'kosong') DEFAULT 'kosong',
    tanggal_mulai DATE NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (pedagang_id) REFERENCES pedagang(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (tarif_id) REFERENCES tarif(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. PENDAFTARAN PEDAGANG TABLE
CREATE TABLE pendaftaran_pedagang (
    id VARCHAR(36) PRIMARY KEY,
    nama_calon VARCHAR(100) NOT NULL,
    no_ktp VARCHAR(20) NOT NULL,
    no_telepon VARCHAR(20) NULL,
    alamat TEXT NULL,
    nama_usaha VARCHAR(100) NULL,
    kategori_usaha ENUM('kuliner', 'pakaian', 'elektronik', 'sayuran', 'buah', 'sembako', 'lainnya') DEFAULT 'kuliner',
    no_kios VARCHAR(20) NOT NULL,
    blok_kios VARCHAR(10) NULL,
    tarif_id VARCHAR(36) NOT NULL,
    tanggal_daftar DATE NOT NULL,
    status_pendaftaran ENUM('menunggu_verifikasi', 'disetujui', 'ditolak') DEFAULT 'menunggu_verifikasi',
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (tarif_id) REFERENCES tarif(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TAGIHAN HARIAN (DAILY BILLING) TABLE
CREATE TABLE tagihan (
    id VARCHAR(36) PRIMARY KEY,
    pedagang_id VARCHAR(36) NOT NULL,
    tarif_id VARCHAR(36) NOT NULL,
    petugas_id VARCHAR(36) NULL, -- Nullable if generated by automated system/cron
    tanggal_tagihan DATE NOT NULL,
    nominal_tagihan DECIMAL(10,2) NOT NULL,
    nominal_dibayar DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status_bayar ENUM('lunas', 'belum_bayar', 'bayar_sebagian') NOT NULL DEFAULT 'belum_bayar',
    waktu_bayar TIMESTAMP NULL DEFAULT NULL,
    metode_bayar ENUM('tunai', 'transfer', 'qris') NULL,
    no_bukti VARCHAR(100) NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pedagang_id) REFERENCES pedagang(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tarif_id) REFERENCES tarif(id) ON UPDATE CASCADE,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    -- Ensure a merchant only gets one bill per calendar day
    UNIQUE KEY uq_pedagang_tanggal (pedagang_id, tanggal_tagihan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_tagihan_tanggal ON tagihan (tanggal_tagihan);
CREATE INDEX idx_tagihan_status ON tagihan (status_bayar);

-- 7. LOG PENGIRIMAN (NOTIFICATION DELIVERY LOG) TABLE
CREATE TABLE log_pengiriman (
    id VARCHAR(36) PRIMARY KEY,
    waktu_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    penerima VARCHAR(100) NOT NULL,
    tipe_channel ENUM('whatsapp', 'email') NOT NULL,
    status ENUM('terkirim', 'gagal') NOT NULL,
    response_api TEXT NULL,
    catatan TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. AUDIT LOG TABLE
CREATE TABLE audit_log (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NULL,
    aksi VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ====================================================================
-- SEED DATA (INITIAL DUMMY DATA FOR TESTING)
-- ====================================================================

-- Seed Users (Passwords are plaintext for dev, would be hashed in php)
INSERT INTO users (id, nama, username, password, role) VALUES 
('usr-001', 'Admin Yusuf', 'yusuf123', '12345678', 'superadmin'),
('usr-002', 'Petugas Ahmad', 'ahmad', '12345678', 'petugas'),
('usr-003', 'Petugas Budi', 'budi', '12345678', 'petugas'),
('usr-004', 'Pak Bambang', 'bambang', '12345678', 'admin');

-- Seed Rent Rates (Tarif)
INSERT INTO tarif (id, nama_tarif, kategori, nominal_harian, berlaku_mulai, aktif) VALUES
('trf-001', 'Tarif Kuliner', 'kuliner', 5000.00, '2026-01-01', 1),
('trf-002', 'Tarif Pakaian', 'pakaian', 5000.00, '2026-01-01', 1),
('trf-003', 'Tarif Sembako', 'sembako', 5000.00, '2026-01-01', 1),
('trf-004', 'Tarif Buah & Sayuran', 'sayuran', 5000.00, '2026-01-01', 1),
('trf-005', 'Tarif Elektronik', 'elektronik', 5000.00, '2026-01-01', 1),
('trf-006', 'Tarif Lain-lain', 'lainnya', 5000.00, '2026-01-01', 1);

-- Seed Merchants (Pedagang)
INSERT INTO pedagang (id, nama_pedagang, no_ktp, no_telepon, nama_usaha, kategori_usaha, no_kios, blok_kios, tanggal_daftar, status_pedagang, status_legal, tarif_id, saldo_deposit) VALUES
('pdg-001', 'Sri Wahyuni', '3201020304050001', '081234567890', 'Warung Soto Mbah Sri', 'kuliner', 'A-01', 'Blok A', '2026-02-15', 'aktif', 'legal', 'trf-001', 0.00),
('pdg-002', 'H. Mahmud', '3201020304050002', '082134567891', 'Toko Sembako Jaya', 'sembako', 'A-02', 'Blok A', '2026-02-20', 'aktif', 'legal', 'trf-003', 20000.00), -- Has deposit
('pdg-003', 'Toni Setiawan', '3201020304050003', '083134567892', 'Butik Berkah Busana', 'pakaian', 'B-05', 'Blok B', '2026-03-01', 'aktif', 'legal', 'trf-002', 0.00),
('pdg-004', 'Siti Rahma', '3201020304050004', '084134567893', 'Kios Buah Segar Ibu Siti', 'buah', 'C-10', 'Blok C', '2026-03-05', 'aktif', 'ilegal', 'trf-004', 0.00), -- Ilegal merchant
('pdg-005', 'Joni Rahmat', '3201020304050005', '085134567894', 'Servis Elektronik Joni', 'elektronik', 'D-03', 'Blok D', '2026-03-10', 'sementara_tutup', 'proses_izin', 'trf-005', 0.00), -- Temporarily closed
('pdg-006', 'Budi Santoso', '3201020304050006', '086134567895', 'Kios Sayur Segar Pak Budi', 'sayuran', 'A-03', 'Blok A', '2026-03-12', 'aktif', 'legal', 'trf-004', 0.00),
('pdg-007', 'Dewi Lestari', '3201020304050007', '087134567896', 'Toko Mainan Dewi', 'lainnya', 'E-01', 'Blok E', '2026-04-01', 'nonaktif', 'legal', 'trf-006', 0.00); -- Nonaktif merchant

-- Seed Sample Bill Logs (Tagihan) for yesterday (2026-06-22)
INSERT INTO tagihan (id, pedagang_id, tarif_id, petugas_id, tanggal_tagihan, nominal_tagihan, nominal_dibayar, status_bayar, waktu_bayar, metode_bayar, no_bukti, catatan) VALUES
('tgh-001', 'pdg-001', 'trf-001', 'usr-002', '2026-06-22', 15000.00, 15000.00, 'lunas', '2026-06-22 09:30:00', 'tunai', 'STK-20260622-001', 'Lunas bayar tunai'),
('tgh-002', 'pdg-002', 'trf-003', 'usr-002', '2026-06-22', 10000.00, 10000.00, 'lunas', '2026-06-22 09:45:00', 'transfer', 'TXN-9821832', 'Bayar via transfer Bank'),
('tgh-003', 'pdg-003', 'trf-002', 'usr-003', '2026-06-22', 20000.00, 10000.00, 'bayar_sebagian', '2026-06-22 10:15:00', 'tunai', 'STK-20260622-002', 'Tunggak sisa Rp 10.000'),
('tgh-004', 'pdg-004', 'trf-004', NULL, '2026-06-22', 8000.00, 0.00, 'belum_bayar', NULL, NULL, NULL, 'Toko tutup saat ditagih'),
('tgh-006', 'pdg-006', 'trf-004', 'usr-003', '2026-06-22', 8000.00, 8000.00, 'lunas', '2026-06-22 10:40:00', 'qris', 'QR-821948', 'Lunas QRIS');

-- Note: pdg-005 (sementara_tutup) and pdg-007 (nonaktif) do not get bills for yesterday.
