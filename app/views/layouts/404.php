<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>
    <div class="error-page">
        <div>
            <div class="error-code">404</div>
            <h1 class="error-title"><i class="fa-solid fa-compass me-2" style="color: var(--warning);"></i>Halaman Tidak Ditemukan</h1>
            <p class="error-message">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
            <a href="index.php?controller=dashboard&action=index" class="btn btn-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
