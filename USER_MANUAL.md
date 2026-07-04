# PANDUAN PENGGUNA (USER MANUAL)
## Sistem Monitoring dan Pengelolaan Inventaris Barang Resimen Mahasiswa (MENWA) Berbasis Web
### Studi Kasus: UPN "Veteran" Yogyakarta

Sistem ini didesain khusus untuk mendigitalisasi, memantau, dan memverifikasi logistik/inventaris barang di lingkungan MENWA UPN "Veteran" Yogyakarta dengan mematuhi hak akses berbasis peran (RBAC).

---

## 1. PENGENALAN PERAN (USER ROLES)
Sistem memiliki 4 aktor dengan hak akses spesifik:
1. **Administrator (Admin)**: Manajemen pengguna, audit trail, pengelolaan master data (kategori, bidang, barang, unit).
2. **Operator**: Verifikator peminjaman tingkat 1, eksekutor serah terima fisik barang, verifikator pengembalian kondisi fisik barang, dan pelaporan.
3. **Komandan Satuan (Dansat)**: Otoritas tertinggi pengambil keputusan peminjaman barang berkategori kritis (rawan tinggi), dan pelaporan.
4. **Anggota**: Mengajukan peminjaman barang, melihat riwayat pribadi, melakukan konfirmasi mandiri (self-report) pengembalian barang.

---

## 2. PANDUAN UNTUK ANGGOTA (MEMBER WORKFLOW)
### A. Mengajukan Peminjaman (FR-09)
1. Masuk ke aplikasi, klik **Peminjaman** → **Ajukan Peminjaman**.
2. Masukkan jumlah unit yang ingin dipinjam di kolom **Jumlah Pinjam** pada tabel inventaris tersedia.
3. Masukkan **Tanggal Rencana Pinjam**, **Tanggal Rencana Kembali**, dan tulis **Keperluan** peminjaman dengan jelas (minimal 10 karakter).
4. Klik **Ajukan Sekarang**.
   > [!NOTE]
   > Sistem secara otomatis menyaring unit yang berstatus "Tersedia". Jika pengajuan memuat barang berkategori kritis, status pengajuan Anda akan ditandai untuk eskalasi persetujuan Dansat.

### B. Mengajukan Pengembalian (FR-13)
1. Setelah serah terima fisik barang selesai dan masa kegiatan berakhir, akses **Pengembalian** → **Ajukan Pengembalian**.
2. Pilih nomor peminjaman aktif Anda di panel sebelah kiri.
3. Centang unit barang yang ingin dikembalikan (Mendukung pengembalian bertahap / parsial - BR-04).
4. Tentukan kondisi barang saat ini pada kolom **Self-Report** (Baik / Rusak Ringan / Rusak Berat) secara jujur.
5. Klik **Ajukan Pengembalian**.

---

## 3. PANDUAN UNTUK OPERATOR (OPERATOR WORKFLOW)
### A. Melakukan Verifikasi Pengajuan Peminjaman (FR-10)
1. Akses menu **Peminjaman** → **Verifikasi Pengajuan**.
2. Klik tombol **Verifikasi** pada pengajuan berstatus `Menunggu Verifikasi`.
3. Tinjau rincian peminjam dan alokasi unit.
4. Pilih opsi **Setujui Pengajuan** atau **Tolak Pengajuan**. 
   * Jika memilih **Tolak**, Anda wajib memasukkan alasan penolakan secara tertulis (minimal 10 karakter).
5. Klik **Proses Keputusan**.
   > [!IMPORTANT]
   > Jika pengajuan mengandung barang berkategori **Kritis**, tombol persetujuan langsung tidak akan tersedia untuk Operator. Sistem secara otomatis mengelevasi status transaksi menjadi `Menunggu Persetujuan Dansat`.

