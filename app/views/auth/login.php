<?php
/**
 * Login View Page
 * Modern Enterprise Login
 */
?>
<div class="wp-login-container animated-fade mx-auto mt-5 pt-4" style="max-width: 400px;">
    <div class="text-center mb-4">
        <a href="index.php" class="text-decoration-none">
            <div class="wp-login-logo mb-3 text-wp-blue d-inline-block">
                <i class="fa-solid fa-shield-halved" style="font-size: 3rem;"></i>
            </div>
        </a>
        <h2 class="h4 fw-bold text-dark">Log in ke akun Anda</h2>
        <p class="text-muted small">Inventaris MENWA UPNVY</p>
    </div>

    <div class="wp-login-card bg-white p-4 p-md-5 rounded-3 border shadow-sm">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-start border-4 border-danger rounded-0 py-2 px-3 mb-4 small" style="background-color: #fdf2f2; color: #d63638;" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['timeout'])): ?>
            <div class="alert alert-warning border-start border-4 border-warning rounded-0 py-2 px-3 mb-4 small" style="background-color: #fcf9e8; color: #8a6d3b;" role="alert">
                Sesi berakhir karena tidak ada aktivitas.
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=login" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

            <div class="mb-4">
                <label for="username" class="form-label fw-bold small text-dark">Username atau NIM/NBP</label>
                <input type="text" class="form-control wp-input -subtle" id="username" name="username" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-bold small text-dark">Kata Sandi</label>
                <input type="password" class="form-control wp-input -subtle" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-wp-primary w-100 py-2 fw-bold">
                Log In
            </button>
        </form>
    </div>
    <div class="text-center mt-4 mb-5">
        <a href="index.php" class="text-decoration-none small wp-link text-muted">&larr; Kembali ke Beranda</a>
    </div>
</div>
