<?php
/**
 * Peminjaman Ajukan View (Anggota - FR-09)
 */
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Ajukan Peminjaman Barang</h1>
    <span class="text-muted small">Pilih barang inventaris, masukkan jumlah, tanggal pinjam, dan rencana kembali</span>
</div>

<div class="row">
    <div class="col-12">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-3 px-4 mb-4 rounded-3">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=peminjaman&action=ajukan" method="POST" id="loanForm">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

            <div class="row">
                <!-- Items Selection Grid -->
                <div class="col-md-8 mb-4">
                    <div class="glass-card p-4 h-100">
                        <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Pilih Barang Inventaris</h3>
                        <div class="table-responsive">
                            <table class="table table-modern mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Tersedia</th>
                                        <th style="width: 150px;">Jumlah Pinjam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <?php if ($item['tersedia_unit'] > 0): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= (!empty($item['foto']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/barang/' . $item['foto'])) 
                                                              ? 'uploads/barang/' . $item['foto'] 
                                                              : 'https://images.unsplash.com/photo-1595079676339-1534801ad6cf?w=100&auto=format&fit=crop&q=60' ?>" 
                                                         alt="Barang" class="rounded border" width="45" height="45" style="object-fit: cover;">
                                                </td>
                                                <td>
                                                    <strong class="d-block"><?= htmlspecialchars($item['nama_barang']) ?></strong>
                                                    <span class="small text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($item['nama_bidang']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['nama_kategori']) ?></span>
                                                    <?php if ($item['is_critical'] == 1): ?>
                                                        <span class="badge bg-danger ms-1" style="font-size:0.65rem;"><i class="fa-solid fa-triangle-exclamation"></i> Kritis</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success" id="stock-<?= $item['barang_id'] ?>"><?= $item['tersedia_unit'] ?> Unit</span>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control py-1 px-2 text-center qty-input" 
                                                           name="items[<?= $item['barang_id'] ?>]" 
                                                           id="qty-<?= $item['barang_id'] ?>" 
                                                           min="0" max="<?= $item['tersedia_unit'] ?>" 
                                                           value="0" 
                                                           onchange="validateInputQty(<?= $item['barang_id'] ?>, <?= $item['tersedia_unit'] ?>)">
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Dates & Metadata Column -->
                <div class="col-md-4 mb-4">
                    <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Detail Waktu & Keperluan</h3>
                            
                            <div class="mb-3">
                                <label for="tanggal_pinjam" class="form-label text-muted small">Tanggal Rencana Pinjam <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_rencana_kembali" class="form-label text-muted small">Tanggal Rencana Kembali <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_rencana_kembali" name="tanggal_rencana_kembali" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="keperluan" class="form-label text-muted small">Keperluan Peminjaman <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="4" placeholder="Contoh: Latihan Dasar Menwa Pleton A di Lapangan Upacara..." required></textarea>
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">Minimal 10 karakter.</div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 text-dark d-flex justify-content-center align-items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Ajukan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Real-time stock and input validation
 */
function validateInputQty(barangId, maxStock) {
    const qtyInput = document.getElementById('qty-' + barangId);
    let val = parseInt(qtyInput.value) || 0;
    
    if (val < 0) val = 0;
    if (val > maxStock) {
        alert("Jumlah pinjam melebihi stok yang tersedia!");
        val = maxStock;
    }
    qtyInput.value = val;
}

// Client side date checks before submit
document.getElementById('loanForm').addEventListener('submit', function(e) {
    const start = new Date(document.getElementById('tanggal_pinjam').value);
    const end = new Date(document.getElementById('tanggal_rencana_kembali').value);
    
    if (end < start) {
        e.preventDefault();
        alert("Tanggal rencana kembali tidak boleh sebelum tanggal pinjam!");
        return false;
    }

    // Check if at least 1 item is requested
    const inputs = document.querySelectorAll('.qty-input');
    let totalQty = 0;
    inputs.forEach(input => {
        totalQty += parseInt(input.value) || 0;
    });

    if (totalQty === 0) {
        e.preventDefault();
        alert("Silakan pilih minimal satu barang untuk dipinjam.");
        return false;
    }
});
</script>
