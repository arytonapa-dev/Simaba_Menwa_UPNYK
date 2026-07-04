<?php
/**
 * Barang Controller
 * Kelola Data Barang Master (Operator) - FR-06
 */

require_once dirname(__DIR__) . '/models/Barang.php';
require_once dirname(__DIR__) . '/models/KategoriBarang.php';
require_once dirname(__DIR__) . '/models/BidangBarang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';

class BarangController extends Controller {

    public function index() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);
        
        $model = new Barang();
        $items = $model->getAllWithAggregates();

        $this->view('barang/index', [
            'title' => 'Daftar Master Barang',
            'items' => $items
        ]);
    }

    public function add() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $errors = [];
        
        $catModel = new KategoriBarang();
        $categories = $catModel->findAll();

        $bidModel = new BidangBarang();
        $sections = $bidModel->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;
                $rules = [
                    'nama_barang' => 'required|min:3|max:150',
                    'kategori_id' => 'required|numeric',
                    'bidang_id' => 'required|numeric',
                    'satuan' => 'required|max:20',
                    'deskripsi' => 'max:500'
                ];

                if ($validator->validate($postData, $rules)) {
                    $barangModel = new Barang();
                    $photoName = null;

                    // Handle Photo Upload (max 2MB, JPG/PNG - FR-06)
                    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $file = $_FILES['foto'];
                        
                        if ($file['size'] > 2097152) { // 2MB
                            throw new Exception("Ukuran foto barang maksimal 2MB. (TC-06)");
                        }

                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                            throw new Exception("Format foto barang harus JPG atau PNG.");
                        }

                        $photoName = 'barang_' . time() . '_' . rand(100, 999) . '.' . $ext;
                        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/barang/';
                        
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $photoName)) {
                            throw new Exception("Gagal mengunggah foto barang.");
                        }
                    }

                    $barangModel->insert([
                        'kategori_id' => (int)$postData['kategori_id'],
                        'bidang_id' => (int)$postData['bidang_id'],
                        'nama_barang' => trim($postData['nama_barang']),
                        'satuan' => trim($postData['satuan']),
                        'deskripsi' => trim($postData['deskripsi']),
                        'foto' => $photoName
                    ]);

                    AuditLog::log("Tambah Barang Master: " . $postData['nama_barang'], 'barang');
                    $this->redirect('/index.php?controller=barang&action=index&success=1');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('barang/add', [
            'title' => 'Tambah Barang Master',
            'categories' => $categories,
            'sections' => $sections,
            'errors' => $errors
        ]);
    }

    public function edit() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);

        $id = (int)$this->input('id');
        $barangModel = new Barang();
        $barang = $barangModel->find($id);

        if (!$barang) {
            $this->redirect('/index.php?controller=barang&action=index');
        }

        $errors = [];
        $catModel = new KategoriBarang();
        $categories = $catModel->findAll();

        $bidModel = new BidangBarang();
        $sections = $bidModel->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;
                $rules = [
                    'nama_barang' => 'required|min:3|max:150',
                    'kategori_id' => 'required|numeric',
                    'bidang_id' => 'required|numeric',
                    'satuan' => 'required|max:20',
                    'deskripsi' => 'max:500'
                ];

                if ($validator->validate($postData, $rules)) {
                    $before = $barangModel->find($id);
                    $photoName = $barang['foto'];

                    // Handle Photo Upload (max 2MB, JPG/PNG - FR-06)
                    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $file = $_FILES['foto'];
                        
                        if ($file['size'] > 2097152) { // 2MB
                            throw new Exception("Ukuran foto barang maksimal 2MB. (TC-06)");
                        }

                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                            throw new Exception("Format foto barang harus JPG atau PNG.");
                        }

                        $newPhotoName = 'barang_' . time() . '_' . rand(100, 999) . '.' . $ext;
                        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/barang/';
                        
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        // Delete old photo if exists
                        if (!empty($barang['foto'])) {
                            $oldPhoto = $uploadDir . $barang['foto'];
                            if (file_exists($oldPhoto)) {
                                unlink($oldPhoto);
                            }
                        }

                        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newPhotoName)) {
                            $photoName = $newPhotoName;
                        } else {
                            throw new Exception("Gagal mengunggah foto barang.");
                        }
                    }

                    $barangModel->update($id, [
                        'kategori_id' => (int)$postData['kategori_id'],
                        'bidang_id' => (int)$postData['bidang_id'],
                        'nama_barang' => trim($postData['nama_barang']),
                        'satuan' => trim($postData['satuan']),
                        'deskripsi' => trim($postData['deskripsi']),
                        'foto' => $photoName,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    $after = $barangModel->find($id);
                    AuditLog::log("Ubah Barang Master: " . $after['nama_barang'], 'barang', $before, $after);
                    
                    $this->redirect('/index.php?controller=barang&action=index&success=2');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('barang/edit', [
            'title' => 'Ubah Barang Master',
            'barang' => $barang,
            'categories' => $categories,
            'sections' => $sections,
            'errors' => $errors
        ]);
    }

    public function delete() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR]);
        $this->validatePostRequest();

        $id = (int)$this->input('id');
        $barangModel = new Barang();
        $barang = $barangModel->find($id);

        if ($barang) {
            // Check relationship constraint (Exception Handling FR-06 / BR-06 / TC-06)
            if ($barangModel->hasUnits($id)) {
                $this->redirect('/index.php?controller=barang&action=index&error=has_units');
            }

            $before = $barangModel->find($id);
            
            // Delete associated photo if exists
            if (!empty($barang['foto'])) {
                $photoPath = dirname(dirname(__DIR__)) . '/public/uploads/barang/' . $barang['foto'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $barangModel->delete($id);
            AuditLog::log("Hapus Barang Master: " . $barang['nama_barang'], 'barang', $before, null);
        }

        $this->redirect('/index.php?controller=barang&action=index');
    }
}
