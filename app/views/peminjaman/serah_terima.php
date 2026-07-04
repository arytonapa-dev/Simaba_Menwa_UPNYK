<?php
/**
 * Peminjaman Serah Terima Form View (Operator - FR-12)
 */
?>
<div class="mb-4">
    <a href="index.php?controller=peminjaman&action=serahTerimaList" class="text-accent text-decoration-none small">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Serah Terima
    </a>
    <h1 class="h2 font-heading mt-2" style="color: var(--text-primary);">Konfirmasi Serah Terima #PJ-<?= $loan['peminjaman_id'] ?></h1>
    <span class="text-muted small">Catat kondisi barang saat diserahkan secara fisik kepada peminjam</span>
</div>

<div class="row">
    <!-- Borrower & Loan Details -->
    <div class="col-md-5 mb-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Detail Permohonan</h3>
                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted ps-0" style="width: 130px;">Peminjam</td>
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
            
            <div class="border-top pt-3 mt-3 text-muted small">
                <i class="fa-solid fa-circle-info text-accent"></i> Menekan tombol konfirmasi akan mengubah status peminjaman menjadi <strong>Dipinjam (Berjalan)</strong> (BR-02).
            </div>
        </div>
    </div>

    <!-- Handover Units Form -->
    <div class="col-md-7 mb-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <form action="index.php?controller=peminjaman&action=serahTerima&id=<?= $loan['peminjaman_id'] ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                
                <div>
                    <h3 class="h5 mb-4 font-heading" style="color: var(--text-primary);">Tandai Kondisi Unit Saat Serah Terima</h3>
                    <div class="table-responsive mb-4">
                        <table class="table table-modern mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Kode Unit</th>
                                    <th>Nama Barang</th>
                                    <th style="width: 200px;">Kondisi Serah Terima</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $row): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['kode_unit']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td>
                                            <!-- Select condition during hand-over (BR-01 / FR-12) -->
                                            <select class="form-select py-1 px-2" 
                                                    name="kondisi_serah[<?= $row['unit_id'] ?>]" required>
                                                <option value="Baik" <?= $row['kondisi_sebelum'] === 'Baik' ? 'selected' : '' ?>>Baik</option>
                                                <option value="Rusak Ringan" <?= $row['kondisi_sebelum'] === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                                                <!-- Note: Rusak Berat units are blocked from being borrowed via stock checking in FR-09 -->
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="index.php?controller=peminjaman&action=serahTerimaList" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-circle-check"></i> Konfirmasi Serah Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>
