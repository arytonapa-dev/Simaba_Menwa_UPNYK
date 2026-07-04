<?php
/**
 * Peminjaman Verifikasi Kritis Form View (Dansat - FR-11)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=peminjaman&action=verifikasiKritisList" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Persetujuan Kritis
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Persetujuan Peminjaman Kritis #PJ-<?= $loan['peminjaman_id'] ?></h1>
    <span class="text-muted small">Tinjau permohonan logistik bernilai kritis dan berikan keputusan Komandan Satuan</span>
</div>

<div class="row">
    <!-- Borrower & Loan Details -->
    <div class="col-md-5 mb-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Informasi Peminjam</h3>
                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted ps-0" style="width: 130px;">Nama Lengkap</td>
                        <td><strong><?= htmlspecialchars($loan['borrower_name']) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">NIM / NBP</td>
                        <td><?= htmlspecialchars($loan['borrower_nim']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tanggal Pinjam</td>
                        <td><strong><?= date('d/m/Y', strtotime($loan['tanggal_pinjam'])) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Rencana Kembali</td>
                        <td><strong><?= date('d/m/Y', strtotime($loan['tanggal_rencana_kembali'])) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Keperluan</td>
                        <td class="text-muted"><?= htmlspecialchars($loan['keperluan']) ?></td>
                    </tr>
                </table>
            </div>

            <div class="border-top pt-3 mt-3">
                <span class="text-muted small d-block mb-1">Status Pengajuan</span>
                <span class="badge bg-danger fs-6 d-inline-block"><?= $loan['status'] ?></span>
            </div>
        </div>
    </div>

    <!-- Items Allocated & Actions Form -->
    <div class="col-md-7 mb-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Daftar Unit Barang Kritis</h3>
                <div class="table-responsive mb-4">
                    <table class="table table-modern mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Kode Unit</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $row): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['kode_unit']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td>
                                        <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($row['nama_kategori']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Decision Form -->
            <form action="index.php?controller=peminjaman&action=verifikasiKritis&id=<?= $loan['peminjaman_id'] ?>" method="POST" id="dansatForm">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-15 text-danger small py-2 px-3 mb-3 rounded">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?= htmlspecialchars($errors['general']) ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="form-label text-muted small">Keputusan Dansat (Final)</label>
                    <div class="d-flex gap-3">
                        <div class="form-check bg-success bg-opacity-10 px-4 py-3 border border-success border-opacity-25 rounded-3 w-50" style="cursor:pointer;" onclick="setDecision('approve')">
                            <input class="form-check-input ms-0 me-2" type="radio" name="decision" id="decApprove" value="approve" checked>
                            <label class="form-check-label text-success font-heading" for="decApprove">
                                <strong>Setujui Pengajuan</strong>
                            </label>
                        </div>
                        <div class="form-check bg-danger bg-opacity-10 px-4 py-3 border border-danger border-opacity-25 rounded-3 w-50" style="cursor:pointer;" onclick="setDecision('reject')">
                            <input class="form-check-input ms-0 me-2" type="radio" name="decision" id="decReject" value="reject">
                            <label class="form-check-label text-danger font-heading" for="decReject">
                                <strong>Tolak Pengajuan</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-4 d-none" id="rejectReasonBox">
                    <label for="alasan_tolak" class="form-label text-muted small">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control border-danger" id="alasan_tolak" name="alasan_tolak" rows="3" placeholder="Masukkan alasan penolakan Dansat..."></textarea>
                    <div class="form-text text-muted small" style="font-size: 0.75rem;">Minimal 10 karakter. (TC-11b)</div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3">
                    <a href="index.php?controller=peminjaman&action=verifikasiKritisList" class="btn btn-outline-custom">Kembali</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fa-solid fa-gavel"></i> Ketuk Palu / Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setDecision(decision) {
    const reasonBox = document.getElementById('rejectReasonBox');
    const reasonInput = document.getElementById('alasan_tolak');
    
    if (decision === 'approve') {
        document.getElementById('decApprove').checked = true;
        reasonBox.classList.add('d-none');
        reasonInput.required = false;
    } else {
        document.getElementById('decReject').checked = true;
        reasonBox.classList.remove('d-none');
        reasonInput.required = true;
        reasonInput.focus();
    }
}

document.getElementById('dansatForm').addEventListener('submit', function(e) {
    const isReject = document.getElementById('decReject').checked;
    const reason = document.getElementById('alasan_tolak').value.trim();
    
    if (isReject && reason.length < 10) {
        e.preventDefault();
        alert("Alasan penolakan wajib diisi minimal 10 karakter!");
        return false;
    }
});
</script>
