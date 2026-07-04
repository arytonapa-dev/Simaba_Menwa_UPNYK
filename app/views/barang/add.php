<?php
/**
 * Barang Add View
 * Kelola Barang Master (Operator)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=barang&action=index" class="text-decoration-none small" style="color: var(--primary);">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Barang Master
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Tambah Barang Master</h1>
    <span class="page-subtitle">Registrasi data master model barang logistik</span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card p-4">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=barang&action=add" method="POST" enctype="multipart/form-data" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                <div class="mb-3">
                    <label for="nama_barang" class="form-label text-muted small">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nama_barang']) ? 'is-invalid' : '' ?>" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>" required>
                    <?php if (isset($errors['nama_barang'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['nama_barang']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="kategori_id" class="form-label text-muted small">Kategori Barang <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['kategori_id']) ? 'is-invalid' : '' ?>" id="kategori_id" name="kategori_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['kategori_id'] ?>" <?= (($_POST['kategori_id'] ?? '') == $cat['kategori_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nama_kategori']) ?> <?= $cat['is_critical'] ? '(Kritis)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['kategori_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['kategori_id']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="bidang_id" class="form-label text-muted small">Bidang Penanggung Jawab <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['bidang_id']) ? 'is-invalid' : '' ?>" id="bidang_id" name="bidang_id" required>
                            <option value="">-- Pilih Bidang --</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?= $sec['bidang_id'] ?>" <?= (($_POST['bidang_id'] ?? '') == $sec['bidang_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sec['nama_bidang']) ?> (PJ: <?= htmlspecialchars($sec['penanggung_jawab']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['bidang_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['bidang_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="satuan" class="form-label text-muted small">Satuan Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['satuan']) ? 'is-invalid' : '' ?>" id="satuan" name="satuan" placeholder="buah, unit, pasang, dll" value="<?= htmlspecialchars($_POST['satuan'] ?? '') ?>" required>
                        <?php if (isset($errors['satuan'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['satuan']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="foto" class="form-label text-muted small">Foto Barang (Opsional, Maks 2MB)</label>
                        <input class="form-control" type="file" id="foto" name="foto" accept=".jpg, .jpeg, .png">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label text-muted small">Deskripsi / Spesifikasi Lengkap</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="index.php?controller=barang&action=index" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
