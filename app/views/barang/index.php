<?php
/**
 * Barang Index View
 * Kelola Barang Master (Operator)
 */
?>
<div class="breadcrumb-modern">
    <a href="index.php?controller=dashboard&action=index">Home</a>
    <span class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 0.6rem;"></i></span>
    <span class="active">Master Barang</span>
</div>

<div class="page-header">
    <div>
        <h1>Daftar Master Barang</h1>
        <span class="page-subtitle">Kelola data master barang inventaris logistik Menwa</span>
    </div>
    <a href="index.php?controller=barang&action=add" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Tambah Barang
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert border-0 d-flex align-items-center gap-2 py-3 px-4 mb-4" style="background: var(--success-light); color: var(--success-hover); border-radius: var(--radius-sm);" role="alert">
        <i class="fa-solid fa-circle-check fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] == 1) echo "Barang baru berhasil ditambahkan.";
                elseif ($_GET['success'] == 2) echo "Barang berhasil diperbarui.";
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'has_units'): ?>
    <div class="alert border-0 d-flex align-items-center gap-2 py-3 px-4 mb-4" style="background: var(--danger-light); color: var(--danger-hover); border-radius: var(--radius-sm);" role="alert">
        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
        <div>Barang master tidak dapat dihapus karena masih memiliki data unit fisik terdaftar. Hapus unit terlebih dahulu.</div>
    </div>
<?php endif; ?>

<div class="card-modern">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Bidang</th>
                        <th>Satuan</th>
                        <th>Stok Unit (T/P/R/H)</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4" style="color: var(--text-muted);">
                                <i class="fa-regular fa-folder-open d-block mb-2" style="font-size: 2rem; color: var(--border-color);"></i>
                                Belum ada data master barang terdaftar
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?= (!empty($item['foto']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/barang/' . $item['foto'])) 
                                              ? 'uploads/barang/' . $item['foto'] 
                                              : 'https://images.unsplash.com/photo-1595079676339-1534801ad6cf?w=100&auto=format&fit=crop&q=60' ?>" 
                                         alt="Barang" class="rounded" width="48" height="48" style="object-fit: cover; border: 2px solid var(--border-color);">
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary); display:block;"><?= htmlspecialchars($item['nama_barang']) ?></strong>
                                    <span style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($item['deskripsi'] ?: 'Tanpa spesifikasi') ?></span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--secondary-light); color: var(--text-secondary); font-size: 0.72rem;"><?= htmlspecialchars($item['nama_kategori']) ?></span>
                                    <?php if ($item['is_critical'] == 1): ?>
                                        <span class="badge ms-1" style="background: var(--danger-light); color: var(--danger); font-size:0.65rem;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Kritis
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span style="font-size: 0.85rem; color: var(--text-secondary);"><?= htmlspecialchars($item['nama_bidang']) ?></span></td>
                                <td><code style="color: var(--primary); background: var(--primary-light); padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;"><?= htmlspecialchars($item['satuan']) ?></code></td>
                                <td>
                                    <div class="stock-metrics-group">
                                        <span class="stock-badge baik" title="Tersedia"><?= $item['tersedia_unit'] ?> Tersedia</span>
                                        <span class="stock-badge" style="background: var(--info-light); color: var(--info-hover);" title="Dipinjam"><?= $item['dipinjam_unit'] ?> Dipinjam</span>
                                        <span class="stock-badge rr" title="Perbaikan"><?= $item['perbaikan_unit'] ?> Perbaikan</span>
                                        <span class="stock-badge rb" title="Hilang"><?= $item['hilang_unit'] ?> Hilang</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-custom" data-bs-toggle="modal" data-bs-target="#detailModal<?= $item['barang_id'] ?>" title="Lihat Detail Barang">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </button>
                                        <a href="index.php?controller=unit&action=index&barang_id=<?= $item['barang_id'] ?>" class="btn btn-sm btn-outline-custom" title="Kelola Unit Fisik">
                                            <i class="fa-solid fa-boxes-stacked"></i> Unit
                                        </a>
                                        <a href="index.php?controller=barang&action=edit&id=<?= $item['barang_id'] ?>" class="btn btn-sm btn-outline-custom">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <form action="index.php?controller=barang&action=delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data master barang ini?');">
                                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                            <input type="hidden" name="id" value="<?= $item['barang_id'] ?>">
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
</div>

