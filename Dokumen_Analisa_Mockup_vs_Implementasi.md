# Dokumen Analisa Perancangan (Mockup) vs Implementasi (Perangkat Lunak)

## 1. Pendahuluan
Dokumen ini berisi analisis perbandingan antara perancangan antarmuka (mockup) SIMBA v2.0 yang telah dibuat sebelumnya dengan hasil implementasi akhir perangkat lunak pada proyek `Simaba_Projec`.

## 2. Analisis Antarmuka Pengguna (UI/UX)
| Fitur / Komponen | Perancangan (Mockup) | Implementasi (Perangkat Lunak) | Analisis / Status |
|---|---|---|---|
| **Tema dan Warna** | Menggunakan palet modern (Navy, Blue, Teal, Green) dengan tipografi Inter dan JetBrains Mono. | Diimplementasikan secara modular pada komponen *views* (`app/views`). | **Sesuai**. Implementasi telah menyerap identitas visual utama, meskipun dalam praktiknya dipecah ke dalam layout dinamis. |
| **Tata Letak (Layout)** | Memiliki komponen tunggal (HTML statis) yang mencakup Topbar, Sidebar statis, dan Main Content. | Layout direfaktor dan dipisah secara dinamis (contoh: `app/views/layouts/main.php`). | **Sesuai**. Tata letak dipisahkan ke dalam template dasar (master layout) untuk modularitas dan mempermudah pemeliharaan UI antar-halaman. |
| **Navigasi (Sidebar)** | Menu meliputi: Dashboard, Barang, Peminjaman, Pengembalian, Laporan, Histori, Users. | Menu disesuaikan dengan *Role-Based Access Control* (RBAC) dan mencakup master data tambahan (Kategori, Bidang, Unit, Notifikasi, Audit Trail). | **Diperluas**. Implementasi perangkat lunak jauh lebih dinamis dan menyesuaikan menu berdasarkan peran login pengguna (Admin, Operator, Anggota, Dansat). |

## 3. Analisis Fungsionalitas Modul
| Modul | Perancangan (Mockup) | Implementasi (Perangkat Lunak) | Analisis |
|---|---|---|---|
| **Dashboard** | Menampilkan statistik *hardcoded* (Total, Kondisi, Peminjaman Aktif, Keterlambatan). | `DashboardController.php` memproses *query* agregasi dan memuat data aktual dari basis data. | **Terealisasi**. Mockup visual berhasil ditranslasikan menjadi dashboard dinamis yang fungsional. |
| **Manajemen Barang** | Hanya menampilkan satu tabel barang. | `BarangController.php` dan `UnitController.php` memecah logika antara "Katalog Barang" dan "Unit Fisik Barang". | **Diperbaiki / Lebih Baik**. Implementasi menggunakan pendekatan relasional yang tepat, di mana satu jenis Barang bisa memiliki banyak Unit (dengan *tracking* kode dan kondisi per unit). |
| **Siklus Peminjaman** | Tabel daftar pengajuan dengan tombol aksi sederhana. | `PeminjamanController.php` menerapkan alur logika lengkap: Pengajuan -> Verifikasi Operator -> Elevasi ke Dansat (untuk barang kritis) -> Serah Terima Fisik. | **Terealisasi dengan Alur Bisnis Penuh**. Logika persetujuan bertingkat yang tidak tampak pada Mockup statis telah dikembangkan sempurna pada sistem riil. |
| **Pengembalian** | Desain form input kondisi barang. | `PengembalianController.php` memfasilitasi pelaporan mandiri (*self-report*) dari Anggota, dilanjutkan verifikasi akhir oleh Operator. | **Sesuai**. Implementasi memastikan adanya *check and balance* antara kondisi yang dilaporkan peminjam dan kondisi yang diterima kembali oleh operator. |

## 4. Kesimpulan
Secara keseluruhan, implementasi akhir perangkat lunak pada proyek `Simaba_Projec` **telah berhasil memenuhi dan melampaui** ekspektasi yang dirancang pada tahap Mockup. Mockup statis sukses dikembangkan menjadi aplikasi web berbasis arsitektur MVC (Model-View-Controller) yang kokoh. Sistem final berhasil mewujudkan fitur kompleks seperti *Role-Based Access Control* yang ketat, jejak audit (Audit Trail), persetujuan bertingkat, dan manajemen fisik barang (Unit) yang sangat diperlukan pada operasional inventaris nyata Resimen Mahasiswa.
