<?php
/**
 * Kategori Add View
 * Kelola Kategori (Admin)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=kategori&action=index" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Kategori
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Tambah Kategori Barang</h1>
    <span class="text-muted small">Buat kategori klasifikasi barang baru</span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card p-4">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=kategori&action=add" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                <div class="mb-3">
                    <label for="nama_kategori" class="form-label text-muted small">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nama_kategori']) ? 'is-invalid' : '' ?>" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($_POST['nama_kategori'] ?? '') ?>" required>
                    <?php if (isset($errors['nama_kategori'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['nama_kategori']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label text-muted small">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch bg-tertiary p-3 rounded-3 border" style="border-color: var(--border-color); padding-left: 2.85rem;">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_critical" name="is_critical" value="1" <?= (($_POST['is_critical'] ?? '') == '1') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_critical">
                            <strong>Kategori Barang Kritis</strong>
                            <span class="d-block text-muted small" style="font-size: 0.75rem;">Jika aktif, pengajuan peminjaman barang ini wajib disetujui langsung oleh Komandan Satuan (Dansat) (BR-03).</span>
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="index.php?controller=kategori&action=index" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
