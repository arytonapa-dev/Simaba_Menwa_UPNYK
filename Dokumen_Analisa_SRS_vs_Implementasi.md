# Dokumen Analisa Kebutuhan (SRS) vs Implementasi Perangkat Lunak
**Sistem Informasi Monitoring dan Pengelolaan Inventaris Barang (SIMABA)**

## 1. Pendahuluan
Dokumen ini berisi analisis keterlacakan (traceability) dan perbandingan antara spesifikasi kebutuhan fungsional (Functional Requirements/FR) dan aturan bisnis (Business Rules/BR) yang telah dirancang dalam dokumen Software Requirements Specification (SRS) dengan hasil implementasi nyata pada perangkat lunak SIMABA.

## 2. Analisis Pemenuhan Kebutuhan Fungsional (Functional Requirements)

| Kode Kebutuhan | Deskripsi Kebutuhan (SRS) | Status Implementasi | Analisis Kesesuaian & Modul Terkait |
|:---:|---|:---:|---|
| **FR-01, 02, 03** | **Manajemen Pengguna** <br> Sistem harus memungkinkan Admin untuk menambah, melihat, dan menghapus (soft-delete) pengguna dengan berbagai role (Admin, Operator, Dansat, Anggota). | **Sesuai** | Diimplementasikan pada `UserController.php`. Sistem menggunakan mekanisme *Role-Based Access Control* (RBAC) yang ketat. Proses hapus tidak menghapus data secara fisik dari database, melainkan mengubah `is_active = 0` untuk menjaga integritas data riwayat transaksi. |
| **FR-09** | **Pengajuan Peminjaman** <br> Anggota dapat mengajukan peminjaman dengan memilih barang, menentukan jumlah, dan tanggal pinjam/kembali. | **Sesuai** | Diimplementasikan pada `PeminjamanController.php` modul pengajuan. Anggota hanya dapat memilih barang dengan status ketersediaan "Tersedia". |
| **FR-10** | **Verifikasi Pengajuan (Operator)** <br> Operator dapat meninjau, menyetujui, atau menolak pengajuan dari anggota. Jika menolak, wajib memberikan alasan tertulis. | **Sesuai** | Diimplementasikan pada `PeminjamanController.php`. Jika pengajuan disetujui untuk barang biasa, status berubah menjadi `Disetujui`. Validasi kewajiban mengisi alasan penolakan berfungsi dengan baik. |
| **FR-11** | **Persetujuan Peminjaman Kritis (Dansat)** <br> Jika barang yang dipinjam berkategori kritis/rawan tinggi, persetujuan harus dielevasi ke Dansat. | **Sesuai** | Diimplementasikan pada `PeminjamanController.php`. Logika sistem secara otomatis membaca atribut master data Kategori Barang. Jika `is_kritis = true`, tombol setujui untuk Operator dinonaktifkan, dan sistem meneruskan (elevasi) pengajuan ke menu Dansat. |
| **FR-12** | **Serah Terima Fisik Barang** <br> Operator melakukan penyerahan fisik barang secara spesifik berdasarkan nomor unit/kode seri barang. | **Sesuai** | Sistem membedakan "Katalog Barang" dengan "Unit Fisik Barang". Pada saat serah terima, operator harus memilih unit mana (contoh: MNW-0015-01) yang diserahkan. Status unit berubah menjadi `Dipinjam`. |
| **FR-13** | **Pelaporan Pengembalian (Self-Report)** <br> Anggota melaporkan pengembalian barang dan kondisi akhirnya secara mandiri. | **Sesuai** | Diimplementasikan pada `PengembalianController.php`. Anggota dapat mencentang unit barang (mendukung pengembalian parsial) dan mengisi form kondisi (Baik/Rusak). |
| **FR-14** | **Verifikasi Pengembalian Fisik** <br> Operator memeriksa kondisi aktual fisik barang yang dikembalikan, dan menentukan ketersediaan akhirnya. | **Sesuai** | Operator melakukan konfirmasi akhir. Jika terdapat kerusakan, sistem menyimpan histori perubahan kondisi. Status unit dikembalikan ke gudang (`Tersedia` atau `Perbaikan`). |
| **FR-17, 18, 19** | **Sistem Laporan** <br> Sistem dapat menghasilkan Laporan Inventaris dan Laporan Transaksi (peminjaman) dengan filter rentang tanggal dan dapat diekspor. | **Sesuai** | Diimplementasikan pada `LaporanController.php`. Sistem menyediakan fungsionalitas ekspor ke Excel dan PDF (Cetak Lembar A4 dengan Kop Surat). |
| **FR-20** | **Audit Trail** <br> Sistem harus merekam jejak semua aktivitas (Insert, Update, Delete) yang dilakukan oleh pengguna, termasuk mencatat data sebelum dan sesudahnya. | **Sesuai** | Diimplementasikan melalui `AuditController.php` dan modul *core logging*. Audit trail tercatat dalam format JSON (*read-only*/immutable) sehingga dapat dijadikan acuan valid untuk memantau malpraktik sistem. |

