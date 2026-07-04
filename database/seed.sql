-- Database Seeds
-- Sistem Monitoring dan Pengelolaan Inventaris Barang MENWA
-- Studi Kasus: UPN "Veteran" Yogyakarta

USE db_menwa;

-- Seed roles
INSERT INTO roles (role_id, role_name, description) VALUES
(1, 'Admin', 'Administrator dengan hak akses penuh sistem'),
(2, 'Operator', 'Operator logistik pengelola data barang dan verifikasi transaksi'),
(3, 'Anggota', 'Anggota Menwa pemohon pinjaman inventaris'),
(4, 'Dansat', 'Komandan Satuan dengan wewenang persetujuan barang kritis dan dashboard strategis')
ON DUPLICATE KEY UPDATE role_name=VALUES(role_name), description=VALUES(description);

-- Seed default users with bcrypt hash for 'password'
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (user_id, role_id, full_name, nim_nip, username, password_hash, email, phone, is_active, created_at) VALUES
(1, 1, 'Administrator Utama', '111111111', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@upnyk.ac.id', '081234567890', 1, NOW()),
(2, 2, 'Operator Logistik', '222222222', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator@upnyk.ac.id', '081234567891', 1, NOW()),
(3, 3, 'Anggota Aktif', '333333333', 'anggota', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'anggota@upnyk.ac.id', '081234567892', 1, NOW()),
(4, 4, 'Komandan Satuan', '444444444', 'dansat', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dansat@upnyk.ac.id', '081234567893', 1, NOW())
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), password_hash=VALUES(password_hash), is_active=VALUES(is_active);

-- Seed kategori_barang (including critical ones)
INSERT INTO kategori_barang (kategori_id, nama_kategori, deskripsi, is_critical, created_at) VALUES
(1, 'Perlengkapan Lapangan', 'Perlengkapan umum latihan lapangan (tenda, ransel, matras)', 0, NOW()),
(2, 'Atribut Seragam', 'Pakaian Dinas Lapangan (PDL), Pakaian Dinas Upacara (PDU), baret', 0, NOW()),
(3, 'Perlengkapan Khusus', 'Senjata latihan, amunisi hampa, kompas prisma, GPS', 1, NOW())
ON DUPLICATE KEY UPDATE nama_kategori=VALUES(nama_kategori), is_critical=VALUES(is_critical);

-- Seed bidang_barang
INSERT INTO bidang_barang (bidang_id, nama_bidang, penanggung_jawab, deskripsi, created_at) VALUES
(1, 'Bidang Logistik', 'Sersan Mayor Logistik', 'Mengelola penyimpanan dan perawatan seluruh barang inventaris', NOW()),
(2, 'Bidang Operasi', 'Sersan Kepala Operasi', 'Mengatur kebutuhan latihan taktis dan tugas luar', NOW()),
(3, 'Bidang Latihan', 'Sersan Kepala Latihan', 'Mengurus perlengkapan latihan dasar dan lanjutan', NOW())
ON DUPLICATE KEY UPDATE nama_bidang=VALUES(nama_bidang), penanggung_jawab=VALUES(penanggung_jawab);

-- Seed some initial barang
INSERT INTO barang (barang_id, kategori_id, bidang_id, nama_barang, satuan, deskripsi, foto, created_at) VALUES
(1, 1, 1, 'Tenda Pleton', 'unit', 'Tenda pleton kapasitas 30 orang warna loreng', NULL, NOW()),
(2, 3, 2, 'Senapan Latih', 'unit', 'Senapan dummy kayu untuk latihan PBB', NULL, NOW()),
(3, 2, 1, 'Baret Menwa', 'buah', 'Baret ungu resmi Menwa Mahakarta', NULL, NOW())
ON DUPLICATE KEY UPDATE nama_barang=VALUES(nama_barang), satuan=VALUES(satuan);

-- Seed unit_barang
INSERT INTO unit_barang (unit_id, barang_id, kode_unit, kondisi, status_ketersediaan, tanggal_pengadaan, created_at) VALUES
(1, 1, 'TND-001', 'Baik', 'Tersedia', '2026-01-10', NOW()),
(2, 1, 'TND-002', 'Baik', 'Tersedia', '2026-01-10', NOW()),
(3, 2, 'SNP-014', 'Baik', 'Tersedia', '2026-02-15', NOW()),
(4, 2, 'SNP-015', 'Baik', 'Tersedia', '2026-02-15', NOW()),
(5, 3, 'BRT-001', 'Baik', 'Tersedia', '2026-03-01', NOW())
ON DUPLICATE KEY UPDATE kode_unit=VALUES(kode_unit), kondisi=VALUES(kondisi), status_ketersediaan=VALUES(status_ketersediaan);
