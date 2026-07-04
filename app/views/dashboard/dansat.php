<?php
/**
 * Dansat Dashboard View
 * Modern Enterprise Dashboard
 */
?>
<div class="page-header">
    <div>
        <h1>Dashboard Komandan Satuan</h1>
        <p class="page-subtitle">Selamat datang, <strong><?= htmlspecialchars($fullName) ?></strong>. Ringkasan strategis kondisi inventaris Menwa.</p>
    </div>
</div>

<!-- Metrics Row -->
<div class="row mb-4 g-4">
    <div class="col-md-6">
        <div class="metric-card metric-orange animated-fade animate-delay-1">
            <div class="metric-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Persetujuan Kritis Menunggu Keputusan</div>
                <div class="metric-val"><?= $pending_critical ?></div>
            </div>
            <a href="index.php?controller=peminjaman&action=verifikasiKritisList" class="metric-link">
                Tinjau Sekarang <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="metric-card metric-red animated-fade animate-delay-2">
            <div class="metric-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Persentase Unit Mengalami Kerusakan</div>
                <div class="metric-val"><?= $damage_percentage ?>%</div>
            </div>
        </div>
    </div>
</div>

<!-- Critical Loans Table -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fa-solid fa-shield-halved me-2" style="color: var(--danger);"></i>Peminjaman Kritis Menunggu Persetujuan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No Referensi</th>
                                <th>Anggota Pemohon</th>
                                <th>NIM/NBP</th>
                                <th>Rencana Kembali</th>
                                <th>Keperluan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($critical_loans)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4" style="color: var(--text-muted);">
                                        <i class="fa-regular fa-circle-check d-block mb-2" style="font-size: 2rem; color: var(--success);"></i>
                                        Tidak ada eskalasi peminjaman kritis baru
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($critical_loans as $loan): ?>
                                    <tr>
                                        <td><strong style="color: var(--danger);">#PJ-<?= $loan['peminjaman_id'] ?></strong></td>
                                        <td><?= htmlspecialchars($loan['full_name']) ?></td>
                                        <td><code style="color: var(--text-secondary); background: var(--secondary-light); padding: 2px 8px; border-radius: 4px;"><?= htmlspecialchars($loan['nim_nip']) ?></code></td>
                                        <td>
                                            <span style="color: var(--warning-hover); font-weight: 500;">
                                                <i class="fa-regular fa-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($loan['tanggal_rencana_kembali'])) ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.85rem; color: var(--text-secondary);"><?= htmlspecialchars($loan['keperluan']) ?></td>
                                        <td class="text-end">
                                            <a href="index.php?controller=peminjaman&action=verifikasiKritis&id=<?= $loan['peminjaman_id'] ?>" class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-gavel me-1"></i> Tinjau & Putuskan
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
