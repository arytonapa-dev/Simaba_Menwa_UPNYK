<?php
/**
 * Bidang Edit View
 * Kelola Bidang (Admin)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=bidang&action=index" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Bidang
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Ubah Bidang Barang</h1>
    <span class="text-muted small">Ubah informasi penanggung jawab dan deskripsi bidang</span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card p-4">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=bidang&action=edit&id=<?= $section['bidang_id'] ?>" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_bidang" class="form-label text-muted small">Nama Bidang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['nama_bidang']) ? 'is-invalid' : '' ?>" id="nama_bidang" name="nama_bidang" value="<?= htmlspecialchars($_POST['nama_bidang'] ?? $section['nama_bidang']) ?>" required>
                        <?php if (isset($errors['nama_bidang'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nama_bidang']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="penanggung_jawab" class="form-label text-muted small">Nama Penanggung Jawab <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['penanggung_jawab']) ? 'is-invalid' : '' ?>" id="penanggung_jawab" name="penanggung_jawab" value="<?= htmlspecialchars($_POST['penanggung_jawab'] ?? $section['penanggung_jawab']) ?>" required>
                        <?php if (isset($errors['penanggung_jawab'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['penanggung_jawab']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label text-muted small">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? $section['deskripsi']) ?></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="index.php?controller=bidang&action=index" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