<?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
        <!-- Modal Detail Barang -->
        <div class="modal fade" id="detailModal<?= $item['barang_id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl); overflow: hidden;">
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                        <h4 class="modal-title font-headline fw-bold" style="color: var(--text-primary); font-size: 1.25rem;">
                            Detail Barang &mdash; <?= htmlspecialchars($item['nama_barang']) ?>
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Header Info -->
                        <div class="d-flex flex-column flex-md-row gap-4 mb-4">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 140px; height: 140px; flex-shrink: 0;">
                                <?php if(!empty($item['foto'])): ?>
                                    <img src="uploads/barang/<?= $item['foto'] ?>" alt="<?= htmlspecialchars($item['nama_barang']) ?>" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
                                <?php else: ?>
                                    <i class="fa-solid fa-link" style="font-size: 4rem; color: #D8B4E2;"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="small text-muted fw-bold mb-1 font-monospace">BRG-<?= str_pad($item['barang_id'], 4, '0', STR_PAD_LEFT) ?></div>
                                <h2 class="font-headline fw-bold mb-2" style="color: #1a4a6b; font-size: 1.75rem;"><?= htmlspecialchars($item['nama_barang']) ?></h2>
                                <div class="d-flex gap-2 mb-3 flex-wrap">
                                    <span class="badge" style="background: var(--amber-lt); color: var(--amber-hover); padding: 6px 12px; font-weight: 700; text-transform: uppercase;">
                                        <?= htmlspecialchars($item['nama_kategori']) ?>
                                    </span>
                                    <span class="badge" style="background: var(--blue-lt); color: var(--primary); padding: 6px 12px; font-weight: 700; text-transform: uppercase;">
                                        <?= htmlspecialchars($item['nama_bidang']) ?>
                                    </span>
                                </div>
                                <div style="font-size: 1rem; color: var(--text-secondary);">
                                    Total: <strong class="text-dark"><?= $item['total_unit'] ?> <?= htmlspecialchars($item['satuan']) ?></strong> <span class="mx-1">|</span> 
                                    Tersedia: <strong class="text-dark"><?= $item['tersedia_unit'] ?> <?= htmlspecialchars($item['satuan']) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown Kondisi -->
                        <h5 class="fw-bold mb-3" style="font-size: 1rem; color: var(--text-primary);">Breakdown Kondisi</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="rounded border p-3 text-center h-100" style="background: #eaffea; border-color: #2E7D32 !important;">
                                    <div class="fw-bold" style="font-size: 2rem; color: #2E7D32; line-height: 1;"><?= $item['tersedia_unit'] ?></div>
                                    <div class="small fw-bold mt-2" style="color: #2E7D32; font-size: 0.75rem;">TERSEDIA</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="rounded border p-3 text-center h-100" style="background: #fff3e0; border-color: #E65100 !important;">
                                    <div class="fw-bold" style="font-size: 2rem; color: #E65100; line-height: 1;"><?= $item['perbaikan_unit'] ?></div>
                                    <div class="small fw-bold mt-2" style="color: #E65100; font-size: 0.75rem;">PERBAIKAN</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="rounded border p-3 text-center h-100" style="background: #ffebee; border-color: #C62828 !important;">
                                    <div class="fw-bold" style="font-size: 2rem; color: #C62828; line-height: 1;"><?= $item['hilang_unit'] ?></div>
                                    <div class="small fw-bold mt-2" style="color: #C62828; font-size: 0.75rem;">HILANG</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="rounded border p-3 text-center h-100" style="background: #f8f9fa; border-color: #6c757d !important;">
                                    <div class="fw-bold" style="font-size: 2rem; color: #495057; line-height: 1;"><?= $item['dipinjam_unit'] ?></div>
                                    <div class="small fw-bold mt-2" style="color: #495057; font-size: 0.75rem;">DIPINJAM</div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Tambahan -->
                        <h5 class="fw-bold mb-3" style="font-size: 1rem; color: var(--text-primary);">Informasi Tambahan</h5>
                        <div class="bg-light rounded p-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="d-flex mb-3">
                                        <span class="fw-bold me-2" style="color: var(--text-primary); width: 140px;">Satuan:</span> 
                                        <span style="color: var(--text-secondary);"><?= htmlspecialchars($item['satuan']) ?></span>
                                    </div>
                                    <div class="d-flex">
                                        <span class="fw-bold me-2" style="color: var(--text-primary); width: 140px;">Deskripsi:</span> 
                                        <span style="color: var(--text-secondary);"><?= !empty($item['deskripsi']) ? htmlspecialchars($item['deskripsi']) : '-' ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex mb-3">
                                        <span class="fw-bold me-2" style="color: var(--text-primary); width: 120px;">Ditambahkan:</span> 
                                        <span style="color: var(--text-secondary);"><?= date('d M Y', strtotime($item['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 py-3 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
                        <a href="index.php?controller=unit&action=index&barang_id=<?= $item['barang_id'] ?>" class="btn btn-success fw-bold px-4" style="background: #E65100; border-color: #E65100;">
                            <i class="fa-solid fa-boxes-stacked me-2"></i> Update Kondisi Manual
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
