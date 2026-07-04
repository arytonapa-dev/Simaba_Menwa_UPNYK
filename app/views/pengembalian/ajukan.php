<?php
/**
 * Pengembalian Ajukan View (Anggota - FR-13)
 */
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Ajukan Pengembalian Barang</h1>
    <span class="text-muted small">Pilih transaksi peminjaman aktif Anda, tentukan unit yang dikembalikan, dan laporkan kondisinya</span>
</div>

<div class="row">
    <!-- Step 1: Select Active Loan -->
    <div class="col-md-4 mb-4">
        <div class="glass-card p-4">
            <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Pilih Peminjaman Aktif</h3>
            
            <div class="mb-3">
                <label for="selectLoan" class="form-label text-muted small">Nomor Peminjaman</label>
                <select class="form-select" id="selectLoan" onchange="loadLoanUnits(this.value)">
                    <option value="">-- Pilih Transaksi --</option>
                    <?php foreach ($activeLoans as $l): ?>
                        <option value="<?= $l['peminjaman_id'] ?>" <?= ($peminjamanId == $l['peminjaman_id']) ? 'selected' : '' ?>>
                            #PJ-<?= $l['peminjaman_id'] ?> (Pinjam: <?= date('d/m/Y', strtotime($l['tanggal_pinjam'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($selectedLoan): ?>
                <div class="mt-4 p-3 bg-opacity-30 rounded border small text-muted">
                    <strong>Detail Batas Waktu:</strong><br>
                    Batas Kembali: <span class=""><?= date('d/m/Y', strtotime($selectedLoan['tanggal_rencana_kembali'])) ?></span><br>
                    Status: <span class="text-accent"><?= $selectedLoan['status'] ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Step 2: Select Units & Self Report -->
    <div class="col-md-8 mb-4">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <?php if ($peminjamanId): ?>
            <div class="glass-card p-4">
                <form action="index.php?controller=pengembalian&action=ajukan" method="POST" id="returnForm">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <input type="hidden" name="peminjaman_id" value="<?= $peminjamanId ?>">

                    <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Pilih Unit & Laporkan Kondisi Mandiri</h3>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"></th>
                                    <th>Kode Unit</th>
                                    <th>Nama Barang</th>
                                    <th>Kondisi Pinjam</th>
                                    <th style="width: 220px;">Kondisi Dilaporkan (Self-Report)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($unitsToReturn)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Semua unit barang pada transaksi ini sudah dikembalikan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($unitsToReturn as $unit): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="unit-check" name="selected_units[]" value="<?= $unit['unit_id'] ?>" onchange="toggleUnitRequired(<?= $unit['unit_id'] ?>)">
                                            </td>
                                            <td><strong><?= htmlspecialchars($unit['kode_unit']) ?></strong></td>
                                            <td><?= htmlspecialchars($unit['nama_barang']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($unit['kondisi_saat_pinjam'] ?: 'Baik') ?></span></td>
                                            <td>
                                                <select class="form-select py-1 px-2 self-report-select" 
                                                        name="self_report[<?= $unit['unit_id'] ?>]" 
                                                        id="report-<?= $unit['unit_id'] ?>" disabled>
                                                    <option value="Baik" selected>Baik</option>
                                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                                    <option value="Rusak Berat">Rusak Berat</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($unitsToReturn)): ?>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary py-3 px-4 rounded-3 text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Ajukan Pengembalian
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        <?php else: ?>
            <div class="glass-card p-5 text-center text-muted">
                <i class="fa-solid fa-arrow-left fa-3x mb-3 text-accent"></i>
                <p>Silakan pilih nomor peminjaman aktif terlebih dahulu di sebelah kiri untuk memproses pengembalian.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function loadLoanUnits(pId) {
    if (pId) {
        window.location.href = `index.php?controller=pengembalian&action=ajukan&peminjaman_id=${pId}`;
    } else {
        window.location.href = 'index.php?controller=pengembalian&action=ajukan';
    }
}

function toggleUnitRequired(unitId) {
    const checkbox = document.querySelector(`input[value="${unitId}"]`);
    const select = document.getElementById('report-' + unitId);
    select.disabled = !checkbox.checked;
}

function toggleCheckAll(master) {
    const checks = document.querySelectorAll('.unit-check');
    checks.forEach(check => {
        check.checked = master.checked;
        const select = document.getElementById('report-' + check.value);
        select.disabled = !master.checked;
    });
}

document.getElementById('returnForm')?.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.unit-check:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert("Silakan pilih minimal satu unit barang untuk dikembalikan!");
        return false;
    }
});
</script>
