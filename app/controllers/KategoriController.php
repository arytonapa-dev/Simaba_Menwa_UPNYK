<?php
/**
 * Kategori Controller
 * Kelola Kategori Barang (Admin) - FR-04
 */

require_once dirname(__DIR__) . '/models/KategoriBarang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';

class KategoriController extends Controller {

    public function index() {
        Auth::restrict([ROLE_ADMIN]);
        
        $model = new KategoriBarang();
        $categories = $model->findAll();

        $this->view('kategori/index', [
            'title' => 'Kelola Kategori Barang',
            'categories' => $categories
        ]);
    }

    public function add() {
        Auth::restrict([ROLE_ADMIN]);

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;
                $rules = [
                    'nama_kategori' => 'required|min:3|max:100|unique:kategori_barang,nama_kategori',
                    'deskripsi' => 'max:255'
                ];

                if ($validator->validate($postData, $rules)) {
                    $model = new KategoriBarang();
                    $isCritical = isset($postData['is_critical']) ? 1 : 0;
                    
                    $model->insert([
                        'nama_kategori' => trim($postData['nama_kategori']),
                        'deskripsi' => trim($postData['deskripsi']),
                        'is_critical' => $isCritical
                    ]);

                    AuditLog::log("Tambah Kategori Barang: " . $postData['nama_kategori'], 'kategori_barang');
                    $this->redirect('/index.php?controller=kategori&action=index&success=1');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('kategori/add', [
            'title' => 'Tambah Kategori Barang',
            'errors' => $errors
        ]);
    }

    public function edit() {
        Auth::restrict([ROLE_ADMIN]);

        $id = (int)$this->input('id');
        $model = new KategoriBarang();
        $category = $model->find($id);

        if (!$category) {
            $this->redirect('/index.php?controller=kategori&action=index');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;
                $rules = [
                    'nama_kategori' => "required|min:3|max:100|unique:kategori_barang,nama_kategori,kategori_id,{$id}",
                    'deskripsi' => 'max:255'
                ];

                if ($validator->validate($postData, $rules)) {
                    $before = $model->find($id);
                    $isCritical = isset($postData['is_critical']) ? 1 : 0;

                    $model->update($id, [
                        'nama_kategori' => trim($postData['nama_kategori']),
                        'deskripsi' => trim($postData['deskripsi']),
                        'is_critical' => $isCritical
                    ]);

                    $after = $model->find($id);
                    AuditLog::log("Ubah Kategori Barang: " . $after['nama_kategori'], 'kategori_barang', $before, $after);
                    
                    $this->redirect('/index.php?controller=kategori&action=index&success=2');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('kategori/edit', [
            'title' => 'Ubah Kategori Barang',
            'category' => $category,
            'errors' => $errors
        ]);
    }

    public function delete() {
        Auth::restrict([ROLE_ADMIN]);
        $this->validatePostRequest();

        $id = (int)$this->input('id');
        $model = new KategoriBarang();
        $category = $model->find($id);

        if ($category) {
            // Check relationship constraint (Exception Handling FR-04 / UC-04 / TC-04)
            if ($model->hasActiveItems($id)) {
                $this->redirect('/index.php?controller=kategori&action=index&error=has_items');
            }

            $before = $model->find($id);
            $model->delete($id);
            AuditLog::log("Hapus Kategori Barang: " . $category['nama_kategori'], 'kategori_barang', $before, null);
        }

        $this->redirect('/index.php?controller=kategori&action=index');
    }
}
