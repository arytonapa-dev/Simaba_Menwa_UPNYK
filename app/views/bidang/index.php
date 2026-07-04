<?php
/**
 * Bidang Index View
 * Kelola Bidang (Admin)
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 font-heading" style="color: var(--text-primary);">Bidang Barang</h1>
        <span class="text-muted small">Kelola bidang/unit penanggung jawab inventaris Menwa</span>
    </div>
    <a href="index.php?controller=bidang&action=add" class="btn btn-primary rounded-3">
        <i class="fa-solid fa-plus me-1"></i> Tambah Bidang
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] == 1) echo "Bidang baru berhasil ditambahkan.";
                elseif ($_GET['success'] == 2) echo "Bidang berhasil diperbarui.";
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'has_items'): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
        <div>Bidang tidak dapat dihapus karena masih digunakan sebagai penanggung jawab oleh beberapa data barang.</div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama Bidang</th>
                    <th>Penanggung Jawab</th>
                    <th>Deskripsi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sections)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada bidang terdaftar</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sections as $sec): ?>
                        <tr>
                            <td><strong class=""><?= htmlspecialchars($sec['nama_bidang']) ?></strong></td>
                            <td><strong class="text-accent"><?= htmlspecialchars($sec['penanggung_jawab']) ?></strong></td>
                            <td class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($sec['deskripsi'] ?: '-') ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="index.php?controller=bidang&action=edit&id=<?= $sec['bidang_id'] ?>" class="btn btn-sm btn-outline-custom">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    
                                    <form action="index.php?controller=bidang&action=delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $sec['bidang_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
