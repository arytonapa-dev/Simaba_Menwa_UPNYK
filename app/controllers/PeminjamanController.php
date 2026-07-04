<?php
/**
 * Peminjaman Controller
 * Alur Peminjaman (Ajukan, Verifikasi, Persetujuan Kritis, Serah Terima, Riwayat) - FR-09, FR-10, FR-11, FR-12, FR-15
 */

require_once dirname(__DIR__) . '/models/Peminjaman.php';
require_once dirname(__DIR__) . '/models/DetailPeminjaman.php';
require_once dirname(__DIR__) . '/models/Barang.php';
require_once dirname(__DIR__) . '/models/UnitBarang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';
require_once dirname(dirname(__DIR__)) . '/core/Notification.php';

class PeminjamanController extends Controller {

    /**
     * Ajukan Peminjaman View & Process (Anggota - FR-09)
     */
    public function ajukan() {
        Auth::restrict([ROLE_ANGGOTA]);
        
        $errors = [];
        $barangModel = new Barang();
        // Load items with details to show available counts
        $items = $barangModel->getAllWithAggregates();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $tanggalPinjam = $this->input('tanggal_pinjam');
                $tanggalRencanaKembali = $this->input('tanggal_rencana_kembali');
                $keperluan = trim($this->input('keperluan'));
                $requestedItems = $_POST['items'] ?? []; // format: [barang_id => qty]

                // Validate main fields
                if (empty($tanggalPinjam) || empty($tanggalRencanaKembali) || empty($keperluan)) {
                    throw new Exception("Seluruh kolom wajib diisi.");
                }
                if (strlen($keperluan) < 10) {
                    throw new Exception("Keperluan peminjaman minimal 10 karakter.");
                }

                // Validate dates (TC-09b)
                $pinjamDate = new DateTime($tanggalPinjam);
                $kembaliDate = new DateTime($tanggalRencanaKembali);
                if ($kembaliDate < $pinjamDate) {
                    throw new Exception("Tanggal rencana kembali tidak boleh sebelum tanggal pinjam. (TC-09b)");
                }

                // Filter items with qty > 0
                $loanItems = [];
                foreach ($requestedItems as $barangId => $qty) {
                    $qty = (int)$qty;
                    if ($qty > 0) {
                        $loanItems[$barangId] = $qty;
                    }
                }

                if (empty($loanItems)) {
                    throw new Exception("Silakan pilih minimal satu barang untuk dipinjam.");
                }

                // Validate stocks (TC-09 / FR-09)
                $unitModel = new UnitBarang();
                foreach ($loanItems as $barangId => $qty) {
                    $availableUnits = $unitModel->getAvailableUnitsByBarang($barangId);
                    if (count($availableUnits) < $qty) {
                        $b = $barangModel->find($barangId);
                        throw new Exception("Stok tidak mencukupi untuk barang " . $b['nama_barang'] . ". Tersedia: " . count($availableUnits) . " unit.");
                    }
                }

                // Save loan
                $peminjamanModel = new Peminjaman();
                $userId = $_SESSION['user_id'];
                $pId = $peminjamanModel->createLoan($userId, $loanItems, $tanggalPinjam, $tanggalRencanaKembali, $keperluan);

                $this->redirect('/index.php?controller=peminjaman&action=riwayat&success=1');

            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('peminjaman/ajukan', [
            'title' => 'Ajukan Peminjaman Barang',
            'items' => $items,
            'errors' => $errors
        ]);
    }

