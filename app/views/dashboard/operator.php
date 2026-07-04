<?php
/**
 * Operator Dashboard View
 * Modern Enterprise Dashboard (SIMBA v3.0)
 */
?>
<div class="page-header">
    <div>
        <h1>Dashboard Operator</h1>
        <p class="page-subtitle">Selamat datang, <strong>Operator Logistik</strong>. Berikut adalah ringkasan operasional logistik hari ini.</p>
    </div>
</div>

<!-- Metrics Row -->
<div class="row mb-4 g-4">
    <!-- Card 1: Verifikasi Peminjaman -->
    <div class="col-xl-3 col-md-6">
        <a href="index.php?controller=peminjaman&action=verifikasiList" class="text-decoration-none">
            <div class="metric-card metric-indigo animated-fade animate-delay-1 h-100 position-relative">
                <div class="metric-icon">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-label">Verifikasi Peminjaman</div>
                    <div class="metric-val"><?= $pending_loans ?? 0 ?></div>
                </div>
                <div class="position-absolute bottom-0 end-0 p-3 text-indigo opacity-50">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Verifikasi Pengembalian -->
    <div class="col-xl-3 col-md-6">
        <a href="index.php?controller=pengembalian&action=verifikasiList" class="text-decoration-none">
            <div class="metric-card metric-green animated-fade animate-delay-2 h-100 position-relative">
                <div class="metric-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-label">Verifikasi Pengembalian</div>
                    <div class="metric-val"><?= $pending_returns ?? 0 ?></div>
                </div>
                <div class="position-absolute bottom-0 end-0 p-3 text-green opacity-50">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 3: Sedang Dipinjam -->
    <div class="col-xl-3 col-md-6">
        <div class="metric-card metric-orange animated-fade animate-delay-3 h-100">
            <div class="metric-icon">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Sedang Dipinjam</div>
                <div class="metric-val"><?= $units_borrowed ?? 0 ?></div>
            </div>
        </div>
    </div>

    <!-- Card 4: Unit Perbaikan -->
    <div class="col-xl-3 col-md-6">
        <div class="metric-card metric-cyan animated-fade animate-delay-4 h-100">
            <div class="metric-icon">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Unit Perbaikan</div>
                <div class="metric-val"><?= $units_repair ?? 0 ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Quick Actions -->
    <div class="col-lg-8 mb-4">
        <div class="card-modern h-100">
            <div class="card-header-modern">
                <h5><i class="fa-solid fa-bolt me-2 text-warning"></i>Pengajuan Menunggu Verifikasi (Quick Actions)</h5>
                <a href="index.php?controller=peminjaman&action=verifikasiList" class="btn btn-sm btn-outline-custom">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No Referensi</th>
                                <th>Peminjam</th>
                                <th>Rencana Kembali</th>
                                <th>Keperluan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($quick_loans)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa-regular fa-folder-open d-block mb-3 fs-1 opacity-50"></i>
                                        Tidak ada pengajuan yang menunggu verifikasi saat ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($quick_loans as $loan): ?>
                                    <tr>
                                        <td><strong>#PJ-<?= $loan['peminjaman_id'] ?></strong></td>
                                        <td>
                                            <strong class="d-block"><?= htmlspecialchars($loan['full_name']) ?></strong>
                                            <span class="small text-muted" style="font-size: 0.75rem;">NIM/NBP: <?= htmlspecialchars($loan['nim_nip'] ?? '-') ?></span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($loan['tanggal_rencana_kembali'])) ?></td>
                                        <td class="text-muted" style="font-size: 0.85rem; max-width: 200px;"><?= htmlspecialchars($loan['keperluan']) ?></td>
                                        <td class="text-center">
                                            <a href="index.php?controller=peminjaman&action=verifikasi&id=<?= $loan['peminjaman_id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-clipboard-check"></i> Verifikasi
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

    <!-- Right Column: Sidebar content -->
    <div class="col-lg-4 mb-4">
        <!-- Informasi Sistem -->
        <div class="glass-card p-4 mb-4" style="border-left: 4px solid var(--info);">
            <div class="d-flex gap-3">
                <i class="fa-solid fa-circle-info text-info mt-1 fs-5"></i>
                <div>
                    <h5 class="h6 font-heading mb-1" style="color: var(--text-primary);">Informasi Sistem</h5>
                    <p class="small text-muted mb-0 lh-base">Pastikan semua data inventaris selalu diperbarui secara berkala dan verifikasi setiap pengembalian fisik.</p>
                </div>
            </div>
        </div>

        <!-- Peringatan -->
        <div class="glass-card p-4 mb-4" style="border-left: 4px solid var(--warning);">
            <div class="d-flex gap-3">
                <i class="fa-solid fa-triangle-exclamation text-warning mt-1 fs-5"></i>
                <div>
                    <h5 class="h6 font-heading mb-1" style="color: var(--text-primary);">Peringatan</h5>
                    <p class="small text-muted mb-3">Beberapa item inventaris mungkin membutuhkan perawatan atau kalibrasi.</p>
                    <a href="index.php?controller=barang" class="btn btn-sm btn-warning text-dark fw-medium">Lihat Inventaris</a>
                </div>
            </div>
        </div>
    </div>
</div>
