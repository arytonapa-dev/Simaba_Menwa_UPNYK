<?php
/**
 * Pengembalian Verifikasi Form View (Operator - FR-14)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=pengembalian&action=verifikasiList" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Verifikasi
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Verifikasi Kondisi Pengembalian #PG-<?= $return['pengembalian_id'] ?></h1>
    <span class="text-muted small">Periksa unit fisik barang yang dikembalikan dan konfirmasi kondisi akhirnya</span>
</div>

<div class="row">
    <!-- Borrower & Return Details -->
    <div class="col-md-4 mb-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Informasi Pengembalian</h3>
                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted ps-0" style="width: 130px;">Peminjam</td>
                        <td><strong><?= htmlspecialchars($return['borrower_name']) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">NIM / NBP</td>
                        <td><?= htmlspecialchars($return['borrower_nim']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tanggal Pengajuan</td>
                        <td><strong><?= date('d/m/Y', strtotime($return['tanggal_pengajuan'])) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Batas Rencana Kembali</td>
                        <td><?= date('d/m/Y', strtotime($return['tanggal_rencana_kembali'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Keterlambatan</td>
                        <td>
                            <?php if ($return['is_terlambat'] == 1): ?>
                                <span class="text-danger">Terlambat <?= $return['hari_terlambat'] ?> Hari</span>
                            <?php else: ?>
                                <span class="text-success">Tepat Waktu</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="border-top pt-3 mt-3 text-muted small">
                <i class="fa-solid fa-circle-info text-accent"></i> Sesuai aturan **BR-01 & BR-04**, kondisi akhir unit menentukan status ketersediaan. Rusak Berat tidak diperkenankan berstatus Tersedia/Dipinjam.
            </div>
        </div>
    </div>

    <!-- Units Conditions Forms -->
    <div class="col-md-8 mb-4">
        <div class="glass-card p-4 h-100">
            <form action="index.php?controller=pengembalian&action=verifikasi&id=<?= $return['pengembalian_id'] ?>" method="POST" id="verifyForm">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-2 px-3 mb-3 rounded">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?= htmlspecialchars($errors['general']) ?>
                    </div>
                <?php endif; ?>

                <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Tentukan Kondisi Akhir per Unit</h3>
                
                <div class="table-responsive mb-4">
                    <table class="table table-modern mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Kode Unit</th>
                                <th>Nama Barang</th>
                                <th>Self-Report</th>
                                <th>Kondisi Akhir (Operator)</th>
                                <th>Sub-Status Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $row): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['kode_unit']) ?></strong></td>
                                    <td class="small"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['kondisi_self_report']) ?></span></td>
                                    <td>
                                        <select class="form-select py-1 px-2" 
                                                name="kondisi_akhir[<?= $row['unit_id'] ?>]" 
                                                id="kondisi-<?= $row['unit_id'] ?>" 
                                                onchange="adjustStatusOptions(<?= $row['unit_id'] ?>)" required>
                                            <option value="Baik" <?= $row['kondisi_self_report'] === 'Baik' ? 'selected' : '' ?>>Baik</option>
                                            <option value="Rusak Ringan" <?= $row['kondisi_self_report'] === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                                            <option value="Rusak Berat" <?= $row['kondisi_self_report'] === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select py-1 px-2" 
                                                name="status_ketersediaan[<?= $row['unit_id'] ?>]" 
                                                id="status-<?= $row['unit_id'] ?>" required>
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Perbaikan">Dalam Perbaikan</option>
                                            <option value="Hilang">Hilang</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <label for="catatan" class="form-label text-muted small">Catatan Pemeriksaan Fisik</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Masukkan catatan tambahan pemeriksaan fisik barang..."></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="index.php?controller=pengembalian&action=verifikasiList" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-circle-check"></i> Konfirmasi Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * BR-01 enforcement on return verification page
 */
function adjustStatusOptions(unitId) {
    const kondisi = document.getElementById('kondisi-' + unitId).value;
    const statusSelect = document.getElementById('status-' + unitId);
    
    const currentVal = statusSelect.value;
    statusSelect.innerHTML = ''; // clear options

    if (kondisi === 'Baik') {
        addOption(statusSelect, 'Tersedia', 'Tersedia', true);
    } else if (kondisi === 'Rusak Ringan') {
        addOption(statusSelect, 'Tersedia', 'Tersedia', currentVal === 'Tersedia');
        addOption(statusSelect, 'Perbaikan', 'Dalam Perbaikan', currentVal === 'Perbaikan' || currentVal === 'Tersedia');
    } else if (kondisi === 'Rusak Berat') {
        addOption(statusSelect, 'Perbaikan', 'Dalam Perbaikan', true);
        addOption(statusSelect, 'Hilang', 'Hilang / Dikeluarkan', currentVal === 'Hilang');
    }
}

function addOption(select, value, text, isSelected) {
    const opt = document.createElement('option');
    opt.value = value;
    opt.innerText = text;
    if (isSelected) opt.selected = true;
    select.appendChild(opt);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    <?php foreach ($details as $row): ?>
        adjustStatusOptions(<?= $row['unit_id'] ?>);
    <?php endforeach; ?>
});
</script>
