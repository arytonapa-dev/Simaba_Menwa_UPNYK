<?php
/**
 * Peminjaman Riwayat View (Anggota - FR-15)
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 font-heading" style="color: var(--text-primary);">Riwayat Peminjaman Pribadi</h1>
        <span class="text-muted small">Pantau status pengajuan dan histori peminjaman inventaris Anda</span>
    </div>
    <a href="index.php?controller=peminjaman&action=ajukan" class="btn btn-primary rounded-3">
        <i class="fa-solid fa-plus me-1"></i> Ajukan Peminjaman
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>Pengajuan peminjaman barang berhasil disimpan dan menunggu verifikasi.</div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Keperluan</th>
                    <th>Rencana Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Anda belum pernah mengajukan peminjaman</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($loans as $l): ?>
                        <tr>
                            <td><strong>#PJ-<?= $l['peminjaman_id'] ?></strong></td>
                            <td class="" style="font-size: 0.9rem;"><?= htmlspecialchars($l['keperluan']) ?></td>
                            <td><?= date('d/m/Y', strtotime($l['tanggal_pinjam'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($l['tanggal_rencana_kembali'])) ?></td>
                            <td>
                                <?php if ($l['status'] === STATUS_PINJAM_VERIF_WAIT): ?>
                                    <span class="badge bg-warning"><?= $l['status'] ?></span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_DANSAT_WAIT): ?>
                                    <span class="badge bg-danger"><?= $l['status'] ?></span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_APPROVED): ?>
                                    <span class="badge bg-success"><?= $l['status'] ?></span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_ONGOING): ?>
                                    <span class="badge bg-info">Dipinjam (Berjalan)</span>
                                <?php elseif ($l['status'] === STATUS_PINJAM_COMPLETED): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= $l['status'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="index.php?controller=peminjaman&action=detail&id=<?= $l['peminjaman_id'] ?>" class="btn btn-sm btn-outline-custom">
                                    <i class="fa-solid fa-circle-info"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
