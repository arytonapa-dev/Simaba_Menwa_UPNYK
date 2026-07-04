-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Jul 2026 pada 23.45
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_menwa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `modul` varchar(50) NOT NULL,
  `data_sebelum` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_sebelum`)),
  `data_sesudah` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_sesudah`)),
  `ip_address` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `aktivitas`, `modul`, `data_sebelum`, `data_sesudah`, `ip_address`, `created_at`) VALUES
(1, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:37:51'),
(2, NULL, 'Login Gagal (Username: adminadmin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:38:08'),
(3, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:38:38'),
(4, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:41:56'),
(5, NULL, 'Login Gagal (Username: adminbaduser)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:42:28'),
(6, NULL, 'Login Gagal (Username: baduser1)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:42:58'),
(7, NULL, 'Login Gagal (Username: baduser2)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:43:12'),
(8, NULL, 'Login Gagal (Username: baduser3)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-02 21:43:45'),
(9, NULL, 'Login Gagal (Username: kjnjsanksa)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:54:24'),
(10, NULL, 'Login Gagal (Username: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:55:38'),
(11, NULL, 'Login Gagal (Username: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:56:10'),
(12, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:56:21'),
(13, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:56:36'),
(14, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:56:42'),
(15, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:56:57'),
(16, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:57:05'),
(17, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:57:37'),
(18, 3, 'Login Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:57:42'),
(19, 3, 'Pengajuan Peminjaman Baru #1', 'peminjaman', NULL, '{\"peminjaman_id\":\"1\"}', '127.0.0.1', '2026-07-03 08:58:36'),
(20, 3, 'Logout Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:58:43'),
(21, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:58:53'),
(22, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 08:59:56'),
(23, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:00:00'),
(24, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:00:10'),
(25, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:00:13'),
(26, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:00:24'),
(27, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:00:29'),
(28, 2, 'Operator Menyetujui Peminjaman #1', 'peminjaman', '{\"peminjaman_id\":1,\"user_id\":3,\"status\":\"Menunggu Verifikasi\",\"tanggal_pinjam\":\"2026-07-03\",\"tanggal_rencana_kembali\":\"2026-07-06\",\"tanggal_serah_terima\":null,\"keperluan\":\"latihan dasar dilapangan rektorat\",\"verifikator_id\":null,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-03 08:58:36\",\"updated_at\":\"2026-07-03 08:58:36\",\"borrower_name\":\"Anggota Aktif\",\"borrower_nim\":\"333333333\",\"borrower_phone\":\"081234567892\",\"verifikator_name\":null,\"dansat_name\":null}', '{\"peminjaman_id\":1,\"user_id\":3,\"status\":\"Disetujui\",\"tanggal_pinjam\":\"2026-07-03\",\"tanggal_rencana_kembali\":\"2026-07-06\",\"tanggal_serah_terima\":null,\"keperluan\":\"latihan dasar dilapangan rektorat\",\"verifikator_id\":2,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-03 08:58:36\",\"updated_at\":\"2026-07-03 09:00:42\"}', '127.0.0.1', '2026-07-03 09:00:42'),
(29, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:01:05'),
(30, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:01:09'),
(31, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:01:19'),
(32, 3, 'Login Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:01:25'),
(33, 3, 'Logout Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:01:41'),
(34, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:01:52'),
(35, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:02:03'),
(36, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:02:13'),
(37, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 09:16:55'),
(38, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 10:43:17'),
(39, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-03 22:29:35'),
(40, 1, 'Update Profil Pengguna: admin', 'users', '{\"user_id\":1,\"role_id\":1,\"full_name\":\"Administrator Utama\",\"nim_nip\":\"111111111\",\"username\":\"admin\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"admin@upnyk.ac.id\",\"phone\":\"081234567890\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"user_id\":1,\"role_id\":1,\"full_name\":\"Administrator Utama\",\"nim_nip\":\"111111111\",\"username\":\"admin\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"admin@upnyk.ac.id\",\"phone\":\"081234567890\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '127.0.0.1', '2026-07-03 22:56:15'),
(41, 1, 'Update Profil Pengguna: admin', 'users', '{\"user_id\":1,\"role_id\":1,\"full_name\":\"Administrator Utama\",\"nim_nip\":\"111111111\",\"username\":\"admin\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"admin@upnyk.ac.id\",\"phone\":\"081234567890\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"user_id\":1,\"role_id\":1,\"full_name\":\"Administrator Utama\",\"nim_nip\":\"111111111\",\"username\":\"admin\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"admin@upnyk.ac.id\",\"phone\":\"081234567890\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '127.0.0.1', '2026-07-03 22:56:21'),
(42, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 00:01:43'),
(43, 1, 'Ubah Bidang Barang: Bidang Logistik', 'bidang_barang', '{\"bidang_id\":1,\"nama_bidang\":\"Bidang Logistik\",\"penanggung_jawab\":\"Sersan Mayor Logistik\",\"deskripsi\":\"Mengelola penyimpanan dan perawatan seluruh barang inventaris\",\"created_at\":\"2026-07-02 21:37:30\"}', '{\"bidang_id\":1,\"nama_bidang\":\"Bidang Logistik\",\"penanggung_jawab\":\"Marx\",\"deskripsi\":\"Mengelola penyimpanan dan perawatan seluruh barang inventaris\",\"created_at\":\"2026-07-02 21:37:30\"}', '127.0.0.1', '2026-07-04 00:22:44'),
(44, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 00:56:55'),
(45, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:00:24'),
(46, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:00:50'),
(47, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:03:24'),
(48, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:03:28'),
(49, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:03:40'),
(50, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:03:51'),
(51, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:07:26'),
(52, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:07:31'),
(53, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:07:42'),
(54, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:07:47'),
(55, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:07:57'),
(56, 3, 'Login Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:08:04'),
(57, 3, 'Pengajuan Peminjaman Baru #2', 'peminjaman', NULL, '{\"peminjaman_id\":\"2\"}', '127.0.0.1', '2026-07-04 01:09:07'),
(58, 3, 'Logout Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:09:22'),
(59, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:09:28'),
(60, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:09:40'),
(61, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:09:45'),
(62, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:10:18'),
(63, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:10:27'),
(64, 2, 'Eskalasi Peminjaman Kritis #2 ke Dansat', 'peminjaman', '{\"peminjaman_id\":2,\"user_id\":3,\"status\":\"Menunggu Verifikasi\",\"tanggal_pinjam\":\"2026-07-04\",\"tanggal_rencana_kembali\":\"2026-07-07\",\"tanggal_serah_terima\":null,\"keperluan\":\"latihan dasar\",\"verifikator_id\":null,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-04 01:09:07\",\"updated_at\":\"2026-07-04 01:09:07\",\"borrower_name\":\"Anggota Aktif\",\"borrower_nim\":\"333333333\",\"borrower_phone\":\"081234567892\",\"verifikator_name\":null,\"dansat_name\":null}', '{\"peminjaman_id\":2,\"user_id\":3,\"status\":\"Menunggu Persetujuan Dansat\",\"tanggal_pinjam\":\"2026-07-04\",\"tanggal_rencana_kembali\":\"2026-07-07\",\"tanggal_serah_terima\":null,\"keperluan\":\"latihan dasar\",\"verifikator_id\":2,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-04 01:09:07\",\"updated_at\":\"2026-07-04 01:11:06\"}', '127.0.0.1', '2026-07-04 01:11:06'),
(65, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:11:19'),
(66, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:11:23'),
(67, 4, 'Dansat Menyetujui Peminjaman Kritis #2', 'peminjaman', '{\"peminjaman_id\":2,\"user_id\":3,\"status\":\"Menunggu Persetujuan Dansat\",\"tanggal_pinjam\":\"2026-07-04\",\"tanggal_rencana_kembali\":\"2026-07-07\",\"tanggal_serah_terima\":null,\"keperluan\":\"latihan dasar\",\"verifikator_id\":2,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-04 01:09:07\",\"updated_at\":\"2026-07-04 01:11:06\",\"borrower_name\":\"Anggota Aktif\",\"borrower_nim\":\"333333333\",\"borrower_phone\":\"081234567892\",\"verifikator_name\":\"Operator Logistik\",\"dansat_name\":null}', '{\"peminjaman_id\":2,\"user_id\":3,\"status\":\"Disetujui\",\"tanggal_pinjam\":\"2026-07-04\",\"tanggal_rencana_kembali\":\"2026-07-07\",\"tanggal_serah_terima\":null,\"keperluan\":\"latihan dasar\",\"verifikator_id\":2,\"approver_dansat_id\":4,\"alasan_tolak\":null,\"created_at\":\"2026-07-04 01:09:07\",\"updated_at\":\"2026-07-04 01:11:33\"}', '127.0.0.1', '2026-07-04 01:11:33'),
(68, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:11:39'),
(69, 4, 'Login Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:11:43'),
(70, 4, 'Logout Berhasil (User: dansat)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:11:50'),
(71, 3, 'Login Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:11:54'),
(72, 3, 'Logout Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:12:26'),
(73, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:12:32'),
(74, 2, 'Pembaruan kondisi unit SNP-014 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":3,\"barang_id\":2,\"kode_unit\":\"SNP-014\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-02-15\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"unit_id\":3,\"barang_id\":2,\"kode_unit\":\"SNP-014\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-02-15\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 01:15:57\"}', '127.0.0.1', '2026-07-04 01:15:57'),
(75, 2, 'Serah Terima Peminjaman #2 Berhasil', 'peminjaman', NULL, NULL, '127.0.0.1', '2026-07-04 01:15:57'),
(76, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:16:04'),
(77, 3, 'Login Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:16:07'),
(78, 3, 'Pengajuan Pengembalian Baru #1', 'pengembalian', NULL, '{\"pengembalian_id\":\"1\"}', '127.0.0.1', '2026-07-04 01:16:41'),
(79, 3, 'Logout Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:20:44'),
(80, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 01:20:49'),
(81, 2, 'Pembaruan kondisi unit TND-001 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":1,\"barang_id\":1,\"kode_unit\":\"TND-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-01-10\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"unit_id\":1,\"barang_id\":1,\"kode_unit\":\"TND-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-01-10\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 01:21:38\"}', '127.0.0.1', '2026-07-04 01:21:38'),
(82, 2, 'Serah Terima Peminjaman #1 Berhasil', 'peminjaman', NULL, NULL, '127.0.0.1', '2026-07-04 01:21:38'),
(83, 2, 'Pembaruan kondisi unit SNP-014 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":3,\"barang_id\":2,\"kode_unit\":\"SNP-014\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-02-15\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 01:15:57\"}', '{\"unit_id\":3,\"barang_id\":2,\"kode_unit\":\"SNP-014\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-02-15\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 01:21:51\"}', '127.0.0.1', '2026-07-04 01:21:51'),
(84, 2, 'Verifikasi Pengembalian #1', 'pengembalian', NULL, '{\"pengembalian_id\":1}', '127.0.0.1', '2026-07-04 01:21:51'),
(85, 2, 'Tambah Barang Master: matras', 'barang', NULL, NULL, '127.0.0.1', '2026-07-04 01:22:58'),
(86, 2, 'Tambah Unit Barang: MTR-001 (matras)', 'unit_barang', NULL, NULL, '127.0.0.1', '2026-07-04 01:27:17'),
(87, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 02:26:24'),
(88, 3, 'Login Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 02:26:29'),
(89, 3, 'Logout Berhasil (User: anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 02:32:43'),
(90, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 02:32:48'),
(91, 2, 'Pembaruan kondisi unit SNP-015 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":4,\"barang_id\":2,\"kode_unit\":\"SNP-015\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-02-15\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"unit_id\":4,\"barang_id\":2,\"kode_unit\":\"SNP-015\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-02-15\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 03:43:31\"}', '127.0.0.1', '2026-07-04 03:43:31'),
(92, 2, 'Pembaruan kondisi unit TND-002 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":2,\"barang_id\":1,\"kode_unit\":\"TND-002\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-01-10\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"unit_id\":2,\"barang_id\":1,\"kode_unit\":\"TND-002\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-01-10\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 03:43:44\"}', '127.0.0.1', '2026-07-04 03:43:44'),
(93, 2, 'Ubah Barang Master: Matras', 'barang', '{\"barang_id\":4,\"kategori_id\":3,\"bidang_id\":1,\"nama_barang\":\"matras\",\"satuan\":\"buah\",\"deskripsi\":\"\",\"foto\":\"barang_1783102978_977.png\",\"created_at\":\"2026-07-04 01:22:58\",\"updated_at\":null}', '{\"barang_id\":4,\"kategori_id\":3,\"bidang_id\":1,\"nama_barang\":\"Matras\",\"satuan\":\"buah\",\"deskripsi\":\"\",\"foto\":\"barang_1783102978_977.png\",\"created_at\":\"2026-07-04 01:22:58\",\"updated_at\":\"2026-07-04 03:48:20\"}', '127.0.0.1', '2026-07-04 03:48:20'),
(94, 2, 'Tambah 6 Unit Massal untuk Baret Menwa', 'unit_barang', NULL, NULL, '127.0.0.1', '2026-07-04 03:49:23'),
(95, 2, 'Ubah Barang Master: Baret Menwa', 'barang', '{\"barang_id\":3,\"kategori_id\":2,\"bidang_id\":1,\"nama_barang\":\"Baret Menwa\",\"satuan\":\"buah\",\"deskripsi\":\"Baret ungu resmi Menwa Mahakarta\",\"foto\":null,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"barang_id\":3,\"kategori_id\":2,\"bidang_id\":1,\"nama_barang\":\"Baret Menwa\",\"satuan\":\"buah\",\"deskripsi\":\"Baret ungu resmi Menwa Mahakarta\",\"foto\":\"barang_1783111934_106.jpg\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 03:52:14\"}', '127.0.0.1', '2026-07-04 03:52:14'),
(96, 2, 'Ubah Barang Master: Tenda Pleton', 'barang', '{\"barang_id\":1,\"kategori_id\":1,\"bidang_id\":1,\"nama_barang\":\"Tenda Pleton\",\"satuan\":\"unit\",\"deskripsi\":\"Tenda pleton kapasitas 30 orang warna loreng\",\"foto\":null,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"barang_id\":1,\"kategori_id\":1,\"bidang_id\":1,\"nama_barang\":\"Tenda Pleton\",\"satuan\":\"unit\",\"deskripsi\":\"Tenda pleton kapasitas 30 orang warna loreng\",\"foto\":\"barang_1783111951_307.jpeg\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 03:52:31\"}', '127.0.0.1', '2026-07-04 03:52:31'),
(97, 2, 'Ubah Barang Master: Senapan Latih', 'barang', '{\"barang_id\":2,\"kategori_id\":3,\"bidang_id\":2,\"nama_barang\":\"Senapan Latih\",\"satuan\":\"unit\",\"deskripsi\":\"Senapan dummy kayu untuk latihan PBB\",\"foto\":null,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"barang_id\":2,\"kategori_id\":3,\"bidang_id\":2,\"nama_barang\":\"Senapan Latih\",\"satuan\":\"unit\",\"deskripsi\":\"Senapan dummy kayu untuk latihan PBB\",\"foto\":\"barang_1783112012_371.jpg\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 03:53:32\"}', '127.0.0.1', '2026-07-04 03:53:32'),
(98, 2, 'Ubah Barang Master: Matras', 'barang', '{\"barang_id\":4,\"kategori_id\":3,\"bidang_id\":1,\"nama_barang\":\"Matras\",\"satuan\":\"buah\",\"deskripsi\":\"\",\"foto\":\"barang_1783102978_977.png\",\"created_at\":\"2026-07-04 01:22:58\",\"updated_at\":\"2026-07-04 03:48:20\"}', '{\"barang_id\":4,\"kategori_id\":3,\"bidang_id\":1,\"nama_barang\":\"Matras\",\"satuan\":\"buah\",\"deskripsi\":\"\",\"foto\":\"barang_1783112148_518.jpg\",\"created_at\":\"2026-07-04 01:22:58\",\"updated_at\":\"2026-07-04 03:55:48\"}', '127.0.0.1', '2026-07-04 03:55:48'),
(99, 2, 'Pembaruan kondisi unit MTR-001 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":6,\"barang_id\":4,\"kode_unit\":\"MTR-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 01:27:17\",\"updated_at\":null}', '{\"unit_id\":6,\"barang_id\":4,\"kode_unit\":\"MTR-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 01:27:17\",\"updated_at\":\"2026-07-04 03:57:16\"}', '127.0.0.1', '2026-07-04 03:57:16'),
(100, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 03:59:21'),
(101, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 03:59:33'),
(102, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:08:47'),
(103, 1, 'Login Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:08:52'),
(104, 1, 'Ubah Bidang Barang: Bidang Pokma', 'bidang_barang', '{\"bidang_id\":1,\"nama_bidang\":\"Bidang Logistik\",\"penanggung_jawab\":\"Marx\",\"deskripsi\":\"Mengelola penyimpanan dan perawatan seluruh barang inventaris\",\"created_at\":\"2026-07-02 21:37:30\"}', '{\"bidang_id\":1,\"nama_bidang\":\"Bidang Pokma\",\"penanggung_jawab\":\"Kautsar Ulindani\",\"deskripsi\":\"Mengelola penyimpanan dan perawatan seluruh barang inventaris\",\"created_at\":\"2026-07-02 21:37:30\"}', '127.0.0.1', '2026-07-04 04:10:06'),
(105, 1, 'Ubah Bidang Barang: Bidang Diklat', 'bidang_barang', '{\"bidang_id\":2,\"nama_bidang\":\"Bidang Operasi\",\"penanggung_jawab\":\"Sersan Kepala Operasi\",\"deskripsi\":\"Mengatur kebutuhan latihan taktis dan tugas luar\",\"created_at\":\"2026-07-02 21:37:30\"}', '{\"bidang_id\":2,\"nama_bidang\":\"Bidang Diklat\",\"penanggung_jawab\":\"Arilda Sarifah Umahatika\",\"deskripsi\":\"Mengatur kebutuhan latihan taktis dan tugas luar\",\"created_at\":\"2026-07-02 21:37:30\"}', '127.0.0.1', '2026-07-04 04:10:56'),
(106, 1, 'Ubah Bidang Barang: Bidang Operasi', 'bidang_barang', '{\"bidang_id\":2,\"nama_bidang\":\"Bidang Diklat\",\"penanggung_jawab\":\"Arilda Sarifah Umahatika\",\"deskripsi\":\"Mengatur kebutuhan latihan taktis dan tugas luar\",\"created_at\":\"2026-07-02 21:37:30\"}', '{\"bidang_id\":2,\"nama_bidang\":\"Bidang Operasi\",\"penanggung_jawab\":\"Setya Lestari\",\"deskripsi\":\"Mengatur kebutuhan latihan taktis dan tugas luar\",\"created_at\":\"2026-07-02 21:37:30\"}', '127.0.0.1', '2026-07-04 04:11:50'),
(107, 1, 'Ubah Bidang Barang: Bidang Latihan', 'bidang_barang', '{\"bidang_id\":3,\"nama_bidang\":\"Bidang Latihan\",\"penanggung_jawab\":\"Sersan Kepala Latihan\",\"deskripsi\":\"Mengurus perlengkapan latihan dasar dan lanjutan\",\"created_at\":\"2026-07-02 21:37:30\"}', '{\"bidang_id\":3,\"nama_bidang\":\"Bidang Latihan\",\"penanggung_jawab\":\"Arilda Sarifah\",\"deskripsi\":\"Mengurus perlengkapan latihan dasar dan lanjutan\",\"created_at\":\"2026-07-02 21:37:30\"}', '127.0.0.1', '2026-07-04 04:12:09'),
(108, 1, 'Ubah Detail Pengguna: AryTonapa', 'users', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Anggota Aktif\",\"nim_nip\":\"333333333\",\"username\":\"anggota\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081234567892\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"AryTonapa\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:13:53\"}', '127.0.0.1', '2026-07-04 04:13:53'),
(109, 1, 'Ubah Detail Pengguna: Anggota', 'users', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"AryTonapa\",\"password_hash\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\\/.og\\/at2.uheWG\\/igi\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:13:53\"}', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"Anggota\",\"password_hash\":\"$2y$10$oXzi3tHJiokwUMJTLwo9COo1pbG8oQ5VUt09XljG1tn1fY5\\/65b4q\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:14:22\"}', '127.0.0.1', '2026-07-04 04:14:22'),
(110, 1, 'Ubah Detail Pengguna: Anggota', 'users', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"Anggota\",\"password_hash\":\"$2y$10$oXzi3tHJiokwUMJTLwo9COo1pbG8oQ5VUt09XljG1tn1fY5\\/65b4q\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:14:22\"}', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"Anggota\",\"password_hash\":\"$2y$10$h2TcUnUeVAUuwV7Ho2vpreCUxYQQHCG1GM7pPtNvX5BdZjZ1aFfJW\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:14:39\"}', '127.0.0.1', '2026-07-04 04:14:39'),
(111, 1, 'Ubah Detail Pengguna: Anggota', 'users', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"Anggota\",\"password_hash\":\"$2y$10$h2TcUnUeVAUuwV7Ho2vpreCUxYQQHCG1GM7pPtNvX5BdZjZ1aFfJW\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:14:39\"}', '{\"user_id\":3,\"role_id\":3,\"full_name\":\"Maximus Ary Tonapa\",\"nim_nip\":\"24051013508\",\"username\":\"Anggota\",\"password_hash\":\"$2y$10$Le.QVsQBAVvkJDVIT\\/9jPO3OLrqsDHZrgxOMpL0e4.3BbJbX.FtOu\",\"email\":\"anggota@upnyk.ac.id\",\"phone\":\"081380065743\",\"photo\":null,\"is_active\":1,\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:15:03\"}', '127.0.0.1', '2026-07-04 04:15:03'),
(112, 1, 'Logout Berhasil (User: admin)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:23:51'),
(113, 3, 'Login Berhasil (User: Anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:24:42'),
(114, 3, 'Pengajuan Pengembalian Baru #2', 'pengembalian', NULL, '{\"pengembalian_id\":\"2\"}', '127.0.0.1', '2026-07-04 04:25:02'),
(115, 3, 'Logout Berhasil (User: Anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:25:16'),
(116, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:25:28'),
(117, 2, 'Pembaruan kondisi unit TND-001 menjadi Rusak Ringan (Perbaikan)', 'unit_barang', '{\"unit_id\":1,\"barang_id\":1,\"kode_unit\":\"TND-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-01-10\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 01:21:38\"}', '{\"unit_id\":1,\"barang_id\":1,\"kode_unit\":\"TND-001\",\"kondisi\":\"Rusak Ringan\",\"status_ketersediaan\":\"Perbaikan\",\"tanggal_pengadaan\":\"2026-01-10\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:25:46\"}', '127.0.0.1', '2026-07-04 04:25:46'),
(118, 2, 'Verifikasi Pengembalian #2', 'pengembalian', NULL, '{\"pengembalian_id\":2}', '127.0.0.1', '2026-07-04 04:25:46'),
(119, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:28:07'),
(120, 3, 'Login Berhasil (User: Anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:28:11'),
(121, 3, 'Pengajuan Peminjaman Baru #3', 'peminjaman', NULL, '{\"peminjaman_id\":\"3\"}', '127.0.0.1', '2026-07-04 04:29:10'),
(122, 3, 'Logout Berhasil (User: Anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:29:27'),
(123, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:29:31'),
(124, 2, 'Operator Menyetujui Peminjaman #3', 'peminjaman', '{\"peminjaman_id\":3,\"user_id\":3,\"status\":\"Menunggu Verifikasi\",\"tanggal_pinjam\":\"2026-07-04\",\"tanggal_rencana_kembali\":\"2026-07-07\",\"tanggal_serah_terima\":null,\"keperluan\":\"Upacara Kebangsaan\",\"verifikator_id\":null,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-04 04:29:10\",\"updated_at\":\"2026-07-04 04:29:10\",\"borrower_name\":\"Maximus Ary Tonapa\",\"borrower_nim\":\"24051013508\",\"borrower_phone\":\"081380065743\",\"verifikator_name\":null,\"dansat_name\":null}', '{\"peminjaman_id\":3,\"user_id\":3,\"status\":\"Disetujui\",\"tanggal_pinjam\":\"2026-07-04\",\"tanggal_rencana_kembali\":\"2026-07-07\",\"tanggal_serah_terima\":null,\"keperluan\":\"Upacara Kebangsaan\",\"verifikator_id\":2,\"approver_dansat_id\":null,\"alasan_tolak\":null,\"created_at\":\"2026-07-04 04:29:10\",\"updated_at\":\"2026-07-04 04:29:58\"}', '127.0.0.1', '2026-07-04 04:29:58'),
(125, 2, 'Pembaruan kondisi unit BRT-001 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":5,\"barang_id\":3,\"kode_unit\":\"BRT-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-03-01\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":null}', '{\"unit_id\":5,\"barang_id\":3,\"kode_unit\":\"BRT-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-03-01\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:30:07\"}', '127.0.0.1', '2026-07-04 04:30:07'),
(126, 2, 'Pembaruan kondisi unit BRT-002 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":7,\"barang_id\":3,\"kode_unit\":\"BRT-002\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":null}', '{\"unit_id\":7,\"barang_id\":3,\"kode_unit\":\"BRT-002\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '127.0.0.1', '2026-07-04 04:30:07'),
(127, 2, 'Pembaruan kondisi unit BRT-003 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":8,\"barang_id\":3,\"kode_unit\":\"BRT-003\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":null}', '{\"unit_id\":8,\"barang_id\":3,\"kode_unit\":\"BRT-003\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '127.0.0.1', '2026-07-04 04:30:07'),
(128, 2, 'Pembaruan kondisi unit BRT-004 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":9,\"barang_id\":3,\"kode_unit\":\"BRT-004\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":null}', '{\"unit_id\":9,\"barang_id\":3,\"kode_unit\":\"BRT-004\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '127.0.0.1', '2026-07-04 04:30:07'),
(129, 2, 'Pembaruan kondisi unit BRT-005 menjadi Baik (Dipinjam)', 'unit_barang', '{\"unit_id\":10,\"barang_id\":3,\"kode_unit\":\"BRT-005\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":null}', '{\"unit_id\":10,\"barang_id\":3,\"kode_unit\":\"BRT-005\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '127.0.0.1', '2026-07-04 04:30:07'),
(130, 2, 'Serah Terima Peminjaman #3 Berhasil', 'peminjaman', NULL, NULL, '127.0.0.1', '2026-07-04 04:30:07'),
(131, 2, 'Logout Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:30:18'),
(132, 3, 'Login Berhasil (User: Anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:30:22'),
(133, 3, 'Pengajuan Pengembalian Baru #3', 'pengembalian', NULL, '{\"pengembalian_id\":\"3\"}', '127.0.0.1', '2026-07-04 04:30:52'),
(134, 3, 'Logout Berhasil (User: Anggota)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:31:03'),
(135, 2, 'Login Berhasil (User: operator)', 'auth', NULL, NULL, '127.0.0.1', '2026-07-04 04:31:08'),
(136, 2, 'Pembaruan kondisi unit BRT-001 menjadi Rusak Ringan (Perbaikan)', 'unit_barang', '{\"unit_id\":5,\"barang_id\":3,\"kode_unit\":\"BRT-001\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-03-01\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:30:07\"}', '{\"unit_id\":5,\"barang_id\":3,\"kode_unit\":\"BRT-001\",\"kondisi\":\"Rusak Ringan\",\"status_ketersediaan\":\"Perbaikan\",\"tanggal_pengadaan\":\"2026-03-01\",\"created_at\":\"2026-07-02 21:37:30\",\"updated_at\":\"2026-07-04 04:31:30\"}', '127.0.0.1', '2026-07-04 04:31:30'),
(137, 2, 'Pembaruan kondisi unit BRT-002 menjadi Rusak Berat (Perbaikan)', 'unit_barang', '{\"unit_id\":7,\"barang_id\":3,\"kode_unit\":\"BRT-002\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '{\"unit_id\":7,\"barang_id\":3,\"kode_unit\":\"BRT-002\",\"kondisi\":\"Rusak Berat\",\"status_ketersediaan\":\"Perbaikan\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:31:30\"}', '127.0.0.1', '2026-07-04 04:31:30'),
(138, 2, 'Pembaruan kondisi unit BRT-003 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":8,\"barang_id\":3,\"kode_unit\":\"BRT-003\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '{\"unit_id\":8,\"barang_id\":3,\"kode_unit\":\"BRT-003\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:31:30\"}', '127.0.0.1', '2026-07-04 04:31:30'),
(139, 2, 'Pembaruan kondisi unit BRT-004 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":9,\"barang_id\":3,\"kode_unit\":\"BRT-004\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '{\"unit_id\":9,\"barang_id\":3,\"kode_unit\":\"BRT-004\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:31:30\"}', '127.0.0.1', '2026-07-04 04:31:30'),
(140, 2, 'Pembaruan kondisi unit BRT-005 menjadi Baik (Tersedia)', 'unit_barang', '{\"unit_id\":10,\"barang_id\":3,\"kode_unit\":\"BRT-005\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Dipinjam\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:30:07\"}', '{\"unit_id\":10,\"barang_id\":3,\"kode_unit\":\"BRT-005\",\"kondisi\":\"Baik\",\"status_ketersediaan\":\"Tersedia\",\"tanggal_pengadaan\":\"2026-07-04\",\"created_at\":\"2026-07-04 03:49:23\",\"updated_at\":\"2026-07-04 04:31:30\"}', '127.0.0.1', '2026-07-04 04:31:30'),
(141, 2, 'Verifikasi Pengembalian #3', 'pengembalian', NULL, '{\"pengembalian_id\":3}', '127.0.0.1', '2026-07-04 04:31:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `barang_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `bidang_id` int(11) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`barang_id`, `kategori_id`, `bidang_id`, `nama_barang`, `satuan`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Tenda Pleton', 'unit', 'Tenda pleton kapasitas 30 orang warna loreng', 'barang_1783111951_307.jpeg', '2026-07-02 21:37:30', '2026-07-04 03:52:31'),
(2, 3, 2, 'Senapan Latih', 'unit', 'Senapan dummy kayu untuk latihan PBB', 'barang_1783112012_371.jpg', '2026-07-02 21:37:30', '2026-07-04 03:53:32'),
(3, 2, 1, 'Baret Menwa', 'buah', 'Baret ungu resmi Menwa Mahakarta', 'barang_1783111934_106.jpg', '2026-07-02 21:37:30', '2026-07-04 03:52:14'),
(4, 3, 1, 'Matras', 'buah', '', 'barang_1783112148_518.jpg', '2026-07-04 01:22:58', '2026-07-04 03:55:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bidang_barang`
--

CREATE TABLE `bidang_barang` (
  `bidang_id` int(11) NOT NULL,
  `nama_bidang` varchar(100) NOT NULL,
  `penanggung_jawab` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bidang_barang`
--

INSERT INTO `bidang_barang` (`bidang_id`, `nama_bidang`, `penanggung_jawab`, `deskripsi`, `created_at`) VALUES
(1, 'Bidang Pokma', 'Kautsar Ulindani', 'Mengelola penyimpanan dan perawatan seluruh barang inventaris', '2026-07-02 21:37:30'),
(2, 'Bidang Operasi', 'Setya Lestari', 'Mengatur kebutuhan latihan taktis dan tugas luar', '2026-07-02 21:37:30'),
(3, 'Bidang Latihan', 'Arilda Sarifah', 'Mengurus perlengkapan latihan dasar dan lanjutan', '2026-07-02 21:37:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `detail_peminjaman_id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `kondisi_saat_pinjam` varchar(20) NOT NULL DEFAULT 'Baik',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`detail_peminjaman_id`, `peminjaman_id`, `unit_id`, `kondisi_saat_pinjam`, `created_at`) VALUES
(1, 1, 1, 'Baik', '2026-07-03 08:58:36'),
(2, 2, 3, 'Baik', '2026-07-04 01:09:07'),
(3, 3, 5, 'Baik', '2026-07-04 04:29:10'),
(4, 3, 7, 'Baik', '2026-07-04 04:29:10'),
(5, 3, 8, 'Baik', '2026-07-04 04:29:10'),
(6, 3, 9, 'Baik', '2026-07-04 04:29:10'),
(7, 3, 10, 'Baik', '2026-07-04 04:29:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pengembalian`
--

CREATE TABLE `detail_pengembalian` (
  `detail_pengembalian_id` int(11) NOT NULL,
  `pengembalian_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `kondisi_self_report` varchar(20) NOT NULL,
  `kondisi_akhir` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_pengembalian`
--

INSERT INTO `detail_pengembalian` (`detail_pengembalian_id`, `pengembalian_id`, `unit_id`, `kondisi_self_report`, `kondisi_akhir`, `created_at`) VALUES
(1, 1, 3, 'Baik', 'Baik', '2026-07-04 01:16:41'),
(2, 2, 1, 'Rusak Ringan', 'Rusak Ringan', '2026-07-04 04:25:02'),
(3, 3, 5, 'Rusak Ringan', 'Rusak Ringan', '2026-07-04 04:30:52'),
(4, 3, 7, 'Rusak Berat', 'Rusak Berat', '2026-07-04 04:30:52'),
(5, 3, 8, 'Baik', 'Baik', '2026-07-04 04:30:52'),
(6, 3, 9, 'Baik', 'Baik', '2026-07-04 04:30:52'),
(7, 3, 10, 'Baik', 'Baik', '2026-07-04 04:30:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_critical` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori_barang`
--

INSERT INTO `kategori_barang` (`kategori_id`, `nama_kategori`, `deskripsi`, `is_critical`, `created_at`) VALUES
(1, 'Perlengkapan Lapangan', 'Perlengkapan umum latihan lapangan (tenda, ransel, matras)', 0, '2026-07-02 21:37:30'),
(2, 'Atribut Seragam', 'Pakaian Dinas Lapangan (PDL), Pakaian Dinas Upacara (PDU), baret', 0, '2026-07-02 21:37:30'),
(3, 'Perlengkapan Khusus', 'Senjata latihan, amunisi hampa, kompas prisma, GPS', 1, '2026-07-02 21:37:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `notifikasi_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `link_terkait` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`notifikasi_id`, `recipient_id`, `jenis`, `judul`, `pesan`, `link_terkait`, `is_read`, `created_at`) VALUES
(1, 2, 'Peminjaman Baru', 'Pengajuan Peminjaman Baru', 'Terdapat pengajuan peminjaman baru dari Anggota yang membutuhkan verifikasi.', '/index.php?controller=peminjaman&action=detail&id=1', 1, '2026-07-03 08:58:36'),
(2, 3, 'Peminjaman Disetujui', 'Pengajuan Peminjaman Disetujui', 'Pengajuan peminjaman #1 telah disetujui. Silakan menemui Operator untuk serah terima barang.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-03 09:00:42'),
(3, 2, 'Peminjaman Baru', 'Pengajuan Peminjaman Baru', 'Terdapat pengajuan peminjaman baru dari Anggota yang membutuhkan verifikasi.', '/index.php?controller=peminjaman&action=detail&id=2', 1, '2026-07-04 01:09:07'),
(4, 4, 'Persetujuan Kritis', 'Peminjaman Kritis Butuh Persetujuan', 'Terdapat pengajuan peminjaman barang kritis #2 yang membutuhkan keputusan Anda.', '/index.php?controller=peminjaman&action=verifikasiKritis&id=2', 0, '2026-07-04 01:11:06'),
(5, 3, 'Peminjaman Kritis Disetujui', 'Peminjaman Kritis Disetujui Dansat', 'Pengajuan peminjaman kritis #2 disetujui Dansat. Silakan hubungi Operator untuk serah terima barang.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 01:11:33'),
(6, 3, 'Barang Diserahkan', 'Serah Terima Barang Selesai', 'Barang peminjaman #2 telah Anda terima secara fisik. Selamat berlatih.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 01:15:57'),
(8, 3, 'Barang Diserahkan', 'Serah Terima Barang Selesai', 'Barang peminjaman #1 telah Anda terima secara fisik. Selamat berlatih.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 01:21:38'),
(9, 3, 'Peminjaman Selesai', 'Peminjaman Selesai Terverifikasi', 'Peminjaman #2 telah dinyatakan selesai setelah seluruh unit diverifikasi pengembaliannya.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 01:21:51'),
(10, 2, 'Pengembalian Baru', 'Pengajuan Pengembalian', 'Terdapat pengajuan pengembalian barang baru dari Anggota yang membutuhkan verifikasi.', '/index.php?controller=pengembalian&action=detail&id=2', 1, '2026-07-04 04:25:02'),
(11, 3, 'Peminjaman Selesai', 'Peminjaman Selesai Terverifikasi', 'Peminjaman #1 telah dinyatakan selesai setelah seluruh unit diverifikasi pengembaliannya.', '/index.php?controller=peminjaman&action=riwayat', 1, '2026-07-04 04:25:46'),
(12, 2, 'Peminjaman Baru', 'Pengajuan Peminjaman Baru', 'Terdapat pengajuan peminjaman baru dari Anggota yang membutuhkan verifikasi.', '/index.php?controller=peminjaman&action=detail&id=3', 0, '2026-07-04 04:29:10'),
(13, 3, 'Peminjaman Disetujui', 'Pengajuan Peminjaman Disetujui', 'Pengajuan peminjaman #3 telah disetujui. Silakan menemui Operator untuk serah terima barang.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 04:29:58'),
(14, 3, 'Barang Diserahkan', 'Serah Terima Barang Selesai', 'Barang peminjaman #3 telah Anda terima secara fisik. Selamat berlatih.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 04:30:07'),
(15, 2, 'Pengembalian Baru', 'Pengajuan Pengembalian', 'Terdapat pengajuan pengembalian barang baru dari Anggota yang membutuhkan verifikasi.', '/index.php?controller=pengembalian&action=detail&id=3', 0, '2026-07-04 04:30:52'),
(16, 1, 'Kerusakan', 'Pemberitahuan: Unit Rusak Berat Terdeteksi', 'Unit BRT-002 dari barang Baret Menwa diubah kondisinya menjadi Rusak Berat.', NULL, 0, '2026-07-04 04:31:30'),
(17, 3, 'Peminjaman Selesai', 'Peminjaman Selesai Terverifikasi', 'Peminjaman #3 telah dinyatakan selesai setelah seluruh unit diverifikasi pengembaliannya.', '/index.php?controller=peminjaman&action=riwayat', 0, '2026-07-04 04:31:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `peminjaman_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Menunggu Verifikasi','Menunggu Persetujuan Dansat','Disetujui','Ditolak','Ditolak oleh Dansat','Dipinjam (Berjalan)','Selesai') NOT NULL DEFAULT 'Menunggu Verifikasi',
  `tanggal_pinjam` date NOT NULL,
  `tanggal_rencana_kembali` date NOT NULL,
  `tanggal_serah_terima` date DEFAULT NULL,
  `keperluan` varchar(500) NOT NULL,
  `verifikator_id` int(11) DEFAULT NULL,
  `approver_dansat_id` int(11) DEFAULT NULL,
  `alasan_tolak` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`peminjaman_id`, `user_id`, `status`, `tanggal_pinjam`, `tanggal_rencana_kembali`, `tanggal_serah_terima`, `keperluan`, `verifikator_id`, `approver_dansat_id`, `alasan_tolak`, `created_at`, `updated_at`) VALUES
(1, 3, 'Selesai', '2026-07-03', '2026-07-06', '2026-07-04', 'latihan dasar dilapangan rektorat', 2, NULL, NULL, '2026-07-03 08:58:36', '2026-07-04 04:25:46'),
(2, 3, 'Selesai', '2026-07-04', '2026-07-07', '2026-07-04', 'latihan dasar', 2, 4, NULL, '2026-07-04 01:09:07', '2026-07-04 01:21:51'),
(3, 3, 'Selesai', '2026-07-04', '2026-07-07', '2026-07-04', 'Upacara Kebangsaan', 2, NULL, NULL, '2026-07-04 04:29:10', '2026-07-04 04:31:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `pengembalian_id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Menunggu Verifikasi','Selesai') NOT NULL DEFAULT 'Menunggu Verifikasi',
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_verifikasi` date DEFAULT NULL,
  `is_terlambat` tinyint(1) NOT NULL DEFAULT 0,
  `hari_terlambat` int(11) NOT NULL DEFAULT 0,
  `verifikator_id` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`pengembalian_id`, `peminjaman_id`, `user_id`, `status`, `tanggal_pengajuan`, `tanggal_verifikasi`, `is_terlambat`, `hari_terlambat`, `verifikator_id`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 'Selesai', '2026-07-04', '2026-07-04', 0, 0, 2, '', '2026-07-04 01:16:41', '2026-07-04 01:21:51'),
(2, 1, 3, 'Selesai', '2026-07-04', '2026-07-04', 0, 0, 2, '', '2026-07-04 04:25:02', '2026-07-04 04:25:46'),
(3, 3, 3, 'Selesai', '2026-07-04', '2026-07-04', 0, 0, 2, '', '2026-07-04 04:30:52', '2026-07-04 04:31:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(20) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Administrator dengan hak akses penuh sistem'),
(2, 'Operator', 'Operator logistik pengelola data barang dan verifikasi transaksi'),
(3, 'Anggota', 'Anggota Menwa pemohon pinjaman inventaris'),
(4, 'Dansat', 'Komandan Satuan dengan wewenang persetujuan barang kritis dan dashboard strategis');

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_barang`
--

CREATE TABLE `unit_barang` (
  `unit_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `kode_unit` varchar(30) NOT NULL,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  `status_ketersediaan` enum('Tersedia','Dipinjam','Perbaikan','Hilang') NOT NULL DEFAULT 'Tersedia',
  `tanggal_pengadaan` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `unit_barang`
--

INSERT INTO `unit_barang` (`unit_id`, `barang_id`, `kode_unit`, `kondisi`, `status_ketersediaan`, `tanggal_pengadaan`, `created_at`, `updated_at`) VALUES
(1, 1, 'TND-001', 'Rusak Ringan', 'Perbaikan', '2026-01-10', '2026-07-02 21:37:30', '2026-07-04 04:25:46'),
(2, 1, 'TND-002', 'Baik', 'Tersedia', '2026-01-10', '2026-07-02 21:37:30', '2026-07-04 03:43:44'),
(3, 2, 'SNP-014', 'Baik', 'Tersedia', '2026-02-15', '2026-07-02 21:37:30', '2026-07-04 01:21:51'),
(4, 2, 'SNP-015', 'Baik', 'Tersedia', '2026-02-15', '2026-07-02 21:37:30', '2026-07-04 03:43:31'),
(5, 3, 'BRT-001', 'Rusak Ringan', 'Perbaikan', '2026-03-01', '2026-07-02 21:37:30', '2026-07-04 04:31:30'),
(6, 4, 'MTR-001', 'Baik', 'Tersedia', '2026-07-04', '2026-07-04 01:27:17', '2026-07-04 03:57:16'),
(7, 3, 'BRT-002', 'Rusak Berat', 'Perbaikan', '2026-07-04', '2026-07-04 03:49:23', '2026-07-04 04:31:30'),
(8, 3, 'BRT-003', 'Baik', 'Tersedia', '2026-07-04', '2026-07-04 03:49:23', '2026-07-04 04:31:30'),
(9, 3, 'BRT-004', 'Baik', 'Tersedia', '2026-07-04', '2026-07-04 03:49:23', '2026-07-04 04:31:30'),
(10, 3, 'BRT-005', 'Baik', 'Tersedia', '2026-07-04', '2026-07-04 03:49:23', '2026-07-04 04:31:30'),
(11, 3, 'BRT-006', 'Baik', 'Tersedia', '2026-07-04', '2026-07-04 03:49:23', NULL),
(12, 3, 'BRT-007', 'Baik', 'Tersedia', '2026-07-04', '2026-07-04 03:49:23', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `nim_nip` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `full_name`, `nim_nip`, `username`, `password_hash`, `email`, `phone`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator Utama', '111111111', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@upnyk.ac.id', '081234567890', NULL, 1, '2026-07-02 21:37:30', NULL),
(2, 2, 'Operator Logistik', '222222222', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator@upnyk.ac.id', '081234567891', NULL, 1, '2026-07-02 21:37:30', NULL),
(3, 3, 'Maximus Ary Tonapa', '24051013508', 'Anggota', '$2y$10$Le.QVsQBAVvkJDVIT/9jPO3OLrqsDHZrgxOMpL0e4.3BbJbX.FtOu', 'anggota@upnyk.ac.id', '081380065743', NULL, 1, '2026-07-02 21:37:30', '2026-07-04 04:15:03'),
(4, 4, 'Komandan Satuan', '444444444', 'dansat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dansat@upnyk.ac.id', '081234567893', NULL, 1, '2026-07-02 21:37:30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`barang_id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `bidang_id` (`bidang_id`);

--
-- Indeks untuk tabel `bidang_barang`
--
ALTER TABLE `bidang_barang`
  ADD PRIMARY KEY (`bidang_id`),
  ADD UNIQUE KEY `nama_bidang` (`nama_bidang`);

--
-- Indeks untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`detail_peminjaman_id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indeks untuk tabel `detail_pengembalian`
--
ALTER TABLE `detail_pengembalian`
  ADD PRIMARY KEY (`detail_pengembalian_id`),
  ADD KEY `pengembalian_id` (`pengembalian_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indeks untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`kategori_id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`notifikasi_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`peminjaman_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `verifikator_id` (`verifikator_id`),
  ADD KEY `approver_dansat_id` (`approver_dansat_id`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`pengembalian_id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `verifikator_id` (`verifikator_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indeks untuk tabel `unit_barang`
--
ALTER TABLE `unit_barang`
  ADD PRIMARY KEY (`unit_id`),
  ADD UNIQUE KEY `unique_kode_per_barang` (`barang_id`,`kode_unit`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `nim_nip` (`nim_nip`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `bidang_barang`
--
ALTER TABLE `bidang_barang`
  MODIFY `bidang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `detail_peminjaman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `detail_pengembalian`
--
ALTER TABLE `detail_pengembalian`
  MODIFY `detail_pengembalian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `notifikasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `peminjaman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `pengembalian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `unit_barang`
--
ALTER TABLE `unit_barang`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_barang` (`kategori_id`),
  ADD CONSTRAINT `barang_ibfk_2` FOREIGN KEY (`bidang_id`) REFERENCES `bidang_barang` (`bidang_id`);

--
-- Ketidakleluasaan untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`peminjaman_id`),
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `unit_barang` (`unit_id`);

--
-- Ketidakleluasaan untuk tabel `detail_pengembalian`
--
ALTER TABLE `detail_pengembalian`
  ADD CONSTRAINT `detail_pengembalian_ibfk_1` FOREIGN KEY (`pengembalian_id`) REFERENCES `pengembalian` (`pengembalian_id`),
  ADD CONSTRAINT `detail_pengembalian_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `unit_barang` (`unit_id`);

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`verifikator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`approver_dansat_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`peminjaman_id`),
  ADD CONSTRAINT `pengembalian_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `pengembalian_ibfk_3` FOREIGN KEY (`verifikator_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `unit_barang`
--
ALTER TABLE `unit_barang`
  ADD CONSTRAINT `unit_barang_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`barang_id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