    /**
     * Verification list for Operators (FR-10)
     */
    public function verifikasiList() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);
        
        $peminjamanModel = new Peminjaman();
        // Show loans waiting operator verif, or all loans in active status
        $loans = $peminjamanModel->getAllWithUser();

        $this->view('peminjaman/verifikasi_list', [
            'title' => 'Daftar Verifikasi Peminjaman',
            'loans' => $loans
        ]);
    }

    /**
     * Operator Verification Page & Process (FR-10)
     */
    public function verifikasi() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $id = (int)$this->input('id');
        $peminjamanModel = new Peminjaman();
        $loan = $peminjamanModel->findDetailed($id);

        if (!$loan || $loan['status'] !== STATUS_PINJAM_VERIF_WAIT) {
            $this->redirect('/index.php?controller=peminjaman&action=verifikasiList');
        }

        $detailModel = new DetailPeminjaman();
        $details = $detailModel->getUnitsByPeminjaman($id);
        $isCritical = $peminjamanModel->hasCriticalItems($id);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $decision = $this->input('decision'); // 'approve' or 'reject'
                $alasanTolak = trim($this->input('alasan_tolak'));

                if ($decision === 'reject') {
                    if (empty($alasanTolak) || strlen($alasanTolak) < 10) {
                        throw new Exception("Alasan penolakan wajib diisi minimal 10 karakter.");
                    }

                    // Update to Ditolak
                    $peminjamanModel->update($id, [
                        'status' => STATUS_PINJAM_REJECTED,
                        'alasan_tolak' => $alasanTolak,
                        'verifikator_id' => $_SESSION['user_id'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    AuditLog::log("Operator Menolak Pengajuan Peminjaman #" . $id, 'peminjaman', $loan, $peminjamanModel->find($id));
                    Notification::send($loan['user_id'], 'Peminjaman Ditolak', 'Pengajuan Peminjaman Ditolak', 'Pengajuan peminjaman #' . $id . ' ditolak oleh Operator dengan alasan: ' . $alasanTolak, "/index.php?controller=peminjaman&action=riwayat");

                    $this->redirect('/index.php?controller=peminjaman&action=verifikasiList&success=rejected');
                } else {
                    // Check if critical items exist (BR-03)
                    $isCritical = $peminjamanModel->hasCriticalItems($id);

                    if ($isCritical) {
                        // Escalation mandated to Dansat (BR-03)
                        $peminjamanModel->update($id, [
                            'status' => STATUS_PINJAM_DANSAT_WAIT,
                            'verifikator_id' => $_SESSION['user_id'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        AuditLog::log("Eskalasi Peminjaman Kritis #" . $id . " ke Dansat", 'peminjaman', $loan, $peminjamanModel->find($id));
                        
                        // Notify Dansat
                        Notification::sendToRole(ROLE_DANSAT, 'Persetujuan Kritis', 'Peminjaman Kritis Butuh Persetujuan', 'Terdapat pengajuan peminjaman barang kritis #' . $id . ' yang membutuhkan keputusan Anda.', "/index.php?controller=peminjaman&action=verifikasiKritis&id=" . $id);

                        $this->redirect('/index.php?controller=peminjaman&action=verifikasiList&success=escalated');
                    } else {
                        // Approve loan directly
                        $peminjamanModel->update($id, [
                            'status' => STATUS_PINJAM_APPROVED,
                            'verifikator_id' => $_SESSION['user_id'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        AuditLog::log("Operator Menyetujui Peminjaman #" . $id, 'peminjaman', $loan, $peminjamanModel->find($id));
                        Notification::send($loan['user_id'], 'Peminjaman Disetujui', 'Pengajuan Peminjaman Disetujui', 'Pengajuan peminjaman #' . $id . ' telah disetujui. Silakan menemui Operator untuk serah terima barang.', "/index.php?controller=peminjaman&action=riwayat");

                        $this->redirect('/index.php?controller=peminjaman&action=verifikasiList&success=approved');
                    }
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('peminjaman/verifikasi', [
            'title' => 'Verifikasi Pengajuan',
            'loan' => $loan,
            'details' => $details,
            'isCritical' => $isCritical,
            'errors' => $errors
        ]);
    }

    /**
     * Escalation list for Dansat (FR-11)
     */
    public function verifikasiKritisList() {
        Auth::restrict([ROLE_ADMIN, ROLE_DANSAT]);

        $peminjamanModel = new Peminjaman();
        $loans = $peminjamanModel->getAllWithUser(STATUS_PINJAM_DANSAT_WAIT);

        $this->view('peminjaman/verifikasi_kritis_list', [
            'title' => 'Daftar Persetujuan Kritis Dansat',
            'loans' => $loans
        ]);
    }

    /**
     * Dansat Critical Verification Page (FR-11)
     */
    public function verifikasiKritis() {
        Auth::restrict([ROLE_ADMIN, ROLE_DANSAT]);

        $id = (int)$this->input('id');
        $peminjamanModel = new Peminjaman();
        $loan = $peminjamanModel->findDetailed($id);

        if (!$loan || $loan['status'] !== STATUS_PINJAM_DANSAT_WAIT) {
            $this->redirect('/index.php?controller=peminjaman&action=verifikasiKritisList');
        }

        $detailModel = new DetailPeminjaman();
        $details = $detailModel->getUnitsByPeminjaman($id);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $decision = $this->input('decision');
                $alasanTolak = trim($this->input('alasan_tolak'));

                if ($decision === 'reject') {
                    if (empty($alasanTolak) || strlen($alasanTolak) < 10) {
                        throw new Exception("Alasan penolakan wajib diisi minimal 10 karakter.");
                    }

                    // Update to Ditolak oleh Dansat
                    $peminjamanModel->update($id, [
                        'status' => STATUS_PINJAM_REJECTED_DANSAT,
                        'alasan_tolak' => $alasanTolak,
                        'approver_dansat_id' => $_SESSION['user_id'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    AuditLog::log("Dansat Menolak Peminjaman Kritis #" . $id, 'peminjaman', $loan, $peminjamanModel->find($id));
                    Notification::send($loan['user_id'], 'Peminjaman Ditolak Dansat', 'Pengajuan Peminjaman Kritis Ditolak', 'Pengajuan peminjaman kritis #' . $id . ' ditolak oleh Dansat dengan alasan: ' . $alasanTolak, "/index.php?controller=peminjaman&action=riwayat");

                    $this->redirect('/index.php?controller=peminjaman&action=verifikasiKritisList&success=rejected');
                } else {
                    // Approve critical loan (FR-11)
                    $peminjamanModel->update($id, [
                        'status' => STATUS_PINJAM_APPROVED,
                        'approver_dansat_id' => $_SESSION['user_id'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    AuditLog::log("Dansat Menyetujui Peminjaman Kritis #" . $id, 'peminjaman', $loan, $peminjamanModel->find($id));
                    Notification::send($loan['user_id'], 'Peminjaman Kritis Disetujui', 'Peminjaman Kritis Disetujui Dansat', 'Pengajuan peminjaman kritis #' . $id . ' disetujui Dansat. Silakan hubungi Operator untuk serah terima barang.', "/index.php?controller=peminjaman&action=riwayat");

                    $this->redirect('/index.php?controller=peminjaman&action=verifikasiKritisList&success=approved');
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('peminjaman/verifikasi_kritis', [
            'title' => 'Persetujuan Peminjaman Kritis',
            'loan' => $loan,
            'details' => $details,
            'errors' => $errors
        ]);
    }

    /**
     * Handover list (FR-12)
     */
    public function serahTerimaList() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $peminjamanModel = new Peminjaman();
        $loans = $peminjamanModel->getAllWithUser(STATUS_PINJAM_APPROVED);

        $this->view('peminjaman/serah_terima_list', [
            'title' => 'Serah Terima Barang',
            'loans' => $loans
        ]);
    }

    /**
     * Serah Terima hand-over processing (FR-12, BR-02)
     */
    public function serahTerima() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $id = (int)$this->input('id');
        $peminjamanModel = new Peminjaman();
        $loan = $peminjamanModel->findDetailed($id);

        if (!$loan || $loan['status'] !== STATUS_PINJAM_APPROVED) {
            $this->redirect('/index.php?controller=dashboard&action=index');
        }

        $detailModel = new DetailPeminjaman();
        $details = $detailModel->getUnitsByPeminjaman($id);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $unitConditions = $_POST['kondisi_serah'] ?? [];
                $unitModel = new UnitBarang();

                // Start Transaction for Hand-Over
                $db = Database::getInstance()->getConnection();
                $db->beginTransaction();

                // 1. Update each unit condition to whatever is checked, and status to 'Dipinjam' (BR-02)
                foreach ($details as $row) {
                    $unitId = $row['unit_id'];
                    $kondisiSaatSerah = $unitConditions[$unitId] ?? COND_BAIK;
                    
                    // Single source of truth update
                    $unitModel->updateKondisiDanStatus($unitId, $kondisiSaatSerah, STATUS_DIPINJAM);

                    // Update kondisi_saat_pinjam in detail_peminjaman
                    $sqlUpd = "UPDATE detail_peminjaman SET kondisi_saat_pinjam = :cond WHERE detail_peminjaman_id = :id";
                    $stmtUpd = $db->prepare($sqlUpd);
                    $stmtUpd->execute([
                        'cond' => $kondisiSaatSerah,
                        'id' => $row['detail_peminjaman_id']
                    ]);
                }

                // 2. Set loan status to 'Dipinjam (Berjalan)' (BR-02)
                $peminjamanModel->update($id, [
                    'status' => STATUS_PINJAM_ONGOING,
                    'tanggal_serah_terima' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $db->commit();

                AuditLog::log("Serah Terima Peminjaman #" . $id . " Berhasil", 'peminjaman');
                Notification::send($loan['user_id'], 'Barang Diserahkan', 'Serah Terima Barang Selesai', 'Barang peminjaman #' . $id . ' telah Anda terima secara fisik. Selamat berlatih.', "/index.php?controller=peminjaman&action=riwayat");

                $this->redirect('/index.php?controller=dashboard&action=index&success=handover');

            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('peminjaman/serah_terima', [
            'title' => 'Form Serah Terima Barang',
            'loan' => $loan,
            'details' => $details,
            'errors' => $errors
        ]);
    }

    /**
     * Riwayat Peminjaman Pribadi (Anggota - FR-15)
     */
    public function riwayat() {
        Auth::restrict([ROLE_ANGGOTA]);

        $peminjamanModel = new Peminjaman();
        $userId = $_SESSION['user_id'];
        $loans = $peminjamanModel->getByUserId($userId);

        $this->view('peminjaman/riwayat', [
            'title' => 'Riwayat Peminjaman Saya',
            'loans' => $loans
        ]);
    }

    /**
     * View detail of a single loan (Row-level access protection - FR-15b)
     */
    public function detail() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);

        $id = (int)$this->input('id');
        $peminjamanModel = new Peminjaman();
        $loan = $peminjamanModel->findDetailed($id);

        if (!$loan) {
            $this->redirect('/index.php?controller=dashboard&action=index');
        }

        // Row-Level access restriction: Anggota cannot view others' loans (Exception Handling FR-15b / UC-15 / TC-15b)
        if ($_SESSION['role_id'] == ROLE_ANGGOTA && $loan['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            require_once dirname(dirname(__DIR__)) . '/app/views/layouts/403.php';
            exit();
        }

        $detailModel = new DetailPeminjaman();
        $details = $detailModel->getUnitsByPeminjaman($id);

        $this->view('peminjaman/detail', [
            'title' => 'Detail Transaksi Peminjaman',
            'loan' => $loan,
            'details' => $details
        ]);
    }
}
