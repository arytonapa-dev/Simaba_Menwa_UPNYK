<?php
/**
 * Unit Add View
 * Kelola Unit (Operator)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=unit&action=index&barang_id=<?= $barangId ?>" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Unit Fisik
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Registrasi Unit Baru</h1>
    <span class="text-muted small">Registrasi unit fisik individual untuk barang: <strong><?= htmlspecialchars($barang['nama_barang']) ?></strong></span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="glass-card p-4">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=unit&action=add&barang_id=<?= $barangId ?>" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                <!-- Mode Selection -->
                <div class="mb-4">
                    <label class="form-label text-muted small d-block">Metode Registrasi</label>
                    <div class="form-check form-check-inline bg-tertiary px-4 py-2 border rounded-3 me-3" style="cursor:pointer;">
                        <input class="form-check-input" type="radio" name="mode" id="modeSingle" value="single" checked onchange="toggleMode()">
                        <label class="form-check-label" for="modeSingle">Single (Satu Unit)</label>
                    </div>
                    <div class="form-check form-check-inline bg-tertiary px-4 py-2 border rounded-3" style="cursor:pointer;">
                        <input class="form-check-input" type="radio" name="mode" id="modeBulk" value="bulk" onchange="toggleMode()">
                        <label class="form-check-label" for="modeBulk">Massal (Banyak Unit - Maks 500)</label>
                    </div>
                </div>

                <!-- Single Mode Fields -->
                <div id="singleFields" class="mb-3">
                    <label for="kode_unit" class="form-label text-muted small">Kode Unit Unik <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kode_unit" name="kode_unit" placeholder="Contoh: TND-001">
                </div>

                <!-- Bulk Mode Fields -->
                <div id="bulkFields" class="row mb-3 d-none">
                    <div class="col-md-6 mb-3">
                        <label for="prefix_kode" class="form-label text-muted small">Prefix Kode Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prefix_kode" name="prefix_kode" placeholder="Contoh: TND">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jumlah_unit" class="form-label text-muted small">Jumlah Unit (Maks 500) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah_unit" name="jumlah_unit" min="1" max="500" value="1">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="kondisi_awal" class="form-label text-muted small">Kondisi Awal</label>
                        <select class="form-select" id="kondisi_awal" name="kondisi_awal" required>
                            <option value="Baik" selected>Baik (Status: Tersedia)</option>
                            <option value="Rusak Ringan">Rusak Ringan (Status: Tersedia)</option>
                            <option value="Rusak Berat">Rusak Berat (Status: Dalam Perbaikan)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pengadaan" class="form-label text-muted small">Tanggal Pengadaan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_pengadaan" name="tanggal_pengadaan" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="index.php?controller=unit&action=index&barang_id=<?= $barangId ?>" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleMode() {
    const isSingle = document.getElementById('modeSingle').checked;
    const singleFields = document.getElementById('singleFields');
    const bulkFields = document.getElementById('bulkFields');
    
    const kodeUnitInput = document.getElementById('kode_unit');
    const prefixInput = document.getElementById('prefix_kode');
    const jumlahInput = document.getElementById('jumlah_unit');

    if (isSingle) {
        singleFields.classList.remove('d-none');
        bulkFields.classList.add('d-none');
        kodeUnitInput.required = true;
        prefixInput.required = false;
        jumlahInput.required = false;
    } else {
        singleFields.classList.add('d-none');
        bulkFields.classList.remove('d-none');
        kodeUnitInput.required = false;
        prefixInput.required = true;
        jumlahInput.required = true;
    }
}

// Run on load
document.addEventListener("DOMContentLoaded", toggleMode);
</script>
