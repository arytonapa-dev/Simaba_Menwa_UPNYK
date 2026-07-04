# Dokumen User Acceptance Test (UAT)
**Sistem Informasi Monitoring dan Pengelolaan Inventaris Barang (SIMABA)**

**Skenario Uji Utama:** Siklus Penuh Peminjaman Barang Kritis (End-to-End)
**Tujuan:** Menguji seluruh alur logika fungsional mulai dari pengajuan peminjaman oleh anggota, verifikasi awal oleh operator, persetujuan (approval) oleh komandan satuan (Dansat) untuk barang rawan/kritis, proses serah terima fisik, laporan pengembalian (self-report), hingga verifikasi akhir ketersediaan barang.

---

## 1. Informasi Skenario
- **Aktor Terlibat:**
  - Anggota (sebagai Peminjam)
  - Operator (sebagai Verifikator Lapangan)
  - Dansat (sebagai Otoritas Penyetuju Barang Kritis)
- **Kondisi Awal (Pre-condition):**
  - Akun Anggota, Operator, dan Dansat telah terdaftar dan aktif.
  - Terdapat minimal satu unit barang dengan kategori **Kritis/Rawan Tinggi** (misalnya: Tali Carmantel, HT, Tenda Peleton) yang berstatus `Tersedia` dengan kondisi `Baik`.

---

## 2. Langkah-langkah Pengujian (Test Steps)

| No | Fase & Langkah Uji | Aktor | Ekspektasi Hasil (Expected Result) | Status (Pass/Fail) | Catatan |
|:--:|---|---|---|:---:|---|
| | **Fase 1: Pengajuan Peminjaman** | | | | |
| 1 | Login menggunakan akun **Anggota**. | Anggota | Sistem berhasil mengautentikasi dan mengarahkan ke halaman Dashboard Anggota. | [ ] | |
| 2 | Buka menu navigasi **Peminjaman -> Ajukan Peminjaman**. | Anggota | Menampilkan antarmuka form pengajuan, lengkap dengan tabel rincian inventaris barang yang tersedia. | [ ] | |
| 3 | Pilih/Cari barang berkategori **Kritis**, masukkan *Jumlah Pinjam*, *Tanggal Rencana Pinjam*, *Tanggal Rencana Kembali*, dan *Keperluan* (min. 10 karakter). Lalu klik "Ajukan Sekarang". | Anggota | Sistem memvalidasi input. Pengajuan tersimpan dengan status `Menunggu Verifikasi`. Muncul notifikasi pengajuan berhasil. | [ ] | |
| 4 | Logout dari akun Anggota. | Anggota | Kembali ke halaman Login. | [ ] | |
| | **Fase 2: Verifikasi Awal & Persetujuan Kritis** | | | | |
| 5 | Login menggunakan akun **Operator**. | Operator | Berhasil masuk ke halaman Dashboard Operator. | [ ] | |
| 6 | Buka menu navigasi **Peminjaman -> Verifikasi Pengajuan**. Temukan data pengajuan dari Anggota sebelumnya, klik tombol "Verifikasi". | Operator | Karena barang bersifat kritis, sistem mengenali rule bisnis ini dan secara otomatis mengubah status transaksi menjadi `Menunggu Persetujuan Dansat`. Operator tidak memiliki tombol setuju langsung. | [ ] | |
| 7 | Logout dari akun Operator. | Operator | Kembali ke halaman Login. | [ ] | |
| 8 | Login menggunakan akun **Dansat**. | Dansat | Berhasil masuk, terdapat indikator notifikasi pengajuan yang memerlukan keputusan. | [ ] | |
| 9 | Buka menu navigasi **Peminjaman -> Persetujuan Kritis**. Klik tombol "Tinjau & Putuskan" pada baris transaksi terkait, lalu berikan keputusan "Setujui". | Dansat | Transaksi berhasil diotorisasi, status berubah menjadi `Disetujui` (yang berarti barang siap untuk diserahterimakan). | [ ] | |
| 10 | Logout dari akun Dansat. | Dansat | Kembali ke halaman Login. | [ ] | |
| | **Fase 3: Serah Terima Fisik Barang** | | | | |
| 11 | Peminjam datang menemui operator. Login menggunakan akun **Operator**. | Operator | Berhasil masuk. | [ ] | |
| 12 | Buka menu navigasi **Peminjaman -> Serah Terima**. Klik tombol "Serah Terima" pada transaksi yang telah disetujui. | Operator | Menampilkan *pop-up* form untuk memilih nomor seri / kode unik fisik barang yang akan diserahkan kepada peminjam. | [ ] | |
| 13 | Lakukan inspeksi fisik, pilih kondisi barang saat penyerahan, dan klik "Konfirmasi Serah Terima". | Operator | Status unit fisik barang berubah dari `Tersedia` menjadi `Dipinjam`. Status transaksi peminjaman berubah menjadi `Dipinjam (Berjalan)`. | [ ] | |
| 14 | Logout dari akun Operator. | Operator | Kembali ke halaman Login. | [ ] | |
| | **Fase 4: Pengembalian (Self-Report)** | | | | |
| 15 | Kegiatan telah usai. Login menggunakan akun **Anggota**. | Anggota | Berhasil masuk. | [ ] | |
| 16 | Buka menu navigasi **Pengembalian -> Ajukan Pengembalian**. Pilih transaksi peminjaman aktif. | Anggota | Sistem memunculkan detail unit barang yang sedang dipegang oleh Anggota. | [ ] | |
| 17 | Isi kolom konfirmasi kondisi (Self-Report) secara jujur menjadi "Baik" pada setiap unit, lalu klik "Ajukan Pengembalian". | Anggota | Data tersimpan. Status pengembalian menjadi `Menunggu Verifikasi` pihak Operator. | [ ] | |
| 18 | Logout dari akun Anggota. | Anggota | Kembali ke halaman Login. | [ ] | |
| | **Fase 5: Verifikasi Pengembalian Fisik** | | | | |
| 19 | Anggota membawa barang ke gudang. Login menggunakan akun **Operator**. | Operator | Berhasil masuk. | [ ] | |
| 20 | Buka menu navigasi **Pengembalian -> Verifikasi Pengembalian**. Temukan riwayat pelaporan dari anggota. | Operator | Menampilkan rincian daftar unit yang dikembalikan. | [ ] | |
| 21 | Lakukan *cross-check* kesesuaian fisik secara langsung. Masukkan Kondisi Akhir ("Baik") dan Sub-Status ketersediaan ("Tersedia"). Klik "Konfirmasi Verifikasi". | Operator | Siklus transaksi selesai. Status peminjaman menjadi `Selesai`. Unit fisik barang kembali tercatat di sistem dengan status `Tersedia` dan siap untuk dipinjamkan kembali. | [ ] | |

---

## 3. Pengesahan Hasil Pengujian
Skenario (End-to-End) di atas telah diuji pada sistem. Hasilnya menunjukkan kesesuaian antara fungsionalitas aplikasi dengan alur proses bisnis yang disyaratkan.

**Tanggal Pengujian:** _________________________

| Anggota (Peminjam) | Operator (Verifikator) | Komandan Satuan (Approver) | Administrator |
|:---:|:---:|:---:|:---:|
| <br><br> (____________________) | <br><br> (____________________) | <br><br> (____________________) | <br><br> (____________________) |
