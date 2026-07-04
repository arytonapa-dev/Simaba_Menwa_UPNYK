<?php
/**
 * Unit Controller
 * Kelola Unit Barang & Update Kondisi (Operator) - FR-07, FR-08
 */

require_once dirname(__DIR__) . '/models/UnitBarang.php';
require_once dirname(__DIR__) . '/models/Barang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';

class UnitController extends Controller {

    public function index() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $barangId = isset($_GET['barang_id']) ? (int)$_GET['barang_id'] : null;
        $unitModel = new UnitBarang();
        $barangModel = new Barang();

        if ($barangId) {
            $units = $unitModel->getUnitsByBarang($barangId);
            $barang = $barangModel->find($barangId);
        } else {
            $units = $unitModel->getAllWithBarang();
            $barang = null;
        }

        $this->view('unit/index', [
            'title' => 'Kelola Unit Barang',
            'units' => $units,
            'barang' => $barang,
            'barangId' => $barangId
        ]);
    }

    public function add() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $barangId = (int)$this->input('barang_id');
        $barangModel = new Barang();
        $barang = $barangModel->find($barangId);

        if (!$barang) {
            $this->redirect('/index.php?controller=barang&action=index');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $mode = $this->input('mode', 'single');
                $kondisiAwal = $this->input('kondisi_awal', 'Baik');
                $tanggalPengadaan = $this->input('tanggal_pengadaan');

                if (empty($tanggalPengadaan)) {
                    throw new Exception("Tanggal pengadaan wajib diisi.");
                }

                // Determine default status_ketersediaan based on kondisiAwal (BR-01)
                $statusAwal = 'Tersedia';
                if ($kondisiAwal === 'Rusak Berat') {
                    $statusAwal = 'Perbaikan';
                }

                $unitModel = new UnitBarang();

                if ($mode === 'single') {
                    $kodeUnit = trim($this->input('kode_unit'));
                    if (empty($kodeUnit)) {
                        throw new Exception("Kode unit wajib diisi.");
                    }

                    // Check uniqueness per barang (Validation Rules FR-07)
                    $db = Database::getInstance()->getConnection();
                    $sqlCheck = "SELECT COUNT(*) FROM unit_barang WHERE barang_id = :barang_id AND kode_unit = :kode_unit";
                    $stmtCheck = $db->prepare($sqlCheck);
                    $stmtCheck->execute(['barang_id' => $barangId, 'kode_unit' => $kodeUnit]);
                    
                    if ($stmtCheck->fetchColumn() > 0) {
                        throw new Exception("Duplikasi Kode Unit! Kode " . $kodeUnit . " sudah terdaftar untuk barang ini. (TC-07b)");
                    }

                    $unitModel->insert([
                        'barang_id' => $barangId,
                        'kode_unit' => $kodeUnit,
                        'kondisi' => $kondisiAwal,
                        'status_ketersediaan' => $statusAwal,
                        'tanggal_pengadaan' => $tanggalPengadaan
                    ]);

                    AuditLog::log("Tambah Unit Barang: " . $kodeUnit . " (" . $barang['nama_barang'] . ")", 'unit_barang');

                } else {
                    // Bulk mode
                    $jumlahUnit = (int)$this->input('jumlah_unit');
                    $prefixKode = trim($this->input('prefix_kode'));

                    if ($jumlahUnit < 1 || $jumlahUnit > 500) {
                        throw new Exception("Jumlah unit massal harus antara 1 sampai 500 per operasi.");
                    }
                    if (empty($prefixKode)) {
                        throw new Exception("Prefix kode unit wajib diisi.");
                    }

                    $db = Database::getInstance()->getConnection();
                    
                    // Pre-fetch existing kode_units to check in-memory (faster)
                    $sqlExists = "SELECT kode_unit FROM unit_barang WHERE barang_id = :barang_id";
                    $stmtExists = $db->prepare($sqlExists);
                    $stmtExists->execute(['barang_id' => $barangId]);
                    $existingCodes = $stmtExists->fetchAll(PDO::FETCH_COLUMN);

                    $insertedCount = 0;
                    $sequence = 1;

                    // Bulk insert logic: skip colliding keys (Exception Handling FR-07)
                    while ($insertedCount < $jumlahUnit) {
                        $generatedCode = $prefixKode . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
                        
                        if (in_array($generatedCode, $existingCodes)) {
                            // Collision detected, skip this sequence number
                            $sequence++;
                            continue;
                        }

                        // Perform insert
                        $unitModel->insert([
                            'barang_id' => $barangId,
                            'kode_unit' => $generatedCode,
                            'kondisi' => $kondisiAwal,
                            'status_ketersediaan' => $statusAwal,
                            'tanggal_pengadaan' => $tanggalPengadaan
                        ]);

                        $insertedCount++;
                        $sequence++;
                    }

                    AuditLog::log("Tambah " . $insertedCount . " Unit Massal untuk " . $barang['nama_barang'], 'unit_barang');
                }

                $this->redirect('/index.php?controller=unit&action=index&barang_id=' . $barangId . '&success=1');

            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('unit/add', [
            'title' => 'Tambah Unit Barang',
            'barang' => $barang,
            'barangId' => $barangId,
            'errors' => $errors
        ]);
    }

    /**
     * Update conditions and statuses (FR-08 / BR-01)
     */
    public function updateKondisi() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);
        $this->validatePostRequest();

        $unitId = (int)$this->input('id');
        $kondisiBaru = $this->input('kondisi');
        $statusBaru = $this->input('status_ketersediaan');
        $barangId = $this->input('barang_id');

        try {
            $unitModel = new UnitBarang();
            // Single source of truth update logic
            $unitModel->updateKondisiDanStatus($unitId, $kondisiBaru, $statusBaru);

            $this->redirect('/index.php?controller=unit&action=index' . ($barangId ? '&barang_id=' . $barangId : '') . '&success=2');
        } catch (Exception $e) {
            // Render error page or pass to view. For simplicity redirect with error banner
            $this->redirect('/index.php?controller=unit&action=index' . ($barangId ? '&barang_id=' . $barangId : '') . '&error=' . urlencode($e->getMessage()));
        }
    }

    public function delete() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);
        $this->validatePostRequest();

        $id = (int)$this->input('id');
        $barangId = $this->input('barang_id');
        
        $unitModel = new UnitBarang();
        $unit = $unitModel->find($id);

        if ($unit) {
            $before = $unitModel->find($id);
            $unitModel->delete($id);
            AuditLog::log("Hapus Unit Barang: " . $unit['kode_unit'], 'unit_barang', $before, null);
        }

        $this->redirect('/index.php?controller=unit&action=index' . ($barangId ? '&barang_id=' . $barangId : ''));
    }
}
