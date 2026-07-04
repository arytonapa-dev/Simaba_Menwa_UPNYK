-- Database Schema
-- Sistem Monitoring dan Pengelolaan Inventaris Barang MENWA
-- Studi Kasus: UPN "Veteran" Yogyakarta

CREATE DATABASE IF NOT EXISTS db_menwa;
USE db_menwa;

-- 1. roles table
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(20) NOT NULL UNIQUE,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    nim_nip VARCHAR(30) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    photo VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. kategori_barang table
CREATE TABLE IF NOT EXISTS kategori_barang (
    kategori_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT NULL,
    is_critical TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. bidang_barang table
CREATE TABLE IF NOT EXISTS bidang_barang (
    bidang_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_bidang VARCHAR(100) NOT NULL UNIQUE,
    penanggung_jawab VARCHAR(100) NOT NULL,
    deskripsi TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. barang table
CREATE TABLE IF NOT EXISTS barang (
    barang_id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    bidang_id INT NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    deskripsi TEXT NULL,
    foto VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori_barang(kategori_id),
    FOREIGN KEY (bidang_id) REFERENCES bidang_barang(bidang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. unit_barang table
CREATE TABLE IF NOT EXISTS unit_barang (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    kode_unit VARCHAR(30) NOT NULL,
    kondisi ENUM('Baik', 'Rusak Ringan', 'Rusak Berat') NOT NULL DEFAULT 'Baik',
    status_ketersediaan ENUM('Tersedia', 'Dipinjam', 'Perbaikan', 'Hilang') NOT NULL DEFAULT 'Tersedia',
    tanggal_pengadaan DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_kode_per_barang (barang_id, kode_unit),
    FOREIGN KEY (barang_id) REFERENCES barang(barang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. peminjaman table
CREATE TABLE IF NOT EXISTS peminjaman (
    peminjaman_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('Menunggu Verifikasi', 'Menunggu Persetujuan Dansat', 'Disetujui', 'Ditolak', 'Ditolak oleh Dansat', 'Dipinjam (Berjalan)', 'Selesai') NOT NULL DEFAULT 'Menunggu Verifikasi',
    tanggal_pinjam DATE NOT NULL,
    tanggal_rencana_kembali DATE NOT NULL,
    tanggal_serah_terima DATE NULL,
    keperluan VARCHAR(500) NOT NULL,
    verifikator_id INT NULL,
    approver_dansat_id INT NULL,
    alasan_tolak TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (verifikator_id) REFERENCES users(user_id),
    FOREIGN KEY (approver_dansat_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. detail_peminjaman table
CREATE TABLE IF NOT EXISTS detail_peminjaman (
    detail_peminjaman_id INT AUTO_INCREMENT PRIMARY KEY,
    peminjaman_id INT NOT NULL,
    unit_id INT NOT NULL,
    kondisi_saat_pinjam VARCHAR(20) NOT NULL DEFAULT 'Baik',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(peminjaman_id),
    FOREIGN KEY (unit_id) REFERENCES unit_barang(unit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. pengembalian table
CREATE TABLE IF NOT EXISTS pengembalian (
    pengembalian_id INT AUTO_INCREMENT PRIMARY KEY,
    peminjaman_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('Menunggu Verifikasi', 'Selesai') NOT NULL DEFAULT 'Menunggu Verifikasi',
    tanggal_pengajuan DATE NOT NULL,
    tanggal_verifikasi DATE NULL,
    is_terlambat TINYINT(1) NOT NULL DEFAULT 0,
    hari_terlambat INT NOT NULL DEFAULT 0,
    verifikator_id INT NULL,
    catatan TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(peminjaman_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (verifikator_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. detail_pengembalian table
CREATE TABLE IF NOT EXISTS detail_pengembalian (
    detail_pengembalian_id INT AUTO_INCREMENT PRIMARY KEY,
    pengembalian_id INT NOT NULL,
    unit_id INT NOT NULL,
    kondisi_self_report VARCHAR(20) NOT NULL,
    kondisi_akhir VARCHAR(20) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pengembalian_id) REFERENCES pengembalian(pengembalian_id),
    FOREIGN KEY (unit_id) REFERENCES unit_barang(unit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. notifikasi table
CREATE TABLE IF NOT EXISTS notifikasi (
    notifikasi_id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    jenis VARCHAR(50) NOT NULL,
    judul VARCHAR(150) NOT NULL,
    pesan TEXT NOT NULL,
    link_terkait VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. audit_log table
CREATE TABLE IF NOT EXISTS audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    aktivitas VARCHAR(255) NOT NULL,
    modul VARCHAR(50) NOT NULL,
    data_sebelum JSON NULL,
    data_sesudah JSON NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
