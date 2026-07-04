<?php
/**
 * Profile Management View (All users - FR-23)
 */
$fullName = $user['full_name'] ?? '';
$photo = $user['photo'] ?? '';
$profileImg = (!empty($photo) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/profil/' . $photo)) 
              ? 'uploads/profil/' . $photo 
              : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($fullName);
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Profil Saya</h1>
    <span class="text-muted small">Kelola informasi kontak dan ubah kata sandi secara mandiri</span>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div><?= htmlspecialchars($success) ?></div>
    </div>
<?php endif; ?>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center">
        <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
        <div><?= htmlspecialchars($errors['general']) ?></div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Edit Profile Card -->
    <div class="col-md-7 mb-4">
        <div class="glass-card p-4 h-100">
            <h3 class="h5 border-bottom pb-3 mb-4" style="color: var(--text-primary);">Detail Informasi Kontak</h3>
            
            <form action="index.php?controller=user&action=profil" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                
                <div class="d-flex flex-column align-items-center mb-4">
                    <img src="<?= $profileImg ?>" alt="Foto Profil" class="rounded-circle border border-2 border-accent mb-3" width="120" height="120" style="object-fit: cover; border-color: var(--accent) !important;">
                    
                    <div class="col-md-8">
                        <label for="photo" class="form-label text-muted small text-center d-block">Unggah Foto Profil Baru (Maks 2MB, JPG/PNG)</label>
                        <input class="form-control" type="file" id="photo" name="photo" accept=".png, .jpg, .jpeg">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Username</label>
                    <input type="text" class="form-control bg-opacity-30 text-muted" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    <div class="form-text text-muted small" style="font-size: 0.75rem;">Username hanya dapat diubah oleh Administrator.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">NIM / NBP</label>
                    <input type="text" class="form-control bg-opacity-30 text-muted" value="<?= htmlspecialchars($user['nim_nip']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label text-muted small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($fullName) ?>" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="email" class="form-label text-muted small">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label text-muted small">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 text-dark">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                </button>
            </form>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="col-md-5 mb-4">
        <div class="glass-card p-4 h-100">
            <h3 class="h5 border-bottom pb-3 mb-4" style="color: var(--text-primary);">Ganti Kata Sandi</h3>
            
            <div id="passAlertContainer"></div>

            <form id="changePasswordForm">
                <input type="hidden" name="csrf_token" id="passCsrf" value="<?= Session::getCsrfToken() ?>">

                <div class="mb-3">
                    <label for="password_lama" class="form-label text-muted small">Kata Sandi Lama <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_lama" name="password_lama" required>
                </div>

                <div class="mb-3">
                    <label for="password_baru" class="form-label text-muted small">Kata Sandi Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                    <div class="form-text text-muted small" style="font-size: 0.75rem;">Kata sandi minimal 8 karakter.</div>
                </div>

                <div class="mb-4">
                    <label for="konfirmasi_baru" class="form-label text-muted small">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="konfirmasi_baru" name="konfirmasi_baru" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3">
                    <i class="fa-solid fa-key"></i> Perbarui Kata Sandi
                </button>
            </form>
        </div>
    </div>
</div>

<!-- AJAX Change Password Logic -->
<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const alertContainer = document.getElementById('passAlertContainer');
    alertContainer.innerHTML = ''; // reset alerts
    
    const formData = new FormData(this);
    
    fetch('index.php?controller=user&action=ubahPassword', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if (response.ok) {
            alertContainer.innerHTML = `
                <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center">
                    <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                    <div>${data.message}</div>
                </div>
            `;
            document.getElementById('changePasswordForm').reset();
        } else {
            alertContainer.innerHTML = `
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
                    <div>${data.message}</div>
                </div>
            `;
        }
    })
    .catch(err => {
        alertContainer.innerHTML = `
            <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
                <div>Gagal menghubungkan ke server. Silakan coba kembali.</div>
            </div>
        `;
    });
});
</script>
