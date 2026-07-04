<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Beranda' ?> | Sistem Monitoring Inventaris MENWA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="wp-theme">
    <!-- Navbar -->
    <nav class="wp-navbar navbar navbar-expand-lg bg-white sticky-top">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand fw-bold text-wp-blue d-flex align-items-center gap-2" href="index.php">
                <i class="fa-solid fa-shield-halved"></i> SIMABA MENWA
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link wp-nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link wp-nav-link" href="#tentang">Tentang</a></li>
                </ul>
                <div class="d-flex">
                    <a href="index.php?controller=auth&action=login" class="btn btn-wp-outline me-2">Log In</a>
                    <a href="index.php?controller=auth&action=login" class="btn btn-wp-primary">Mulai Sekarang</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="wp-main-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="wp-footer bg-light py-5 mt-5">
        <div class="container text-center">
            <p class="text-muted small mb-0">&copy; <?= date('Y') ?> Resimen Mahasiswa UPN "Veteran" Yogyakarta. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
