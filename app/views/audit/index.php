<?php
/**
 * Audit Index View (Admin only - FR-20)
 * Redesigned for high-performance administrative UI/UX
 */

// Helper to classify action type, icon, badge type, and detail text
function parseActivity($activityText) {
    $type = 'Lainnya';
    $icon = '⚪';
    $badge = 'secondary';
    $detail = $activityText;

    if (stripos($activityText, 'Login Berhasil') === 0) {
        $type = 'Login';
        $icon = '🟢';
        $badge = 'success';
        $detail = 'Login berhasil ke dalam sistem';
    } elseif (stripos($activityText, 'Login Gagal') === 0) {
        $type = 'Login';
        $icon = '🔴';
        $badge = 'danger';
        $detail = $activityText; // Keep descriptive fail message
    } elseif (stripos($activityText, 'Logout') === 0) {
        $type = 'Logout';
        $icon = '⚪';
        $badge = 'secondary';
        $detail = 'Keluar dari sesi sistem';
    } elseif (stripos($activityText, 'Tambah') === 0) {
        $type = 'Tambah';
        $icon = '🔵';
        $badge = 'primary';
        $detail = $activityText;
    } elseif (stripos($activityText, 'Ubah') === 0 || stripos($activityText, 'Edit') === 0) {
        $type = 'Edit';
        $icon = '🟠';
        $badge = 'warning text-dark';
        $detail = $activityText;
    } elseif (stripos($activityText, 'Hapus') === 0 || stripos($activityText, 'Nonaktifkan') === 0) {
        $type = 'Hapus';
        $icon = '🔴';
        $badge = 'danger';
        $detail = $activityText;
    } elseif (stripos($activityText, 'Verifikasi') !== false || stripos($activityText, 'Persetujuan') !== false || stripos($activityText, 'Serah Terima') !== false) {
        $type = 'Approve';
        $icon = '🟣';
        $badge = 'info';
        $detail = $activityText;
    } elseif (stripos($activityText, 'Ajukan') === 0) {
        $type = 'Pengajuan';
        $icon = '🟡';
        $badge = 'warning text-dark';
        $detail = $activityText;
    }

    return [$type, $icon, $badge, $detail];
}

// Helper to determine module badges and colors
function parseModule($module) {
    $name = strtoupper($module);
    $color = 'bg-secondary';
    
    if ($module === 'auth') {
        $name = 'AUTH';
        $color = 'bg-primary'; // Blue
    } elseif ($module === 'users') {
        $name = 'USER';
        $color = 'bg-danger'; // Red
    } elseif (in_array($module, ['barang', 'unit_barang', 'kategori_barang', 'bidang_barang'])) {
        $name = 'INVENTORY';
        $color = 'bg-success'; // Green
    } elseif ($module === 'peminjaman') {
        $name = 'BORROW';
        $color = 'bg-warning text-dark'; // Yellow
    } elseif ($module === 'pengembalian') {
        $name = 'RETURN';
        $color = 'bg-info text-dark'; // Purple/Cyan
    }

    return [$name, $color];
}

// Helpers to output sorting links
function getSortLink($colName, $currentSort, $currentOrder) {
    $nextOrder = ($currentSort === $colName && $currentOrder === 'asc') ? 'desc' : 'asc';
    
    // Maintain active filters
    $filters = $_GET;
    $filters['sort'] = $colName;
    $filters['order'] = $nextOrder;
    $filters['page'] = 1; // reset page when sorting changes
    
    return 'index.php?' . http_build_query($filters);
}

function getSortIcon($colName, $currentSort, $currentOrder) {
    if ($currentSort !== $colName) {
        return '<i class="fa-solid fa-sort text-muted small opacity-50 ms-1"></i>';
    }
    return $currentOrder === 'asc' 
        ? '<i class="fa-solid fa-sort-up text-accent ms-1"></i>' 
        : '<i class="fa-solid fa-sort-down text-accent ms-1"></i>';
}
?>

<div class="mt-3 mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
  <ol class="breadcrumb bg-transparent mb-0">
    <li class="breadcrumb-item"><a href="index.php?controller=dashboard&action=index">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Audit Trail</li>
  </ol>
