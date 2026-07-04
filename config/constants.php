<?php
/**
 * Global Constants
 * Sistem Monitoring dan Pengelolaan Inventaris Barang MENWA
 */

// Roles Constants
define('ROLE_ADMIN', 1);
define('ROLE_OPERATOR', 2);
define('ROLE_ANGGOTA', 3);
define('ROLE_DANSAT', 4);

// Role Names
define('ROLE_NAMES', [
    ROLE_ADMIN => 'Admin',
    ROLE_OPERATOR => 'Operator',
    ROLE_ANGGOTA => 'Anggota',
    ROLE_DANSAT => 'Dansat'
]);

// Item Conditions
define('COND_BAIK', 'Baik');
define('COND_RUSAK_RINGAN', 'Rusak Ringan');
define('COND_RUSAK_BERAT', 'Rusak Berat');

// Item Availability Statuses
define('STATUS_TERSEDIA', 'Tersedia');
define('STATUS_DIPINJAM', 'Dipinjam');
define('STATUS_PERBAIKAN', 'Perbaikan');
define('STATUS_HILANG', 'Hilang');

// Loan Statuses
define('STATUS_PINJAM_VERIF_WAIT', 'Menunggu Verifikasi');
define('STATUS_PINJAM_DANSAT_WAIT', 'Menunggu Persetujuan Dansat');
define('STATUS_PINJAM_APPROVED', 'Disetujui');
define('STATUS_PINJAM_REJECTED', 'Ditolak');
define('STATUS_PINJAM_REJECTED_DANSAT', 'Ditolak oleh Dansat');
define('STATUS_PINJAM_ONGOING', 'Dipinjam (Berjalan)');
define('STATUS_PINJAM_COMPLETED', 'Selesai');

// Return Statuses
define('STATUS_KEMBALI_VERIF_WAIT', 'Menunggu Verifikasi');
define('STATUS_KEMBALI_COMPLETED', 'Selesai');