### B. Memproses Serah Terima Fisik Barang (FR-12 / BR-02)
1. Ketika peminjam datang mengambil barang, akses menu **Peminjaman** → **Serah Terima**.
2. Klik **Serah Terima** pada transaksi yang sudah berstatus `Disetujui`.
3. Periksa fisik unit barang yang diserahkan dan pastikan kesesuaian kode unit.
4. Pilih kondisi unit pada dropdown, lalu klik **Konfirmasi Serah Terima**.
   * Setelah diklik, unit barang tersebut berubah status menjadi `Dipinjam` dan status peminjaman menjadi `Dipinjam (Berjalan)`.

### C. Melakukan Verifikasi Pengembalian Fisik Barang (FR-14)
1. Akses menu **Pengembalian** → **Verifikasi Pengembalian**.
2. Klik **Verifikasi** pada baris pengembalian berstatus `Menunggu Verifikasi`.
3. Pilih **Kondisi Akhir** hasil pemeriksaan fisik (Baik / Rusak Ringan / Rusak Berat).
4. Pilih **Sub-Status Ketersediaan** (Tersedia / Perbaikan / Hilang) sesuai aturan bisnis **BR-01**:
   * *Kondisi Baik* → Hanya boleh Tersedia atau Dipinjam.
   * *Kondisi Rusak Ringan* → Hanya boleh Tersedia atau Perbaikan.
   * *Kondisi Rusak Berat* → Hanya boleh Perbaikan atau Hilang.
5. Klik **Konfirmasi Verifikasi**.
   > [!WARNING]
   > Jika barang dinyatakan **Rusak Berat** atau **Hilang**, sistem otomatis mengirimkan notifikasi peringatan berprioritas tinggi ke Admin dan Dansat untuk ditindaklanjuti.

---

## 4. PANDUAN UNTUK DANSAT (COMMANDER WORKFLOW)
### A. Memberikan Persetujuan Peminjaman Kritis (FR-11)
1. Masuk ke aplikasi, akses menu **Peminjaman** → **Persetujuan Kritis**.
2. Klik **Tinjau & Putuskan** pada pengajuan yang memerlukan keputusan komandan.
3. Tinjau keperluan penggunaan barang rawan/kritis.
4. Pilih **Setujui** atau **Tolak** (Wajib mengisi alasan penolakan minimal 10 karakter).
5. Klik **Ketuk Palu / Simpan**.

### B. Melihat dan Mengekspor Laporan (FR-17, FR-18, FR-19)
1. Akses menu **Laporan**.
2. Pilih tab **Laporan Inventaris** untuk melihat rekap unit aktif ATAU **Laporan Transaksi** untuk melihat rekapitulasi pergerakan barang.
3. Untuk Laporan Transaksi, masukkan **Tanggal Awal** dan **Tanggal Akhir** (Maksimal rentang 1 tahun).
4. Klik **Tampilkan Laporan**.
5. Klik tombol **Ekspor Excel** untuk mengunduh spreadsheet, atau **Cetak / PDF** untuk menampilkan lembar cetak siap print A4 portrait dengan kop surat resmi.

---

## 5. PANDUAN UNTUK ADMINISTRATOR (ADMIN WORKFLOW)
### A. Mengelola Pengguna (FR-01, FR-02, FR-03 / BR-07)
1. Akses menu **Pengguna** di bilah navigasi.
2. Tambahkan pengguna baru dengan mengklik **Tambah Pengguna**, masukkan NIM/NIP, peran (role), dan kata sandi.
3. Untuk menghapus pengguna, klik tombol **Hapus** pada baris bersangkutan. Sistem tidak menghapus data secara permanen melainkan mengubah statusnya menjadi tidak aktif (`is_active = 0`) agar rekam jejak transaksi lama peminjaman tetap terjaga (Soft-Delete - BR-07).

### B. Mengaudit Aktivitas (FR-20)
1. Akses menu **Audit Trail**.
2. Anda dapat melihat log kronologis aktivitas seluruh pengguna. Gunakan filter Modul (seperti `auth`, `peminjaman`, `unit_barang`) untuk mempersempit pencarian.
3. Klik tombol **Lihat JSON** untuk membuka modal pembanding rincian *Data Sebelum* dan *Data Sesudah* dari mutasi data tersebut. Log ini bersifat *read-only* (immutable) dan tidak dapat dimanipulasi oleh siapa pun.
