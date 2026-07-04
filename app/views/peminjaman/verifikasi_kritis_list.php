<?php
/**
 * Peminjaman Verifikasi Kritis List View (Dansat - FR-11)
 */
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Persetujuan Peminjaman Kritis</h1>
    <span class="text-muted small">Tinjau dan putuskan persetujuan peminjaman barang inventaris dengan tingkat kerawanan tinggi (kritis)</span>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] === 'approved') echo "Peminjaman kritis berhasil disetujui.";
                elseif ($_GET['success'] === 'rejected') echo "Peminjaman kritis berhasil ditolak.";
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Pemohon</th>
                    <th>Keperluan</th>
                    <th>Rentang Pinjam</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada pengajuan kritis saat ini</td>
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
                                <span class="badge bg-danger"><?= $l['status'] ?></span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?controller=peminjaman&action=verifikasiKritis&id=<?= $l['peminjaman_id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-gavel"></i> Tinjau & Putuskan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
