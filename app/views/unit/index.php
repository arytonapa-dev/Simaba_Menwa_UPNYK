<?php
/**
 * Unit Index View
 * Kelola Unit (Operator)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=barang&action=index" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Barang Master
    </a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>
            <h1 class="h2 font-heading" style="color: var(--text-primary);">
                Kelola Unit Fisik: <?= $barang ? htmlspecialchars($barang['nama_barang']) : 'Semua Unit' ?>
            </h1>
            <span class="text-muted small">Kelola unit fisik individual, kode unik, dan status kondisi terkini</span>
        </div>
        <?php if ($barangId): ?>
            <a href="index.php?controller=unit&action=add&barang_id=<?= $barangId ?>" class="btn btn-primary rounded-3">
                <i class="fa-solid fa-plus me-1"></i> Registrasi Unit
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-15 text-success small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
        <div>
            <?php 
                if ($_GET['success'] == 1) echo "Unit baru berhasil diregistrasi.";
                elseif ($_GET['success'] == 2) echo "Kondisi dan status unit berhasil diperbarui.";
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
        <div><?= htmlspecialchars(urldecode($_GET['error'])) ?></div>
    </div>
<?php endif; ?>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle">
            <thead>
                <tr>
                    <th>Kode Unit</th>
                    <th>Nama Barang</th>
                    <th>Kondisi Fisik</th>
                    <th>Status Ketersediaan</th>
                    <th>Tanggal Pengadaan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($units)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada unit terdaftar</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($units as $u): ?>
                        <tr>
                            <td><strong class=""><?= htmlspecialchars($u['kode_unit']) ?></strong></td>
                            <td><?= htmlspecialchars($u['nama_barang'] ?? $barang['nama_barang']) ?></td>
                            <td>
                                <?php if ($u['kondisi'] === 'Baik'): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Baik</span>
                                <?php elseif ($u['kondisi'] === 'Rusak Ringan'): ?>
                                    <span class="badge bg-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i> Rusak Ringan</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Rusak Berat</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['status_ketersediaan'] === 'Tersedia'): ?>
                                    <span class="badge bg-success">Tersedia</span>
                                <?php elseif ($u['status_ketersediaan'] === 'Dipinjam'): ?>
                                    <span class="badge bg-info">Dipinjam</span>
                                <?php elseif ($u['status_ketersediaan'] === 'Perbaikan'): ?>
                                    <span class="badge bg-warning text-dark">Dalam Perbaikan</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hilang / Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['tanggal_pengadaan'])) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <!-- Edit Condition Trigger -->
                                    <button type="button" class="btn btn-sm btn-outline-custom" data-bs-toggle="modal" data-bs-target="#editModal<?= $u['unit_id'] ?>">
                                        <i class="fa-solid fa-wrench"></i> Update
                                    </button>
                                    
                                    <form action="index.php?controller=unit&action=delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit fisik ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $u['unit_id'] ?>">
                                        <input type="hidden" name="barang_id" value="<?= $barangId ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                                <!-- Bootstrap Modal for Updating Condition & Status -->
                                <div class="modal fade" id="editModal<?= $u['unit_id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow-lg border-0 text-start" style="background-color: #fff;">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title font-heading fw-bold text-success">Update Unit: <?= htmlspecialchars($u['kode_unit']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="index.php?controller=unit&action=updateKondisi" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                                    <input type="hidden" name="id" value="<?= $u['unit_id'] ?>">
                                                    <input type="hidden" name="barang_id" value="<?= $barangId ?>">

                                                    <div class="mb-3">
                                                        <label for="kondisi<?= $u['unit_id'] ?>" class="form-label text-muted small">Kondisi Fisik</label>
                                                        <select class="form-select" id="kondisi<?= $u['unit_id'] ?>" name="kondisi" onchange="adjustStatusOptions(<?= $u['unit_id'] ?>)" required>
                                                            <option value="Baik" <?= $u['kondisi'] === 'Baik' ? 'selected' : '' ?>>Baik</option>
                                                            <option value="Rusak Ringan" <?= $u['kondisi'] === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                                                            <option value="Rusak Berat" <?= $u['kondisi'] === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="status<?= $u['unit_id'] ?>" class="form-label text-muted small">Status Ketersediaan</label>
                                                        <select class="form-select" id="status<?= $u['unit_id'] ?>" name="status_ketersediaan" required>
                                                            <!-- Options will be populated or filtered by javascript check based on BR-01 -->
                                                            <option value="Tersedia" <?= $u['status_ketersediaan'] === 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                                            <option value="Dipinjam" <?= $u['status_ketersediaan'] === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                                            <option value="Perbaikan" <?= $u['status_ketersediaan'] === 'Perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
                                                            <option value="Hilang" <?= $u['status_ketersediaan'] === 'Hilang' ? 'selected' : '' ?>>Hilang / Keluar</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="alert alert-warning border-0 bg-warning bg-opacity-15 text-warning small py-2 px-3 rounded d-none" id="brAlert<?= $u['unit_id'] ?>">
                                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Peraturan Bisnis (BR-01) Terdeteksi!
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
/**
 * Interactively adjust and validate availability options based on condition (BR-01 client side validation)
 */
function adjustStatusOptions(unitId) {
    const kondisi = document.getElementById('kondisi' + unitId).value;
    const statusSelect = document.getElementById('status' + unitId);
    const brAlert = document.getElementById('brAlert' + unitId);
    
    const currentVal = statusSelect.value;
    statusSelect.innerHTML = ''; // clear options
    
    brAlert.classList.add('d-none');

    if (kondisi === 'Baik') {
        // Baik -> Tersedia or Dipinjam
        addOption(statusSelect, 'Tersedia', 'Tersedia', currentVal === 'Tersedia');
        addOption(statusSelect, 'Dipinjam', 'Dipinjam', currentVal === 'Dipinjam');
        if (currentVal !== 'Tersedia' && currentVal !== 'Dipinjam') {
            statusSelect.value = 'Tersedia';
        }
    } else if (kondisi === 'Rusak Ringan') {
        // Rusak Ringan -> Tersedia or Perbaikan
        addOption(statusSelect, 'Tersedia', 'Tersedia', currentVal === 'Tersedia');
        addOption(statusSelect, 'Perbaikan', 'Dalam Perbaikan', currentVal === 'Perbaikan');
        if (currentVal !== 'Tersedia' && currentVal !== 'Perbaikan') {
            statusSelect.value = 'Tersedia';
        }
    } else if (kondisi === 'Rusak Berat') {
        // Rusak Berat -> Perbaikan or Hilang (TIDAK BOLEH Tersedia/Dipinjam)
        addOption(statusSelect, 'Perbaikan', 'Dalam Perbaikan', currentVal === 'Perbaikan');
        addOption(statusSelect, 'Hilang', 'Hilang / Keluar', currentVal === 'Hilang');
        statusSelect.value = (currentVal === 'Perbaikan' || currentVal === 'Hilang') ? currentVal : 'Perbaikan';
        brAlert.classList.remove('d-none');
    }
}

function addOption(select, value, text, isSelected) {
    const opt = document.createElement('option');
    opt.value = value;
    opt.innerText = text;
    if (isSelected) opt.selected = true;
    select.appendChild(opt);
}

// Initial script execution on load
document.addEventListener("DOMContentLoaded", () => {
    <?php foreach ($units as $u): ?>
        adjustStatusOptions(<?= $u['unit_id'] ?>);
    <?php endforeach; ?>
});
</script>
