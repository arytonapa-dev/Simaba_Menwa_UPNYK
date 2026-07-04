<?php
/**
 * Peminjaman Serah Terima List View (Operator - FR-12)
 */
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Serah Terima Barang</h1>
    <span class="text-muted small">Daftar peminjaman disetujui yang siap diserahterimakan fisiknya kepada pemohon</span>
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Peminjam</th>
                    <th>Keperluan</th>
                    <th>Rentang Pinjam</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada peminjaman disetujui yang menunggu serah terima</td>
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
                                <span class="badge bg-success"><?= $l['status'] ?></span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?controller=peminjaman&action=serahTerima&id=<?= $l['peminjaman_id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-boxes-packing"></i> Serah Terima
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
