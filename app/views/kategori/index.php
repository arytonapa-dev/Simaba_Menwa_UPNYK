<?php
/**
 * Kategori Index View
 * Kelola Kategori (Admin)
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 font-heading" style="color: var(--text-primary);">Kategori Barang</h1>
        <span class="text-muted small">Kelola kategori klasifikasi inventaris barang Menwa</span>
    </div>
    <a href="index.php?controller=kategori&action=add" class="btn btn-primary rounded-3">
        <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] == 1) echo "Kategori baru berhasil ditambahkan.";
                elseif ($_GET['success'] == 2) echo "Kategori berhasil diperbarui.";
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'has_items'): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
        <div>Kategori tidak dapat dihapus karena masih digunakan oleh beberapa data barang aktif.</div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Status Kekritisan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada kategori terdaftar</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong class=""><?= htmlspecialchars($cat['nama_kategori']) ?></strong></td>
                            <td class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($cat['deskripsi'] ?: '-') ?></td>
                            <td>
                                <?php if ($cat['is_critical'] == 1): ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-shield-halved me-1"></i> Kritis (Butuh Dansat)</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Standar (Operator)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="index.php?controller=kategori&action=edit&id=<?= $cat['kategori_id'] ?>" class="btn btn-sm btn-outline-custom">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    
                                    <form action="index.php?controller=kategori&action=delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $cat['kategori_id'] ?>">
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
