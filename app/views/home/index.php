<?php
/**
 * Home View (WordPress-style Landing Page)
 */
?>
<!-- Hero Section -->
<section class="wp-hero py-5 text-center text-lg-start">
    <div class="container px-4 px-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="wp-hero-title mb-4">Kelola Inventaris dengan Lebih Cerdas.</h1>
                <p class="wp-hero-subtitle mb-4 text-secondary">
                    SIMABA adalah sistem manajemen logistik cerdas untuk Resimen Mahasiswa UPN "Veteran" Yogyakarta. Pantau, pinjam, dan laporkan barang dengan satu klik.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                    <a href="index.php?controller=auth&action=login" class="btn btn-wp-primary btn-lg">Mulai Sekarang</a>
                    <a href="#fitur" class="btn btn-wp-outline btn-lg">Pelajari Lebih Lanjut</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://wordpress.com/wp-content/themes/a8c/wordpress.com-2022/assets/images/header-graphic-desktop.png" class="img-fluid rounded-4 wp-hero-image" alt="Dashboard Preview">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="fitur" class="wp-features py-5 bg-light">
    <div class="container px-4 px-lg-5 py-5">
        <div class="text-center mb-5">
            <h2 class="wp-section-title">Semua yang Anda butuhkan untuk kelancaran tugas.</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="wp-feature-card h-100 p-4 bg-white rounded-4 border-0 shadow-sm text-center">
                    <div class="wp-feature-icon mb-3 text-wp-blue fs-1">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <h3 class="h5 fw-bold">Manajemen Inventaris</h3>
                    <p class="text-muted small">Lacak setiap unit barang secara akurat mulai dari kondisi, ketersediaan, hingga riwayat penggunaannya.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wp-feature-card h-100 p-4 bg-white rounded-4 border-0 shadow-sm text-center">
                    <div class="wp-feature-icon mb-3 text-wp-blue fs-1">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3 class="h5 fw-bold">Peminjaman Digital</h3>
                    <p class="text-muted small">Ajukan peminjaman secara online dengan sistem persetujuan otomatis berdasarkan hak akses pengguna.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wp-feature-card h-100 p-4 bg-white rounded-4 border-0 shadow-sm text-center">
                    <div class="wp-feature-icon mb-3 text-wp-blue fs-1">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h3 class="h5 fw-bold">Pelaporan Otomatis</h3>
                    <p class="text-muted small">Hasilkan laporan transaksi bulanan dan status barang terkini dengan format PDF dan Excel siap cetak.</p>
                </div>
            </div>
        </div>
    </div>
</section>
