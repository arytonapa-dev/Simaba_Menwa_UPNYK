<?php
/**
 * Notifikasi Index View (All roles - FR-22)
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 font-heading" style="color: var(--text-primary);">Kotak Masuk Notifikasi</h1>
        <span class="text-muted small">Kelola dan baca pesan pemberitahuan transaksi inventaris Anda</span>
    </div>
    
    <?php if (!empty($notifications)): ?>
        <form action="index.php?controller=notifikasi&action=markAllRead" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
            <button type="submit" class="btn btn-outline-custom rounded-3">
                <i class="fa-solid fa-envelope-open me-1"></i> Tandai Semua Dibaca
            </button>
        </form>
    <?php endif; ?>
</div>

<div class="glass-card p-4">
    <?php if (empty($notifications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fa-regular fa-bell-slash fa-3x mb-3 text-accent"></i>
            <p class="mb-0">Tidak ada notifikasi masuk</p>
        </div>
    <?php else: ?>
        <div class="list-group list-group-flush bg-transparent">
            <?php foreach ($notifications as $n): ?>
                <div class="list-group-item bg-transparent p-3 mb-2 rounded border d-flex justify-content-between align-items-center" 
                     style="background: rgba(26, 35, 31, 0.3) !important;">
                    
                    <div class="d-flex align-items-start gap-3">
                        <div class="mt-1">
                            <?php if ($n['is_read'] == 0): ?>
                                <span class="badge bg-accent p-2 rounded-circle" style="width: 10px; height: 10px; display: inline-block;" title="Belum Dibaca"></span>
                            <?php else: ?>
                                <span class="badge bg-secondary p-2 rounded-circle" style="width: 10px; height: 10px; display: inline-block;" title="Sudah Dibaca"></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong class="d-block <?= $n['is_read'] == 0 ? 'text-accent' : '' ?>"><?= htmlspecialchars($n['judul']) ?></strong>
                            <p class="mb-1 text-muted small"><?= htmlspecialchars($n['pesan']) ?></p>
                            <span class="small text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <?php if (!empty($n['link_terkait'])): ?>
                            <a href="index.php?controller=notifikasi&action=read&id=<?= $n['notifikasi_id'] ?>" class="btn btn-sm btn-primary text-dark">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Link
                            </a>
                        <?php elseif ($n['is_read'] == 0): ?>
                            <a href="index.php?controller=notifikasi&action=read&id=<?= $n['notifikasi_id'] ?>" class="btn btn-sm btn-outline-custom">
                                <i class="fa-solid fa-envelope-open"></i> Baca
                            </a>
                        <?php endif; ?>

                        <form action="index.php?controller=notifikasi&action=delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $n['notifikasi_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-trash-can"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
