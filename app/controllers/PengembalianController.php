<?php
/**
 * Pengembalian Controller
 * Alur Pengembalian (Ajukan, Verifikasi, Verifikasi List) - FR-13, FR-14
 */

require_once dirname(__DIR__) . '/models/Pengembalian.php';
require_once dirname(__DIR__) . '/models/DetailPengembalian.php';
require_once dirname(__DIR__) . '/models/Peminjaman.php';
require_once dirname(__DIR__) . '/models/DetailPeminjaman.php';
require_once dirname(__DIR__) . '/models/UnitBarang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';
require_once dirname(dirname(__DIR__)) . '/core/Notification.php';

class PengembalianController extends Controller {

    /**
     * Anggota submits return request (FR-13)
     */
    public function ajukan() {
        Auth::restrict([ROLE_ANGGOTA]);

        $peminjamanId = isset($_GET['peminjaman_id']) ? (int)$_GET['peminjaman_id'] : null;
        $userId = $_SESSION['user_id'];

        $peminjamanModel = new Peminjaman();
        $detailModel = new DetailPeminjaman();

        // 1. Get borrower's active ongoing loans (status = 'Dipinjam (Berjalan)')
        $activeLoans = [];
        $ongoingLoans = $peminjamanModel->getByUserId($userId);
        foreach ($ongoingLoans as $l) {
            if ($l['status'] === STATUS_PINJAM_ONGOING) {
                $activeLoans[] = $l;
            }
        }

        // 2. If a specific loan is selected, get its units that are NOT yet returned
        $unitsToReturn = [];
        $selectedLoan = null;

        if ($peminjamanId) {
            $selectedLoan = $peminjamanModel->findDetailed($peminjamanId);
            
            // Check ownership
            if ($selectedLoan && $selectedLoan['user_id'] == $userId && $selectedLoan['status'] === STATUS_PINJAM_ONGOING) {
                $allUnits = $detailModel->getUnitsByPeminjaman($peminjamanId);
                
                // Exclude units that have already been returned in some complete return request
                $db = Database::getInstance()->getConnection();
                $sqlReturned = "SELECT dp.unit_id 
                                FROM detail_pengembalian dp
                                JOIN pengembalian r ON dp.pengembalian_id = r.pengembalian_id
                                WHERE r.peminjaman_id = :peminjaman_id AND r.status = 'Selesai'";
                $stmtR = $db->prepare($sqlReturned);
                $stmtR->execute(['peminjaman_id' => $peminjamanId]);
                $returnedUnitIds = $stmtR->fetchAll(PDO::FETCH_COLUMN);

                foreach ($allUnits as $unit) {
                    if (!in_array($unit['unit_id'], $returnedUnitIds)) {
                        $unitsToReturn[] = $unit;
                    }
                }
            } else {
                $peminjamanId = null;
            }
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $pId = (int)$this->input('peminjaman_id');
                $selfReports = $_POST['self_report'] ?? []; // format: [unit_id => kondisi]
                $selectedUnits = $_POST['selected_units'] ?? []; // format: [unit_id]

                if (empty($selectedUnits)) {
                    throw new Exception("Silakan pilih minimal satu unit barang yang ingin dikembalikan.");
                }

                // Filter self reports to only selected units
                $unitData = [];
                foreach ($selectedUnits as $unitId) {
                    $unitData[$unitId] = $selfReports[$unitId] ?? COND_BAIK;
                }

                $returnModel = new Pengembalian();
                $returnModel->createReturn($pId, $userId, $unitData);

                $this->redirect('/index.php?controller=peminjaman&action=riwayat&success_return=1');

            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('pengembalian/ajukan', [
            'title' => 'Ajukan Pengembalian Barang',
            'activeLoans' => $activeLoans,
            'peminjamanId' => $peminjamanId,
            'selectedLoan' => $selectedLoan,
            'unitsToReturn' => $unitsToReturn,
            'errors' => $errors
        ]);
    }

    /**
     * Operator return list (FR-14)
     */
    public function verifikasiList() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $returnModel = new Pengembalian();
        $returns = $returnModel->getAllWithUser();

        $this->view('pengembalian/verifikasi_list', [
            'title' => 'Verifikasi Pengembalian',
            'returns' => $returns
        ]);
    }

