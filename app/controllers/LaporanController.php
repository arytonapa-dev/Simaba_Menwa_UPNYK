<?php
/**
 * Laporan Controller
 * Pembuatan Laporan & Ekspor (Admin, Operator, Dansat) - FR-17, FR-18, FR-19
 */

require_once dirname(__DIR__) . '/models/KategoriBarang.php';
require_once dirname(__DIR__) . '/models/BidangBarang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Database.php';

class LaporanController extends Controller {

    /**
     * Display reports tab page (FR-17, FR-18)
     */
    public function index() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_DANSAT]);

        $catModel = new KategoriBarang();
        $categories = $catModel->findAll();

        $bidModel = new BidangBarang();
        $sections = $bidModel->findAll();

        $this->view('laporan/index', [
            'title' => 'Laporan Inventaris & Transaksi',
            'categories' => $categories,
            'sections' => $sections
        ]);
    }

    /**
     * Generate HTML output based on filter input
     */
    public function generate() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_DANSAT]);
        
        $type = $this->input('type'); // 'inventaris' or 'transaksi'
        $db = Database::getInstance()->getConnection();

        if ($type === 'inventaris') {
            $catId = $this->input('kategori_id');
            $bidId = $this->input('bidang_id');
            $kondisi = $this->input('kondisi');

            $sql = "SELECT b.nama_barang, b.satuan, k.nama_kategori, bi.nama_bidang, u.kode_unit, u.kondisi, u.status_ketersediaan, u.tanggal_pengadaan
                    FROM unit_barang u
                    JOIN barang b ON u.barang_id = b.barang_id
                    JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                    JOIN bidang_barang bi ON b.bidang_id = bi.bidang_id
                    WHERE u.status_ketersediaan != 'Hilang'"; // Exclude lost units from active stock (FR-17)
            
            $params = [];
            if (!empty($catId)) {
                $sql .= " AND b.kategori_id = :cat_id";
                $params['cat_id'] = $catId;
            }
            if (!empty($bidId)) {
                $sql .= " AND b.bidang_id = :bid_id";
                $params['bid_id'] = $bidId;
            }
            if (!empty($kondisi)) {
                $sql .= " AND u.kondisi = :kondisi";
                $params['kondisi'] = $kondisi;
            }

            $sql .= " ORDER BY b.nama_barang ASC, u.kode_unit ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            $this->json(['status' => 'success', 'data' => $results]);

        } elseif ($type === 'transaksi') {
            try {
                $startDate = $this->input('start_date');
                $endDate = $this->input('end_date');
                $status = $this->input('status');

                // Lateness range rules check (FR-18 / TC-18 / TC-18b)
                if (empty($startDate) || empty($endDate)) {
                    throw new Exception("Rentang tanggal wajib diisi untuk laporan transaksi. (TC-18)");
                }

                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $diff = $start->diff($end);

                if ($end < $start) {
                    throw new Exception("Tanggal akhir tidak boleh sebelum tanggal awal.");
                }
                if ($diff->y >= 1 && $diff->days > 365) {
                    throw new Exception("Rentang filter tanggal transaksi maksimal adalah 1 tahun untuk menjaga performa. (TC-18)");
                }

                $sql = "SELECT p.peminjaman_id, u.full_name, u.nim_nip, p.tanggal_pinjam, p.tanggal_rencana_kembali, p.tanggal_serah_terima, p.status, p.keperluan,
                        (SELECT COUNT(*) FROM detail_peminjaman dp WHERE dp.peminjaman_id = p.peminjaman_id) as total_items
                        FROM peminjaman p
                        JOIN users u ON p.user_id = u.user_id
                        WHERE p.created_at BETWEEN :start_date AND :end_date";
                
                $params = [
                    'start_date' => $startDate . ' 00:00:00',
                    'end_date' => $endDate . ' 23:59:59'
                ];

                if (!empty($status)) {
                    $sql .= " AND p.status = :status";
                    $params['status'] = $status;
                }

                $sql .= " ORDER BY p.created_at DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetchAll();

                $this->json(['status' => 'success', 'data' => $results]);

            } catch (Exception $e) {
                $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
        }
    }

    /**
     * Export to Excel (FR-19)
     */
    public function exportExcel() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_DANSAT]);

        $type = $this->input('type');
        $db = Database::getInstance()->getConnection();
        
        $filename = "Laporan_" . ucfirst($type) . "_" . date('Ymd_His') . ".xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");

        $titleReport = $type === 'inventaris' ? 'LAPORAN INVENTARIS BARANG' : 'LAPORAN TRANSAKSI PEMINJAMAN';

        // Information Header (FR-19 / TC-19)
        echo "<h3>RESIMEN MAHASISWA UPN \"VETERAN\" YOGYAKARTA</h3>";
        echo "<h2>" . $titleReport . "</h2>";
        echo "<p>Tanggal Unduh: " . date('d-m-Y H:i:s') . "</p>";

        if ($type === 'inventaris') {
            $catId = $this->input('kategori_id');
            $bidId = $this->input('bidang_id');
            $kondisi = $this->input('kondisi');

            $sql = "SELECT b.nama_barang, b.satuan, k.nama_kategori, bi.nama_bidang, u.kode_unit, u.kondisi, u.status_ketersediaan, u.tanggal_pengadaan
                    FROM unit_barang u
                    JOIN barang b ON u.barang_id = b.barang_id
                    JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                    JOIN bidang_barang bi ON b.bidang_id = bi.bidang_id
                    WHERE u.status_ketersediaan != 'Hilang'";
            
            $params = [];
            if (!empty($catId)) {
                $sql .= " AND b.kategori_id = :cat_id";
                $params['cat_id'] = $catId;
            }
            if (!empty($bidId)) {
                $sql .= " AND b.bidang_id = :bid_id";
                $params['bid_id'] = $bidId;
            }
            if (!empty($kondisi)) {
                $sql .= " AND u.kondisi = :kondisi";
                $params['kondisi'] = $kondisi;
            }

            $sql .= " ORDER BY b.nama_barang ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            echo "<table border='1'>";
            echo "<tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kode Unit</th>
                    <th>Kategori</th>
                    <th>Bidang</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Tanggal Pengadaan</th>
                  </tr>";
            
            $i = 1;
            foreach ($results as $r) {
                echo "<tr>
                        <td>{$i}</td>
                        <td>" . htmlspecialchars($r['nama_barang']) . "</td>
                        <td>" . htmlspecialchars($r['kode_unit']) . "</td>
                        <td>" . htmlspecialchars($r['nama_kategori']) . "</td>
                        <td>" . htmlspecialchars($r['nama_bidang']) . "</td>
                        <td>" . htmlspecialchars($r['kondisi']) . "</td>
                        <td>" . htmlspecialchars($r['status_ketersediaan']) . "</td>
                        <td>" . date('d/m/Y', strtotime($r['tanggal_pengadaan'])) . "</td>
                      </tr>";
                $i++;
            }
            echo "</table>";

        } else {
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');
            $status = $this->input('status');

            $sql = "SELECT p.peminjaman_id, u.full_name, u.nim_nip, p.tanggal_pinjam, p.tanggal_rencana_kembali, p.tanggal_serah_terima, p.status, p.keperluan
                    FROM peminjaman p
                    JOIN users u ON p.user_id = u.user_id
                    WHERE p.created_at BETWEEN :start_date AND :end_date";
            
            $params = [
                'start_date' => $startDate . ' 00:00:00',
                'end_date' => $endDate . ' 23:59:59'
            ];

            if (!empty($status)) {
                $sql .= " AND p.status = :status";
                $params['status'] = $status;
            }

            $sql .= " ORDER BY p.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            echo "<p>Rentang Laporan: " . date('d-m-Y', strtotime($startDate)) . " s.d. " . date('d-m-Y', strtotime($endDate)) . "</p>";
            echo "<table border='1'>";
            echo "<tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>NIM/NBP</th>
                    <th>Tanggal Pinjam</th>
                    <th>Batas Pengembalian</th>
                    <th>Status</th>
                    <th>Keperluan</th>
                  </tr>";
            
            $i = 1;
            foreach ($results as $r) {
                echo "<tr>
                        <td>{$i}</td>
                        <td>" . htmlspecialchars($r['full_name']) . "</td>
                        <td>" . htmlspecialchars($r['nim_nip']) . "</td>
                        <td>" . date('d/m/Y', strtotime($r['tanggal_pinjam'])) . "</td>
                        <td>" . date('d/m/Y', strtotime($r['tanggal_rencana_kembali'])) . "</td>
                        <td>" . htmlspecialchars($r['status']) . "</td>
                        <td>" . htmlspecialchars($r['keperluan']) . "</td>
                      </tr>";
                $i++;
            }
            echo "</table>";
        }
        exit();
    }

    /**
     * Export to PDF / Print View (FR-19)
     */
    public function exportPdf() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_DANSAT]);

        $type = $this->input('type');
        $db = Database::getInstance()->getConnection();

        $titleReport = $type === 'inventaris' ? 'LAPORAN INVENTARIS BARANG' : 'LAPORAN TRANSAKSI PEMINJAMAN';

        $data = [
            'title' => $titleReport,
            'type' => $type,
            'date_generated' => date('d-m-Y H:i:s')
        ];

        if ($type === 'inventaris') {
            $catId = $this->input('kategori_id');
            $bidId = $this->input('bidang_id');
            $kondisi = $this->input('kondisi');

            $sql = "SELECT b.nama_barang, b.satuan, k.nama_kategori, bi.nama_bidang, u.kode_unit, u.kondisi, u.status_ketersediaan, u.tanggal_pengadaan
                    FROM unit_barang u
                    JOIN barang b ON u.barang_id = b.barang_id
                    JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                    JOIN bidang_barang bi ON b.bidang_id = bi.bidang_id
                    WHERE u.status_ketersediaan != 'Hilang'";
            
            $params = [];
            if (!empty($catId)) {
                $sql .= " AND b.kategori_id = :cat_id";
                $params['cat_id'] = $catId;
            }
            if (!empty($bidId)) {
                $sql .= " AND b.bidang_id = :bid_id";
                $params['bid_id'] = $bidId;
            }
            if (!empty($kondisi)) {
                $sql .= " AND u.kondisi = :kondisi";
                $params['kondisi'] = $kondisi;
            }

            $sql .= " ORDER BY b.nama_barang ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $data['results'] = $stmt->fetchAll();

        } else {
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');
            $status = $this->input('status');

            $sql = "SELECT p.peminjaman_id, u.full_name, u.nim_nip, p.tanggal_pinjam, p.tanggal_rencana_kembali, p.tanggal_serah_terima, p.status, p.keperluan
                    FROM peminjaman p
                    JOIN users u ON p.user_id = u.user_id
                    WHERE p.created_at BETWEEN :start_date AND :end_date";
            
            $params = [
                'start_date' => $startDate . ' 00:00:00',
                'end_date' => $endDate . ' 23:59:59'
            ];

            if (!empty($status)) {
                $sql .= " AND p.status = :status";
                $params['status'] = $status;
            }

            $sql .= " ORDER BY p.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $data['results'] = $stmt->fetchAll();
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;
        }

        // Render PDF Print Layout (Clean shell-less print page)
        $this->view('laporan/print', $data, null);
    }
}
