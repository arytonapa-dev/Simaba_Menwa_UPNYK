# PANDUAN INSTALASI & DEPLOYMENT
## Sistem Monitoring dan Pengelolaan Inventaris Barang Resimen Mahasiswa (MENWA) Berbasis Web
### Studi Kasus: UPN "Veteran" Yogyakarta

Panduan ini ditujukan untuk Operator/Administrator TI dalam melakukan instalasi, konfigurasi server lokal (XAMPP/Windows), pembuatan basis data, dan konfigurasi penjadwalan tugas otomatis (Cron Job/Windows Task Scheduler).

---

## 1. PERSYARATAN SISTEM (SYSTEM REQUIREMENTS)
Aplikasi ini dikembangkan menggunakan **PHP Native (Arsitektur MVC)** tanpa pustaka luar (Zero Dependency) untuk kecepatan tinggi dan kompatibilitas penuh dengan shared hosting.
* **Sistem Operasi**: Windows 10 / 11 ATAU Windows Server
* **Web Server**: Apache HTTP Server (Sudah termasuk di XAMPP)
* **PHP Engine**: PHP Versi **8.0** s.d. **8.2** (Dengan modul PDO, PDO_MySQL, OpenSSL, GD, Fileinfo aktif)
* **DBMS**: MySQL Versi **8.0** ATAU MariaDB Versi **10.4+**

---

## 2. INSTALASI WEB SERVER (XAMPP)
1. Unduh installer **XAMPP untuk Windows** yang memuat PHP 8.x dari [situs resmi Apache Friends](https://www.apachefriends.org/).
2. Jalankan installer dan selesaikan instalasi (disarankan ke direktori default `C:\xampp`).
3. Buka **XAMPP Control Panel**, lalu aktifkan modul **Apache** dan **MySQL** dengan mengklik tombol **Start**.

---

## 3. KONFIGURASI `php.ini` (PENTING - FR-03, FR-06)
Aplikasi membatasi unggah foto profil dan foto barang maksimal **2 MB** (BR-03 & BR-06). Konfigurasikan batas unggahan di file konfigurasi PHP:
1. Buka XAMPP Control Panel, klik tombol **Config** di baris Apache, pilih `PHP (php.ini)`.
2. Cari dan ubah parameter berikut:
   ```ini
   upload_max_filesize = 2M
   post_max_size = 8M
   memory_limit = 128M
   date.timezone = "Asia/Jakarta"
   ```
3. Simpan perubahan file, lalu lakukan **Restart** pada modul Apache di XAMPP Control Panel.

---

## 4. INSTALASI BASIS DATA & SEEDING (MYSQL 8.0)
Basis data dikonfigurasi menggunakan SQL ANSI murni dengan Foreign Keys dan konstrain ketat.
1. Buka browser dan akses **phpMyAdmin** di alamat `http://localhost/phpmyadmin/`.
2. Buat database baru bernama: `db_menwa`.
3. Buka terminal (CMD/Powershell) dan masuk ke direktori MySQL XAMPP untuk mengimpor schema dan seed data secara berurutan:
   ```powershell
   # 1. Impor Skema Tabel (12 Tabel Relasional)
   C:\xampp\mysql\bin\mysql.exe -u root -p db_menwa < "C:\xampp\htdocs\Simaba_Projec\database\schema.sql"

   # 2. Impor Data Awal (Aktor Default, Hak Akses, Kategori, Barang, dan Unit)
   C:\xampp\mysql\bin\mysql.exe -u root -p db_menwa < "C:\xampp\htdocs\Simaba_Projec\database\seed.sql"
   ```
   *(Tekan Enter saat diminta password jika menggunakan konfigurasi default tanpa kata sandi).*

---

## 5. PENEMPATAN CODEBASE & VIRTUAL HOST
1. Salin seluruh isi folder proyek `Simaba_Projec` ke dalam direktori:
   `C:\xampp\htdocs\Simaba_Projec`
2. Konfigurasikan pemetaan `.htaccess` dan penimpaan URL. Jika menggunakan server Apache default htdocs, pastikan modul `mod_rewrite` aktif di file `httpd.conf` Apache dengan memeriksa baris berikut:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
   Dan pastikan direktori memiliki izin override:
   ```apache
   <Directory "C:/xampp/htdocs">
       AllowOverride All
       Require all granted
   </Directory>
   ```

---

## 6. KONFIGURASI APLIKASI
File konfigurasi utama terletak di `config/config.php`. Pastikan setelan kredensial basis data sesuai dengan lingkungan server Anda:
```php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_menwa');
define('DB_PORT', '3306');
```

---

## 7. SETUP CRON JOB DI WINDOWS (WINDOWS TASK SCHEDULER - BR-08)
Untuk mengotomatiskan pengiriman notifikasi pengingat jatuh tempo H-1 kepada peminjam dan Operator, buat tugas terjadwal harian di Windows:

1. Buka **Command Prompt (Administrator)**.
2. Jalankan perintah `schtasks` berikut untuk membuat task harian otomatis pada jam 07:00 pagi:
   ```cmd
   schtasks /create /tn "Menwa_Due_Reminder_H1" /tr "C:\xampp\php\php.exe -f C:\xampp\htdocs\Simaba_Projec\cron\reminder.php" /sc daily /st 07:00
   ```
3. **Verifikasi**: Tugas terjadwal baru bernama `Menwa_Due_Reminder_H1` akan muncul di aplikasi *Windows Task Scheduler*. Setiap kali dijalankan, log aktivitas cron akan terekam secara otomatis di file `C:\xampp\htdocs\Simaba_Projec\logs\cron.log`.
