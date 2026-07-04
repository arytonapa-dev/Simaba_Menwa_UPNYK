<?php
/**
 * Admin Dashboard View
 * Modern Enterprise Dashboard
 */
?>
<div class="page-header">
    <div>
        <h1>Dashboard Admin</h1>
        <p class="page-subtitle">Selamat datang, <strong><?= htmlspecialchars($fullName) ?></strong>. Ringkasan operasional sistem saat ini.</p>
    </div>
</div>

<!-- Metrics Row -->
<div class="row mb-4 g-4">
    <div class="col-xl-3 col-md-6">
        <div class="metric-card metric-indigo animated-fade animate-delay-1">
            <div class="metric-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Total Barang</div>
                <div class="metric-val"><?= $total_barang ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card metric-green animated-fade animate-delay-2">
            <div class="metric-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Unit Tersedia</div>
                <div class="metric-val"><?= $total_unit_tersedia ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card metric-orange animated-fade animate-delay-3">
            <div class="metric-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Pengajuan Tertunda</div>
                <div class="metric-val"><?= $total_pending_verif ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="metric-card metric-cyan animated-fade animate-delay-4">
            <div class="metric-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="metric-content">
                <div class="metric-label">Pengguna Aktif</div>
                <div class="metric-val"><?= $total_pengguna ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Column -->
    <div class="col-lg-5 mb-4">
        <div class="card-modern h-100">
            <div class="card-header-modern">
                <h5><i class="fa-solid fa-chart-pie me-2" style="color: var(--primary);"></i>Distribusi Kondisi Unit</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="height: 280px;">
                <canvas id="conditionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Audit Log Column -->
    <div class="col-lg-7 mb-4">
        <div class="card-modern h-100">
            <div class="card-header-modern">
                <h5><i class="fa-solid fa-list-check me-2" style="color: var(--primary);"></i>5 Aktivitas Terbaru</h5>
                <a href="index.php?controller=audit&action=index" class="btn btn-sm btn-outline-custom">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aktivitas</th>
                                <th>Modul</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_logs)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4" style="color: var(--text-muted);">
                                        <i class="fa-regular fa-folder-open d-block mb-2" style="font-size: 2rem; color: var(--border-color);"></i>
                                        Belum ada log terekam
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_logs as $log): ?>
                                    <tr>
                                        <td style="font-size: 0.82rem; color: var(--text-muted);">
                                            <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($log['username'] ?? 'Sistem') ?></strong></td>
                                        <td style="font-size: 0.85rem;"><?= htmlspecialchars($log['aktivitas']) ?></td>
                                        <td><span class="badge bg-secondary" style="background: var(--secondary-light) !important; color: var(--secondary) !important; font-size: 0.72rem;"><?= htmlspecialchars($log['modul']) ?></span></td>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('conditionChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
            datasets: [{
                data: [
                    <?= (int)$kondisi_baik ?>, 
                    <?= (int)$kondisi_rusak_ringan ?>, 
                    <?= (int)$kondisi_rusak_berat ?>
                ],
                backgroundColor: ['#22C55E', '#F59E0B', '#EF4444'],
                borderWidth: 2,
                borderColor: '#FFFFFF',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#475569',
                        font: { family: 'Poppins', size: 12 },
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10
                    }
                }
            }
        }
    });
});
</script>
