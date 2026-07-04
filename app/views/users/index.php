<?php
/**
 * User Index View
 * Kelola Pengguna (Admin)
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 font-heading" style="color: var(--text-primary);">Kelola Pengguna</h1>
        <span class="text-muted small">Registrasi dan pengelolaan akun anggota Resimen Mahasiswa</span>
    </div>
    <a href="index.php?controller=user&action=add" class="btn btn-primary rounded-3">
        <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] == 1) echo "Pengguna baru berhasil ditambahkan.";
                elseif ($_GET['success'] == 2) echo "Detail pengguna berhasil diperbarui.";
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'self_deactivate'): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
        <div>Anda tidak dapat menonaktifkan akun administrator Anda sendiri demi keamanan sistem.</div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>NIM/NBP</th>
                    <th>Username</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= (!empty($u['photo']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/profil/' . $u['photo'])) 
                                          ? 'uploads/profil/' . $u['photo'] 
                                          : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($u['full_name']) ?>" 
                                     alt="Profil" class="rounded-circle me-3 border border-1" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <strong class="d-block"><?= htmlspecialchars($u['full_name']) ?></strong>
                                    <span class="small text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($u['email'] ?: '-') ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['nim_nip']) ?></td>
                        <td><code class="text-accent"><?= htmlspecialchars($u['username']) ?></code></td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($u['role_name']) ?></span>
                        </td>
                        <td>
                            <?php if ($u['is_active'] == 1): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i> Non-Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="index.php?controller=user&action=edit&id=<?= $u['user_id'] ?>" class="btn btn-sm btn-outline-custom" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                
                                <form action="index.php?controller=user&action=toggleActive" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif pengguna ini?');">
                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                    <input type="hidden" name="id" value="<?= $u['user_id'] ?>">
                                    <?php if ($u['is_active'] == 1): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-user-slash"></i> Nonaktifkan
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fa-solid fa-user-check"></i> Aktifkan
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
