<?php
/**
 * Peminjaman Detail View (All roles - Row-level protection enforced in Controller)
 */
?>
<div class="mb-4">
    <?php if ($_SESSION['role_id'] == ROLE_ANGGOTA): ?>
        <a href="index.php?controller=peminjaman&action=riwayat" class="text-accent text-decoration-none small">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat Peminjaman
        </a>
    <?php else: ?>
        <a href="index.php?controller=peminjaman&action=verifikasiList" class="text-accent text-decoration-none small">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Verifikasi
        </a>
    <?php endif; ?>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Detail Peminjaman #PJ-<?= $loan['peminjaman_id'] ?></h1>
    <span class="text-muted small">Metadata lengkap permohonan peminjaman inventaris</span>
</div>

<div class="row">
    <!-- Meta Info Card -->
    <div class="col-md-5 mb-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Detail Informasi Transaksi</h3>
                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted ps-0" style="width: 140px;">Pemohon (Anggota)</td>
                        <td><strong><?= htmlspecialchars($loan['borrower_name']) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">NIM / NBP</td>
                        <td><?= htmlspecialchars($loan['borrower_nim']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tanggal Pinjam</td>
                        <td><?= date('d/m/Y', strtotime($loan['tanggal_pinjam'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Rencana Kembali</td>
                        <td><?= date('d/m/Y', strtotime($loan['tanggal_rencana_kembali'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tanggal Serah Terima</td>
                        <td><?= $loan['tanggal_serah_terima'] ? date('d/m/Y', strtotime($loan['tanggal_serah_terima'])) : '<span class="text-muted small">Belum serah terima</span>' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Keperluan</td>
                        <td class="text-muted"><?= htmlspecialchars($loan['keperluan']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Verifikator Operator</td>
                        <td><?= htmlspecialchars($loan['verifikator_name'] ?: '-') ?></td>
                    </tr>
                    <?php if ($loan['approver_dansat_id']): ?>
                        <tr>
                            <td class="text-muted ps-0">Persetujuan Dansat</td>
                            <td><?= htmlspecialchars($loan['dansat_name'] ?: '-') ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="border-top pt-3 mt-3">
                <span class="text-muted small d-block mb-1">Status Transaksi</span>
                <?php if ($loan['status'] === STATUS_PINJAM_VERIF_WAIT): ?>
                    <span class="badge bg-warning fs-6 d-inline-block"><?= $loan['status'] ?></span>
                <?php elseif ($loan['status'] === STATUS_PINJAM_DANSAT_WAIT): ?>
                    <span class="badge bg-danger fs-6 d-inline-block"><?= $loan['status'] ?></span>
                <?php elseif ($loan['status'] === STATUS_PINJAM_APPROVED): ?>
                    <span class="badge bg-success fs-6 d-inline-block"><?= $loan['status'] ?></span>
                <?php elseif ($loan['status'] === STATUS_PINJAM_ONGOING): ?>
                    <span class="badge bg-info fs-6 d-inline-block">Dipinjam (Berjalan)</span>
                <?php elseif ($loan['status'] === STATUS_PINJAM_COMPLETED): ?>
                    <span class="badge bg-success fs-6 d-inline-block">Selesai</span>
                <?php else: ?>
                    <span class="badge bg-danger fs-6 d-inline-block"><?= $loan['status'] ?></span>
                <?php endif; ?>

                <?php if (!empty($loan['alasan_tolak'])): ?>
                    <div class="mt-3 p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded text-danger small">
                        <strong>Alasan Penolakan:</strong><br>
                        <?= htmlspecialchars($loan['alasan_tolak']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Items Allocated Card -->
    <div class="col-md-7 mb-4">
        <div class="glass-card p-4 h-100">
            <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Daftar Unit Barang Dialokasikan</h3>
            <div class="table-responsive">
                <table class="table table-modern mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Kode Unit</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Kondisi Saat Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $row): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['kode_unit']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?= htmlspecialchars($row['kondisi_saat_pinjam'] ?: 'Baik') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
