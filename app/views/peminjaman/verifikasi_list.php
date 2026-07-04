<?php
/**
 * Peminjaman Verifikasi List View
 * Operator/Admin verifikasi list
 */
?>
<div class="breadcrumb-modern">
    <a href="index.php?controller=dashboard&action=index">Home</a>
    <span class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 0.6rem;"></i></span>
    <span class="active">Peminjaman</span>
</div>

<div class="page-header">
    <div>
        <h1>Daftar Peminjaman</h1>
        <span class="page-subtitle">Kelola pengajuan dan approval peminjaman barang</span>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] === 'approved') echo "Pengajuan peminjaman berhasil disetujui.";
                elseif ($_GET['success'] === 'rejected') echo "Pengajuan peminjaman berhasil ditolak.";
                elseif ($_GET['success'] === 'escalated') echo "Pengajuan peminjaman kritis didekteksi dan berhasil dieskalasi ke Dansat.";
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="filter-tabs">
    <div class="filter-tab active">
        Menunggu (<?= count($loans) ?>)
    </div>
    <div class="filter-tab">
        Semua Data
    </div>
</div>

<div class="card-modern">
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Peminjam</th>
                    <th>Keperluan</th>
                    <th>Rencana Pinjam</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data peminjaman saat ini</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($loans as $l): ?>
                        <tr>
                            <td><strong>#PJ-<?= $l['peminjaman_id'] ?></strong></td>
                            <td>
                                <strong class="d-block"><?= htmlspecialchars($l['full_name']) ?></strong>
                                <span class="small text-muted" style="font-size: 0.75rem;">NIM/NBP: <?= htmlspecialchars($l['nim_nip']) ?></span>
                            </td>
                            <td class="text-muted" style="font-size: 0.85rem; max-width: 250px;"><?= htmlspecialchars($l['keperluan']) ?></td>
                            <td>
                                <span class="small d-block"><?= date('d/m/Y', strtotime($l['tanggal_pinjam'])) ?></span>
                                <span class="small text-muted" style="font-size: 0.75rem;">s.d. <?= date('d/m/Y', strtotime($l['tanggal_rencana_kembali'])) ?></span>
                            </td>
                            <td>
                                <?php if ($l['status'] === STATUS_PINJAM_VERIF_WAIT): ?>
                                    <span class="badge bg-warning"><?= $l['status'] ?></span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_DANSAT_WAIT): ?>
                                    <span class="badge bg-danger"><?= $l['status'] ?></span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_APPROVED): ?>
                                    <span class="badge bg-success"><?= $l['status'] ?></span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_ONGOING): ?>
                                    <span class="badge bg-info">Dipinjam</span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_COMPLETED): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= $l['status'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="index.php?controller=peminjaman&action=detail&id=<?= $l['peminjaman_id'] ?>" class="btn btn-sm btn-outline-custom">
                                        <i class="fa-solid fa-circle-info"></i> Detail
                                    </a>
                                    <?php if ($l['status'] === STATUS_PINJAM_VERIF_WAIT): ?>
                                        <a href="index.php?controller=peminjaman&action=verifikasi&id=<?= $l['peminjaman_id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-clipboard-check"></i> Verifikasi
                                        </a>
                                    <?php elseif ($l['status'] === STATUS_PINJAM_APPROVED): ?>
                                        <a href="index.php?controller=peminjaman&action=serahTerima&id=<?= $l['peminjaman_id'] ?>" class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-boxes-packing"></i> Serah Terima
                                        </a>
                                    <?php endif; ?>
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
