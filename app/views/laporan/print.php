<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 16px;
            margin: 0 0 5px 0;
            font-weight: normal;
        }
        .header p {
            font-size: 12px;
            margin: 0;
            color: #666;
        }
        .meta-info {
            font-size: 12px;
            margin-bottom: 20px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 30px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
        }
        .signature-section {
            width: 100%;
            margin-top: 50px;
            font-size: 13px;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm;
            }
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header Kop Surat (FR-19) -->
    <div class="header">
        <h1>Resimen Mahasiswa (MENWA) Satuan UPN "Veteran" Yogyakarta</h1>
        <h2>Universitas Pembangunan Nasional "Veteran" Yogyakarta</h2>
        <p>Alamat: Jalan SWK 104 (Lembuelap) Condongcatur, Sleman, Yogyakarta 55283</p>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 18px; text-transform: uppercase;"><?= htmlspecialchars($title) ?></h2>
    </div>

    <!-- Metadata Laporan -->
    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 120px;">Jenis Laporan</td>
                <td>: <?= $type === 'inventaris' ? 'Laporan Inventaris Barang Aktif' : 'Rekapitulasi Transaksi Peminjaman' ?></td>
                <td style="text-align: right;">Tanggal Cetak: <?= $date_generated ?></td>
            </tr>
            <?php if ($type === 'transaksi'): ?>
                <tr>
                    <td>Rentang Laporan</td>
                    <td>: <?= date('d/m/Y', strtotime($start_date)) ?> s.d. <?= date('d/m/Y', strtotime($end_date)) ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <?php if ($type === 'inventaris'): ?>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Barang</th>
                    <th>Kode Unit</th>
                    <th>Kategori</th>
                    <th>Bidang</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data terdaftar</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1; foreach ($results as $r): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                            <td><code><?= htmlspecialchars($r['kode_unit']) ?></code></td>
                            <td><?= htmlspecialchars($r['nama_kategori']) ?></td>
                            <td><?= htmlspecialchars($r['nama_bidang']) ?></td>
                            <td><?= htmlspecialchars($r['kondisi']) ?></td>
                            <td><?= htmlspecialchars($r['status_ketersediaan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        <?php else: ?>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Ref ID</th>
                    <th>Peminjam</th>
                    <th>NIM/NBP</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada transaksi ditemukan</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1; foreach ($results as $r): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><strong>#PJ-<?= $r['peminjaman_id'] ?></strong></td>
                            <td><?= htmlspecialchars($r['full_name']) ?></td>
                            <td><?= htmlspecialchars($r['nim_nip']) ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tanggal_pinjam'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tanggal_rencana_kembali'])) ?></td>
                            <td><?= htmlspecialchars($r['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        <?php endif; ?>
    </table>

    <!-- Signature Khas Laporan Militer -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Yogyakarta, <?= date('d F Y') ?></p>
            <p>Komandan Satuan,</p>
            <div class="signature-space"></div>
            <p><strong><u>Komandan Satuan MENWA</u></strong></p>
            <p>NBP/NIM. 444444444</p>
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