</nav>
<h1 class="h2 font-heading mb-1" style="color: var(--text-primary);">Audit Trail</h1>
    <span class="text-muted small d-block mb-3">
        Pantau seluruh aktivitas pengguna yang tercatat secara otomatis. Seluruh data bersifat <strong>immutable</strong> dan tidak dapat diubah maupun dihapus.
    </span>
</div>

<!-- 1. Audit Overview Stats Cards (Item 8) -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="card-modern p-3 d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-database fa-lg text-secondary"></i>
                </div>
                <strong class="h3 font-heading mb-0" style="color: #111827;"><?= number_format($stats['total']) ?></strong>
            </div>
            <span class="d-block" style="color: #6B7280; font-weight: 600; font-size: 0.85rem;">Total Log</span>
        </div>
    </div>
    <div class="col">
        <div class="card-modern p-3 d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-key fa-lg text-primary"></i>
                </div>
                <strong class="h3 font-heading mb-0" style="color: #111827;"><?= number_format($stats['login']) ?></strong>
            </div>
            <span class="d-block" style="color: #6B7280; font-weight: 600; font-size: 0.85rem;">Login Sesi</span>
        </div>
    </div>
    <div class="col">
        <div class="card-modern p-3 d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-pen-to-square fa-lg text-warning"></i>
                </div>
                <strong class="h3 font-heading mb-0" style="color: #111827;"><?= number_format($stats['crud']) ?></strong>
            </div>
            <span class="d-block" style="color: #6B7280; font-weight: 600; font-size: 0.85rem;">Aksi CRUD</span>
        </div>
    </div>
    <div class="col">
        <div class="card-modern p-3 d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-circle-check fa-lg text-info"></i>
                </div>
                <strong class="h3 font-heading mb-0" style="color: #111827;"><?= number_format($stats['approve']) ?></strong>
            </div>
            <span class="d-block" style="color: #6B7280; font-weight: 600; font-size: 0.85rem;">Approve</span>
        </div>
    </div>
    <div class="col">
        <div class="card-modern p-3 d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-calendar-day fa-lg text-success"></i>
                </div>
                <strong class="h3 font-heading mb-0" style="color: #111827;"><?= number_format($stats['today']) ?></strong>
            </div>
            <span class="d-block" style="color: #6B7280; font-weight: 600; font-size: 0.85rem;">Hari Ini</span>
        </div>
    </div>
</div>

<!-- 2. Compact Search Filters (Item 2, 3, 4) -->
<div class="card-modern p-4 mb-4">
    <form action="index.php" method="GET" class="row g-3 align-items-end">
        <input type="hidden" name="controller" value="audit">
        <input type="hidden" name="action" value="index">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">

        <div class="col-md-2">
            <label for="user" class="form-label" style="color: #374151; font-weight: 600;">Pengguna</label>
            <input type="text" class="form-control" id="user" name="user" placeholder="Nama / Username" value="<?= htmlspecialchars($userFilter ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label for="activity" class="form-label" style="color: #374151; font-weight: 600;">Aktivitas</label>
            <input type="text" class="form-control" id="activity" name="activity" placeholder="Login, Edit, Hapus..." value="<?= htmlspecialchars($activityFilter ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label for="module" class="form-label" style="color: #374151; font-weight: 600;">Modul</label>
            <select class="form-select" id="module" name="module">
                <option value="">Semua Modul</option>
                <option value="auth" <?= ($moduleFilter === 'auth') ? 'selected' : '' ?>>AUTENTIKASI</option>
                <option value="users" <?= ($moduleFilter === 'users') ? 'selected' : '' ?>>PENGGUNA</option>
                <option value="kategori_barang" <?= ($moduleFilter === 'kategori_barang') ? 'selected' : '' ?>>KATEGORI</option>
                <option value="bidang_barang" <?= ($moduleFilter === 'bidang_barang') ? 'selected' : '' ?>>BIDANG</option>
                <option value="barang" <?= ($moduleFilter === 'barang') ? 'selected' : '' ?>>BARANG MASTER</option>
                <option value="unit_barang" <?= ($moduleFilter === 'unit_barang') ? 'selected' : '' ?>>UNIT FISIK</option>
                <option value="peminjaman" <?= ($moduleFilter === 'peminjaman') ? 'selected' : '' ?>>PEMINJAMAN</option>
                <option value="pengembalian" <?= ($moduleFilter === 'pengembalian') ? 'selected' : '' ?>>PENGEMBALIAN</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="start_date" class="form-label" style="color: #374151; font-weight: 600;">Dari Tanggal</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label for="end_date" class="form-label" style="color: #374151; font-weight: 600;">Sampai Tanggal</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>">
        </div>
        <div class="col-md-2 d-flex flex-column gap-2">
            <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" style="height: 48px; border-radius: 12px; color: #ffffff !important;">
                <i class="fa-solid fa-magnifying-glass"></i> Cari Log
            </button>
            <button type="button" class="btn btn-outline-custom w-100 d-flex align-items-center justify-content-center gap-2" style="height: 48px; border-radius: 12px;" onclick="window.location.href='index.php?controller=audit&action=index'">
                <i class="fa-solid fa-arrow-clockwise"></i> Reset
            </button>
        </div>
    </form>