    /**
     * Operator returns verification form & process (FR-14)
     */
    public function verifikasi() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $id = (int)$this->input('id');
        $returnModel = new Pengembalian();
        $return = $returnModel->findDetailed($id);

        if (!$return || $return['status'] !== STATUS_KEMBALI_VERIF_WAIT) {
            $this->redirect('/index.php?controller=pengembalian&action=verifikasiList');
        }

        $detailModel = new DetailPengembalian();
        $details = $detailModel->getUnitsByPengembalian($id);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $unitConditions = $_POST['kondisi_akhir'] ?? [];
                $unitStatuses = $_POST['status_ketersediaan'] ?? [];
                $catatan = trim($this->input('catatan'));

                // Verify conditions matches BR-01 constraints
                foreach ($details as $row) {
                    $uId = $row['unit_id'];
                    $cond = $unitConditions[$uId] ?? COND_BAIK;
                    $status = $unitStatuses[$uId] ?? STATUS_TERSEDIA;

                    // Enforce BR-01 validation (TC-08 / BR-08b)
                    if ($cond === 'Baik') {
                        if ($status !== 'Tersedia' && $status !== 'Dipinjam') {
                            throw new Exception("Unit " . $row['kode_unit'] . " (Kondisi Baik) hanya boleh berstatus Tersedia atau Dipinjam.");
                        }
                    } else if ($cond === 'Rusak Ringan') {
                        if ($status !== 'Tersedia' && $status !== 'Perbaikan') {
                            throw new Exception("Unit " . $row['kode_unit'] . " (Kondisi Rusak Ringan) hanya boleh berstatus Tersedia atau Perbaikan.");
                        }
                    } else if ($cond === 'Rusak Berat') {
                        if ($status !== 'Perbaikan' && $status !== 'Hilang') {
                            throw new Exception("Unit " . $row['kode_unit'] . " (Kondisi Rusak Berat) hanya boleh berstatus Perbaikan atau Hilang. (Tolak Tersedia/Dipinjam)");
                        }
                    }
                }

                // Process verification and update stock/peminjaman parent states
                $returnModel->verifyReturn($id, $_SESSION['user_id'], $unitConditions, $unitStatuses, $catatan);

                // Check if any unit is Hilang to trigger escalation notifications
                foreach ($details as $row) {
                    $uId = $row['unit_id'];
                    $cond = $unitConditions[$uId] ?? COND_BAIK;
                    $status = $unitStatuses[$uId] ?? STATUS_TERSEDIA;
                    
                    if ($status === STATUS_HILANG) {
                        // Alert Admin & Dansat (Alternative Flow UC-14 / FR-14)
                        Notification::sendToRole(ROLE_ADMIN, 'Kehilangan', 'Unit Barang Hilang Terdeteksi', 'Unit ' . $row['kode_unit'] . ' dari barang ' . $row['nama_barang'] . ' dilaporkan hilang saat pengembalian.');
                        Notification::sendToRole(ROLE_DANSAT, 'Kehilangan', 'Unit Barang Hilang Terdeteksi', 'Unit ' . $row['kode_unit'] . ' dari barang ' . $row['nama_barang'] . ' dilaporkan hilang saat pengembalian.');
                    }
                }

                $this->redirect('/index.php?controller=pengembalian&action=verifikasiList&success=1');

            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('pengembalian/verifikasi', [
            'title' => 'Verifikasi Kondisi Pengembalian',
            'return' => $return,
            'details' => $details,
            'errors' => $errors
        ]);
    }
}
