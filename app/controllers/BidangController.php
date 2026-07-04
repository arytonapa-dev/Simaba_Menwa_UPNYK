<?php
/**
 * Bidang Controller
 * Kelola Bidang Barang (Admin) - FR-05
 */

require_once dirname(__DIR__) . '/models/BidangBarang.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';

class BidangController extends Controller {

    public function index() {
        Auth::restrict([ROLE_ADMIN]);
        
        $model = new BidangBarang();
        $sections = $model->findAll();

        $this->view('bidang/index', [
            'title' => 'Kelola Bidang Barang',
            'sections' => $sections
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
                    'nama_bidang' => 'required|min:3|max:100|unique:bidang_barang,nama_bidang',
                    'penanggung_jawab' => 'required|min:3|max:100',
                    'deskripsi' => 'max:255'
                ];

                if ($validator->validate($postData, $rules)) {
                    $model = new BidangBarang();
                    
                    $model->insert([
                        'nama_bidang' => trim($postData['nama_bidang']),
                        'penanggung_jawab' => trim($postData['penanggung_jawab']),
                        'deskripsi' => trim($postData['deskripsi'])
                    ]);

                    AuditLog::log("Tambah Bidang Barang: " . $postData['nama_bidang'], 'bidang_barang');
                    $this->redirect('/index.php?controller=bidang&action=index&success=1');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('bidang/add', [
            'title' => 'Tambah Bidang Barang',
            'errors' => $errors
        ]);
    }

    public function edit() {
        Auth::restrict([ROLE_ADMIN]);

        $id = (int)$this->input('id');
        $model = new BidangBarang();
        $section = $model->find($id);

        if (!$section) {
            $this->redirect('/index.php?controller=bidang&action=index');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;
                $rules = [
                    'nama_bidang' => "required|min:3|max:100|unique:bidang_barang,nama_bidang,bidang_id,{$id}",
                    'penanggung_jawab' => 'required|min:3|max:100',
                    'deskripsi' => 'max:255'
                ];

                if ($validator->validate($postData, $rules)) {
                    $before = $model->find($id);

                    $model->update($id, [
                        'nama_bidang' => trim($postData['nama_bidang']),
                        'penanggung_jawab' => trim($postData['penanggung_jawab']),
                        'deskripsi' => trim($postData['deskripsi'])
                    ]);

                    $after = $model->find($id);
                    AuditLog::log("Ubah Bidang Barang: " . $after['nama_bidang'], 'bidang_barang', $before, $after);
                    
                    $this->redirect('/index.php?controller=bidang&action=index&success=2');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('bidang/edit', [
            'title' => 'Ubah Bidang Barang',
            'section' => $section,
            'errors' => $errors
        ]);
    }

    public function delete() {
        Auth::restrict([ROLE_ADMIN]);
        $this->validatePostRequest();

        $id = (int)$this->input('id');
        $model = new BidangBarang();
        $section = $model->find($id);

        if ($section) {
            // Check relationship constraint (Exception Handling FR-05 / UC-05 / TC-05)
            if ($model->hasActiveItems($id)) {
                $this->redirect('/index.php?controller=bidang&action=index&error=has_items');
            }

            $before = $model->find($id);
            $model->delete($id);
            AuditLog::log("Hapus Bidang Barang: " . $section['nama_bidang'], 'bidang_barang', $before, null);
        }

        $this->redirect('/index.php?controller=bidang&action=index');
    }
}