</div>

<!-- 3. Logs Output Table -->
<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle table-hover table-striped table-rounded">
            <thead class="sticky-top bg-secondary">
                <tr>
                    <!-- Sorted Headers (Item 10) -->
                    <th style="width: 170px;">
                        <a href="<?= getSortLink('waktu', $sort, $order) ?>" class="text-decoration-none text-muted">
                            Waktu Kejadian <?= getSortIcon('waktu', $sort, $order) ?>
                        </a>
                    </th>
                    <th style="width: 160px;">
                        <a href="<?= getSortLink('user', $sort, $order) ?>" class="text-decoration-none text-muted">
                            Pengguna <?= getSortIcon('user', $sort, $order) ?>
                        </a>
                    </th>
                    <th style="width: 140px;">Aktivitas</th>
                    <th style="width: 130px;">
                        <a href="<?= getSortLink('modul', $sort, $order) ?>" class="text-decoration-none text-muted">
                            Modul <?= getSortIcon('modul', $sort, $order) ?>
                        </a>
                    </th>
                    <th style="width: 130px;">
                        <a href="<?= getSortLink('ip', $sort, $order) ?>" class="text-decoration-none text-muted">
                            IP Address <?= getSortIcon('ip', $sort, $order) ?>
                        </a>
                    </th>
                    <th>Rincian Log</th>
                    <th class="text-end" style="width: 110px;">Mutasi Data</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <!-- Empty State Layout (Item 11) -->
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="fa-solid fa-magnifying-glass fa-3x mb-3" style="color: #9CA3AF;"></i>
                                <h5 class="font-heading mb-2" style="color: #111827; font-weight: 600;">Tidak ditemukan log yang sesuai</h5>
                                <span style="color: #6B7280; font-size: 0.875rem;">Coba ubah filter pencarian atau gunakan kata kunci lain.</span>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <?php 
                            list($type, $icon, $actBadge, $detail) = parseActivity($log['aktivitas']);
                            list($modName, $modColor) = parseModule($log['modul']);
                        ?>
                        <tr>
                            <!-- 1. Timestamp -->
                            <td class="text-muted" style="font-size: 0.85rem;">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <!-- 2. User info -->
                            <td>
                                <strong class="text-main"><?= htmlspecialchars($log['full_name'] ?: 'Sistem') ?></strong>
                                <span class="d-block text-muted small" style="font-size: 0.75rem;">@<?= htmlspecialchars($log['username'] ?: 'system') ?></span>
                            </td>
                            <!-- 3. Classified Activity with Emojis (Item 5, 15) -->
                            <td>
                                <span class="badge bg-<?= $actBadge ?> text-uppercase" style="font-size: 0.65rem; font-weight:700;">
                                    <?= $icon ?> <?= $type ?>
                                </span>
                            </td>
                            <!-- 4. Module Badge (Item 6) -->
                            <td>
                                <span class="badge <?= $modColor ?> text-uppercase" style="font-size: 0.65rem; font-weight:700; letter-spacing: 0.05em;">
                                    <?= $modName ?>
                                </span>
                            </td>
                            <!-- 5. Neutral Colored IP Address (Item 7) -->
                            <td>
                                <code class="text-muted bg-opacity-25 px-2 py-1 rounded small border border-opacity-10">
                                    <?= htmlspecialchars($log['ip_address']) ?>
                                </code>
                            </td>
                            <!-- 6. Detailed Text description (Item 14) -->
                            <td class="text-main small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($detail) ?>
                            </td>
                            <!-- 7. JSON detail viewer -->
                            <td class="text-end">
                                <?php if ($log['data_sebelum'] || $log['data_sesudah']): ?>
                                    <button type="button" class="btn btn-sm btn-outline-custom py-1 px-2" style="font-size: 0.75rem;" onclick="showLogDetail(<?= $log['log_id'] ?>)">
                                        <i class="fa-solid fa-file-code me-1 text-accent"></i> JSON
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 4. Responsive Pagination Controls (Item 9) -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination pagination-sm justify-content-center mb-0 gap-1">
                <!-- First Page -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-3" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                </li>
                <!-- Previous Page -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-3" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                        <i class="fa-solid fa-angle-left"></i> Prev
                    </a>
                </li>

                <!-- Page numbers -->
                <?php 
                $startRange = max(1, $page - 2);
                $endRange = min($totalPages, $page + 2);
                for ($p = $startRange; $p <= $endRange; $p++): 
                ?>
                    <li class="page-item <?= ($page == $p) ? 'active' : '' ?>">
                        <a class="page-link <?= ($page == $p) ? 'bg-accent text-dark border-accent' : ' ' ?> rounded-3 fw-bold" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>">
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Next Page -->
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-3" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                        Next <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
                <!-- Last Page -->
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link rounded-3" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>">
                        <i class="fa-solid fa-angles-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Log JSON Detail Modal -->
