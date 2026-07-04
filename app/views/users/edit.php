<?php
/**
 * User Edit View
 * Kelola Pengguna (Admin)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=user&action=index" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pengguna
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Ubah Data Pengguna</h1>
    <span class="text-muted small">Edit informasi profil dan hak akses akun</span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card p-4">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=user&action=edit&id=<?= $user['user_id'] ?>" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="full_name" class="form-label text-muted small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? $user['full_name']) ?>" required>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['full_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="nim_nip" class="form-label text-muted small">NIM / NBP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['nim_nip']) ? 'is-invalid' : '' ?>" id="nim_nip" name="nim_nip" value="<?= htmlspecialchars($_POST['nim_nip'] ?? $user['nim_nip']) ?>" required>
                        <?php if (isset($errors['nim_nip'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nim_nip']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label text-muted small">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? $user['username']) ?>" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['username']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="role_id" class="form-label text-muted small">Hak Akses / Peran <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>" id="role_id" name="role_id" required>
                            <option value="">-- Pilih Peran --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['role_id'] ?>" <?= (($_POST['role_id'] ?? $user['role_id']) == $role['role_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['role_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['role_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label text-muted small">Kata Sandi Baru (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password">
                    <div class="form-text text-muted small" style="font-size: 0.75rem;">Kata sandi baru minimal 8 karakter.</div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="email" class="form-label text-muted small">Email (Opsional)</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label text-muted small">Nomor Telepon (Opsional)</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone']) ?>">
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="index.php?controller=user&action=index" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
