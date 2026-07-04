<?php
/**
 * Laporan Index View (FR-17, FR-18)
 */
?>
<div class="mb-4">
    <h1 class="h2 font-heading" style="color: var(--text-primary);">Halaman Laporan</h1>
    <span class="text-muted small">Cari data inventaris dan rekapitulasi transaksi logistik MENWA</span>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link nav-link-custom active border-0" id="inventaris-tab" data-bs-toggle="tab" data-bs-target="#inventaris-pane" type="button" role="tab" aria-selected="true" onclick="setReportType('inventaris')">
            <i class="fa-solid fa-boxes-stacked"></i> Laporan Inventaris
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link nav-link-custom border-0" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi-pane" type="button" role="tab" aria-selected="false" onclick="setReportType('transaksi')">
            <i class="fa-solid fa-exchange-alt"></i> Laporan Transaksi
        </button>
    </li>
</ul>

<div class="tab-content" id="reportTabsContent">
    <!-- 1. INVENTARIS TAB -->
    <div class="tab-pane fade show active" id="inventaris-pane" role="tabpanel" aria-labelledby="inventaris-tab">
        <div class="glass-card p-4 mb-4">
            <form id="inventarisForm" class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label for="kategori_id" class="form-label text-muted small">Kategori</label>
                    <select class="form-select" id="inv_kategori_id" name="kategori_id">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['kategori_id'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="bidang_id" class="form-label text-muted small">Bidang / PJ</label>
                    <select class="form-select" id="inv_bidang_id" name="bidang_id">
                        <option value="">Semua Bidang</option>
                        <?php foreach ($sections as $sec): ?>
                            <option value="<?= $sec['bidang_id'] ?>"><?= htmlspecialchars($sec['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="kondisi" class="form-label text-muted small">Kondisi Unit</label>
                    <select class="form-select" id="inv_kondisi" name="kondisi">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 text-dark">
                        <i class="fa-solid fa-magnifying-glass"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. TRANSAKSI TAB -->
    <div class="tab-pane fade" id="transaksi-pane" role="tabpanel" aria-labelledby="transaksi-tab">
        <div class="glass-card p-4 mb-4">
            <form id="transaksiForm" class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label text-muted small">Tanggal Awal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tr_start_date" name="start_date" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label text-muted small">Tanggal Akhir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tr_end_date" name="end_date" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label text-muted small">Status Transaksi</label>
                    <select class="form-select" id="tr_status" name="status">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                        <option value="Dipinjam (Berjalan)">Dipinjam (Berjalan)</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 text-dark">
                        <i class="fa-solid fa-magnifying-glass"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Output Laporan Grid -->
<div class="glass-card p-4 d-none" id="laporanOutputBox">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h3 class="h5 font-heading text-accent mb-0" id="outputTitle">Hasil Laporan</h3>
        <div class="d-inline-flex gap-2">
            <button onclick="triggerExport('excel')" class="btn btn-sm btn-outline-custom">
                <i class="fa-solid fa-file-excel text-success"></i> Ekspor Excel
            </button>
            <button onclick="triggerExport('pdf')" class="btn btn-sm btn-outline-custom">
                <i class="fa-solid fa-file-pdf text-danger"></i> Cetak / PDF
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern mb-0 align-middle" id="reportTable">
            <thead id="reportTableHead"></thead>
            <tbody id="reportTableBody"></tbody>
        </table>
    </div>
</div>

<!-- AJAX Laporan Engine -->
<script>
let currentReportType = 'inventaris';

function setReportType(type) {
    currentReportType = type;
    document.getElementById('laporanOutputBox').classList.add('d-none');
}

document.getElementById('inventarisForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetchReportData('inventaris');
});

document.getElementById('transaksiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetchReportData('transaksi');
});

function fetchReportData(type) {
    const outputBox = document.getElementById('laporanOutputBox');
    const tableHead = document.getElementById('reportTableHead');
    const tableBody = document.getElementById('reportTableBody');
    const outputTitle = document.getElementById('outputTitle');
    
    let url = `index.php?controller=laporan&action=generate&type=${type}`;
    let bodyData = '';

    if (type === 'inventaris') {
        const cat = document.getElementById('inv_kategori_id').value;
        const bid = document.getElementById('inv_bidang_id').value;
        const cond = document.getElementById('inv_kondisi').value;
        url += `&kategori_id=${cat}&bidang_id=${bid}&kondisi=${cond}`;
    } else {
        const start = document.getElementById('tr_start_date').value;
        const end = document.getElementById('tr_end_date').value;
        const status = document.getElementById('tr_status').value;
        
        if (!start || !end) {
            alert("Rentang tanggal wajib diisi!");
            return;
        }
        url += `&start_date=${start}&end_date=${end}&status=${status}`;
    }

    fetch(url)
    .then(async response => {
        const res = await response.json();
        if (!response.ok) {
            throw new Error(res.message || "Gagal memproses data laporan.");
        }
        
        outputBox.classList.remove('d-none');
        tableBody.innerHTML = '';
        
        if (type === 'inventaris') {
            outputTitle.innerText = "Hasil Laporan Inventaris Aktif";
            tableHead.innerHTML = `
                <tr>
                    <th>Nama Barang</th>
                    <th>Kode Unit</th>
                    <th>Kategori</th>
                    <th>Bidang</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Tgl Pengadaan</th>
                </tr>
            `;
            
            if (res.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data cocok dengan filter</td></tr>';
                return;
            }

            res.data.forEach(row => {
                let badgeCond = 'bg-success';
                if (row.kondisi === 'Rusak Ringan') badgeCond = 'bg-warning text-dark';
                else if (row.kondisi === 'Rusak Berat') badgeCond = 'bg-danger';

                tableBody.innerHTML += `
                    <tr>
                        <td><strong class="">${row.nama_barang}</strong></td>
                        <td><code>${row.kode_unit}</code></td>
                        <td>${row.nama_kategori}</td>
                        <td>${row.nama_bidang}</td>
                        <td><span class="badge ${badgeCond}">${row.kondisi}</span></td>
                        <td><span class="badge bg-secondary">${row.status_ketersediaan}</span></td>
                        <td>${new Date(row.tanggal_pengadaan).toLocaleDateString('id-ID')}</td>
                    </tr>
                `;
            });
        } else {
            outputTitle.innerText = "Hasil Laporan Transaksi Peminjaman";
            tableHead.innerHTML = `
                <tr>
                    <th>Ref ID</th>
                    <th>Pemohon</th>
                    <th>NIM/NBP</th>
                    <th>Rentang Pinjam</th>
                    <th>Jumlah Unit</th>
                    <th>Status</th>
                </tr>
            `;
            
            if (res.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada transaksi ditemukan untuk rentang tanggal tersebut</td></tr>';
                return;
            }

            res.data.forEach(row => {
                let badgeClass = 'bg-warning text-dark';
                if (row.status === 'Disetujui' || row.status === 'Selesai') badgeClass = 'bg-success';
                else if (row.status.startsWith('Ditolak')) badgeClass = 'bg-danger';

                tableBody.innerHTML += `
                    <tr>
                        <td><strong>#PJ-${row.peminjaman_id}</strong></td>
                        <td>${row.full_name}</td>
                        <td>${row.nim_nip}</td>
                        <td>${new Date(row.tanggal_pinjam).toLocaleDateString('id-ID')} s.d. ${new Date(row.tanggal_rencana_kembali).toLocaleDateString('id-ID')}</td>
                        <td>${row.total_items} Unit</td>
                        <td><span class="badge ${badgeClass}">${row.status}</span></td>
                    </tr>
                `;
            });
        }
    })
    .catch(err => {
        alert(err.message);
        outputBox.classList.add('d-none');
    });
}

function triggerExport(format) {
    let action = format === 'excel' ? 'exportExcel' : 'exportPdf';
    let url = `index.php?controller=laporan&action=${action}&type=${currentReportType}`;

    if (currentReportType === 'inventaris') {
        const cat = document.getElementById('inv_kategori_id').value;
        const bid = document.getElementById('inv_bidang_id').value;
        const cond = document.getElementById('inv_kondisi').value;
        url += `&kategori_id=${cat}&bidang_id=${bid}&kondisi=${cond}`;
    } else {
        const start = document.getElementById('tr_start_date').value;
        const end = document.getElementById('tr_end_date').value;
        const status = document.getElementById('tr_status').value;
        url += `&start_date=${start}&end_date=${end}&status=${status}`;
    }

    window.open(url, '_blank');
}
</script>