<div class="modal fade" id="detailLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card" style="background-color: var(--bg-tertiary);">
            <div class="modal-header">
                <h5 class="modal-title font-heading text-accent" style="color: var(--text-primary);">Detail Mutasi Data Log #<span id="modalLogId"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 text-muted small">IP Address Pengakses: <code class="" id="modalIpAddress"></code></div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <span class="text-muted small d-block mb-1">State Sebelum Perubahan (Data Sebelum)</span>
                        <pre class="p-3 rounded border text-accent small" style="max-height: 350px; overflow: auto;"><code id="jsonBefore"></code></pre>
                    </div>
                    <div class="col-md-6 mb-3">
                        <span class="text-muted small d-block mb-1">State Sesudah Perubahan (Data Sesudah)</span>
                        <pre class="p-3 rounded border text-accent small" style="max-height: 350px; overflow: auto;"><code id="jsonAfter"></code></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary text-dark" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetail(logId) {
    const modal = new bootstrap.Modal(document.getElementById('detailLogModal'));
    
    document.getElementById('modalLogId').innerText = logId;
    document.getElementById('modalIpAddress').innerText = 'Loading...';
    document.getElementById('jsonBefore').innerText = 'Loading...';
    document.getElementById('jsonAfter').innerText = 'Loading...';

    fetch(`index.php?controller=audit&action=detail&id=${logId}`)
    .then(async response => {
        const res = await response.json();
        if (response.ok) {
            document.getElementById('modalIpAddress').innerText = res.data.ip_address;
            document.getElementById('jsonBefore').innerText = res.data.data_sebelum ? JSON.stringify(res.data.data_sebelum, null, 4) : 'NULL';
            document.getElementById('jsonAfter').innerText = res.data.data_sesudah ? JSON.stringify(res.data.data_sesudah, null, 4) : 'NULL';
        } else {
            alert(res.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Gagal memuat rincian log.");
    });

    modal.show();
}
</script>
