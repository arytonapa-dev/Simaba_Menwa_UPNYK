<?php
/**
 * Anggota Dashboard View
 * Modern Enterprise Dashboard
 */
?>
<div class="page-header">
    <div>
        <h1>Dashboard Anggota</h1>
        <p class="page-subtitle">Selamat datang, <strong><?= htmlspecialchars($fullName) ?></strong>. Kelola peminjaman inventaris latihan Anda.</p>
    </div>
    <a href="index.php?controller=peminjaman&action=ajukan" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Ajukan Peminjaman
    </a>
</div>

<!-- Metrics Row -->
<div class="row mb-4 g-4">
    <div class="col-md-6">
        <div class="metric-card metric-blue animated-fade animate-delay-1">
            <div class="metric-icon">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Barang Sedang Anda Pinjam (Unit)</div>
                <div class="metric-val"><?= $units_borrowed ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="metric-card metric-orange animated-fade animate-delay-2">
            <div class="metric-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Menunggu Verifikasi / Persetujuan</div>
                <div class="metric-val"><?= $loans_waiting ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Active Loans Table -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fa-solid fa-list me-2" style="color: var(--primary);"></i>Barang yang Sedang Anda Pinjam</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No Peminjaman</th>
                                <th>Tanggal Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Jumlah Unit</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($active_loans)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4" style="color: var(--text-muted);">
                                        <i class="fa-regular fa-folder-open d-block mb-2" style="font-size: 2rem; color: var(--border-color);"></i>
                                        Anda tidak memiliki peminjaman aktif
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($active_loans as $loan): ?>
                                    <tr>
                                        <td><strong style="color: var(--primary);">#PJ-<?= $loan['peminjaman_id'] ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($loan['tanggal_pinjam'])) ?></td>
                                        <td>
                                            <span style="color: var(--warning-hover); font-weight: 500;">
                                                <?= date('d/m/Y', strtotime($loan['tanggal_rencana_kembali'])) ?>
                                            </span>
                                        </td>
                                        <td><span class="badge" style="background: var(--primary-light); color: var(--primary);"><?= $loan['total_items'] ?> Unit</span></td>
                                        <td>
                                            <span class="badge bg-info"><?= $loan['status'] ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="index.php?controller=pengembalian&action=ajukan&peminjaman_id=<?= $loan['peminjaman_id'] ?>" class="btn btn-sm btn-success">
                                                <i class="fa-solid fa-arrow-rotate-left me-1"></i> Kembalikan
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
