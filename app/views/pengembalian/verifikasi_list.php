<?php
/**
 * Pengembalian Verifikasi List View (Operator - FR-14)
 */
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Verifikasi Pengembalian</h1>
    <span class="text-muted small">Tinjau, catat kondisi fisik akhir unit barang, dan verifikasi penyelesaian peminjaman</span>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>Verifikasi kondisi akhir pengembalian barang berhasil disimpan dan diproses.</div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Peminjam</th>
                    <th>No Peminjaman</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status Keterlambatan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($returns)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada data pengembalian saat ini</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($returns as $r): ?>
                        <tr>
                            <td><strong>#PG-<?= $r['pengembalian_id'] ?></strong></td>
                            <td>
                                <strong class="d-block"><?= htmlspecialchars($r['full_name']) ?></strong>
                                <span class="small text-muted" style="font-size: 0.75rem;">NIM/NBP: <?= htmlspecialchars($r['nim_nip']) ?></span>
                            </td>
                            <td><strong>#PJ-<?= $r['peminjaman_id'] ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($r['tanggal_pengajuan'])) ?></td>
                            <td>
                                <?php if ($r['is_terlambat'] == 1): ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-clock me-1"></i> Terlambat (<?= $r['hari_terlambat'] ?> Hari)</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Tepat Waktu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($r['status'] === STATUS_KEMBALI_VERIF_WAIT): ?>
                                    <span class="badge bg-warning"><?= $r['status'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($r['status'] === STATUS_KEMBALI_VERIF_WAIT): ?>
                                    <a href="index.php?controller=pengembalian&action=verifikasi&id=<?= $r['pengembalian_id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-clipboard-check"></i> Verifikasi
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Diverifikasi oleh: <?= htmlspecialchars($r['verifikator_name'] ?: '-') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