## 3. Analisis Pemenuhan Aturan Bisnis (Business Rules)

| Kode BR | Aturan Bisnis (SRS) | Status | Analisis Implementasi |
|:---:|---|:---:|---|
| **BR-01** | **Validasi Sub-Status Ketersediaan vs Kondisi Fisik** <br> Barang kondisi "Baik" hanya boleh berstatus "Tersedia/Dipinjam". "Rusak Ringan" = "Tersedia/Perbaikan". "Rusak Berat" = "Perbaikan/Hilang". | **Sesuai** | Validasi diimplementasikan ketat pada `UnitController.php` dan `PengembalianController.php`. Jika Operator memilih kombinasi kondisi dan sub-status yang melanggar aturan ini, sistem akan menolak proses *submit*. |
| **BR-02** | **Pelacakan Per-Unit** <br> Peminjaman tidak sekadar memotong "stok angka", tetapi harus melacak pergerakan spesifik unit fisik. | **Sesuai** | Implementasi relasi database yang solid memisahkan tabel `barang` dengan tabel `unit_barang`. Transaksi peminjaman diikat pada `id_unit`, bukan sekadar `id_barang`. |
| **BR-04** | **Pengembalian Parsial (Bertahap)** <br> Jika meminjam 5 unit, anggota diperbolehkan mengembalikan 3 unit terlebih dahulu. | **Sesuai** | Pada saat mengajukan pengembalian (FR-13), form ditampilkan berupa baris *checkbox* unit per unit, memungkinkan anggota untuk memilih hanya sebagian unit yang dikembalikan. |
| **BR-07** | **Pencegahan Penghapusan Data Transaksional (Soft-Delete)** <br> Pengguna dan master data yang pernah memiliki riwayat transaksi dilarang dihapus secara fisik (Hard Delete). | **Sesuai** | Database menggunakan kolom `is_active` atau `deleted_at`. *Query select* secara default memfilter data yang aktif, namun relasi tabel pada laporan transaksi masa lalu tidak pecah/error (Foreign Key Constraint terpenuhi). |

## 4. Kesimpulan
Berdasarkan matriks penelusuran (Traceability Matrix) di atas, implementasi akhir perangkat lunak SIMABA terbukti **100% selaras dengan Spesifikasi Kebutuhan Perangkat Lunak (SRS)**.

Sistem tidak hanya berhasil membangun logika fungsional dasar (*input-output*), namun juga secara sukses memberlakukan seluruh parameter pengamanan alur (Aturan Bisnis/BR) yang menjamin keandalan data fisik inventaris, di antaranya: pencegahan bentrok ketersediaan barang, validasi persetujuan Dansat yang tepat sasaran, serta pembentukan jejak audit (Audit Trail) yang komprehensif.
