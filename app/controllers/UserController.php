<?php
/**
 * User Controller
 * Kelola Pengguna (Admin) & Kelola Profil (Semua Peran) - FR-03, FR-23
 */

require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Role.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Validator.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';

class UserController extends Controller {

    /**
     * Display list of users (Admin only - FR-03)
     */
    public function index() {
        Auth::restrict([ROLE_ADMIN]);

        $userModel = new User();
        $users = $userModel->getAllWithRoles();

        $this->view('users/index', [
            'title' => 'Kelola Akun Pengguna',
            'users' => $users
        ]);
    }

    /**
     * Add new user (Admin only - FR-03)
     */
    public function add() {
        Auth::restrict([ROLE_ADMIN]);

        $errors = [];
        $roleModel = new Role();
        $roles = $roleModel->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;

                $rules = [
                    'full_name' => 'required|min:3|max:100',
                    'nim_nip' => 'required|unique:users,nim_nip',
                    'username' => 'required|alphanumeric|min:5|max:50|unique:users,username',
                    'role_id' => 'required|numeric',
                    'password' => 'required|min:8'
                ];

                if ($validator->validate($postData, $rules)) {
                    $userModel = new User();
                    
                    // Hash using bcrypt cost 10
                    $passwordHash = password_hash($postData['password'], PASSWORD_BCRYPT, ['cost' => 10]);

                    $userModel->insert([
                        'role_id' => (int)$postData['role_id'],
                        'full_name' => trim($postData['full_name']),
                        'nim_nip' => trim($postData['nim_nip']),
                        'username' => trim($postData['username']),
                        'password_hash' => $passwordHash,
                        'email' => trim($postData['email'] ?? ''),
                        'phone' => trim($postData['phone'] ?? ''),
                        'is_active' => 1
                    ]);

                    AuditLog::log("Tambah Pengguna Baru: " . $postData['username'], 'users', null, ['username' => $postData['username']]);
                    $this->redirect('/index.php?controller=user&action=index&success=1');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('users/add', [
            'title' => 'Tambah Pengguna Baru',
            'roles' => $roles,
            'errors' => $errors
        ]);
    }

    /**
     * Edit user details (Admin only - FR-03)
     */
    public function edit() {
        Auth::restrict([ROLE_ADMIN]);

        $id = (int)$this->input('id');
        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            $this->redirect('/index.php?controller=user&action=index');
        }

        $errors = [];
        $roleModel = new Role();
        $roles = $roleModel->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $validator = new Validator();
                $postData = $_POST;

                $rules = [
                    'full_name' => 'required|min:3|max:100',
                    'nim_nip' => "required|unique:users,nim_nip,user_id,{$id}",
                    'username' => "required|alphanumeric|min:5|max:50|unique:users,username,user_id,{$id}",
                    'role_id' => 'required|numeric'
                ];

                if ($validator->validate($postData, $rules)) {
                    $before = $userModel->find($id);

                    $updateData = [
                        'role_id' => (int)$postData['role_id'],
                        'full_name' => trim($postData['full_name']),
                        'nim_nip' => trim($postData['nim_nip']),
                        'username' => trim($postData['username']),
                        'email' => trim($postData['email'] ?? ''),
                        'phone' => trim($postData['phone'] ?? '')
                    ];

                    // Optional password reset
                    if (!empty($postData['password'])) {
                        if (strlen($postData['password']) < 8) {
                            throw new Exception("Kata sandi baru minimal 8 karakter.");
                        }
                        $updateData['password_hash'] = password_hash($postData['password'], PASSWORD_BCRYPT, ['cost' => 10]);
                    }

                    $userModel->update($id, $updateData);
                    $after = $userModel->find($id);

                    AuditLog::log("Ubah Detail Pengguna: " . $after['username'], 'users', $before, $after);
                    $this->redirect('/index.php?controller=user&action=index&success=2');
                } else {
                    $errors = $validator->getErrors();
                }
            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('users/edit', [
            'title' => 'Ubah Pengguna',
            'user' => $user,
            'roles' => $roles,
            'errors' => $errors
        ]);
    }

    /**
     * Soft delete/deactivate user (Admin only - FR-03 / BR-07)
     */
    public function toggleActive() {
        Auth::restrict([ROLE_ADMIN]);
        $this->validatePostRequest();

        $id = (int)$this->input('id');
        $userModel = new User();
        $user = $userModel->find($id);

        if ($user) {
            // Prevent Admin from deactivating their own account (Exception Handling FR-03)
            if ($id === (int)$_SESSION['user_id']) {
                $this->redirect('/index.php?controller=user&action=index&error=self_deactivate');
            }

            $before = $userModel->find($id);
            $newStatus = $user['is_active'] == 1 ? 0 : 1;
            
            $userModel->update($id, ['is_active' => $newStatus]);
            $after = $userModel->find($id);

            $activity = $newStatus == 0 ? "Nonaktifkan Pengguna" : "Aktifkan Pengguna";
            AuditLog::log($activity . ": " . $user['username'], 'users', $before, $after);
        }

        $this->redirect('/index.php?controller=user&action=index');
    }

    /**
     * Profil Saya (All roles - FR-23)
     */
    public function profil() {
        // Must be logged in
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);

        $userModel = new User();
        $userId = $_SESSION['user_id'];
        $user = $userModel->find($userId);

        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validatePostRequest();

                $fullName = trim($this->input('full_name'));
                $email = trim($this->input('email'));
                $phone = trim($this->input('phone'));

                // Validate profile inputs
                if (empty($fullName)) {
                    throw new Exception("Nama lengkap wajib diisi.");
                }
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Format email tidak valid.");
                }

                $before = $userModel->find($userId);
                $updateData = [
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone' => $phone
                ];

                // Handle Photo Upload (max 2MB, JPG/PNG - FR-23b)
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $file = $_FILES['photo'];
                    
                    if ($file['size'] > 2097152) { // 2MB
                        throw new Exception("Ukuran foto maksimal 2MB.");
                    }

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        throw new Exception("Format foto harus JPG atau PNG.");
                    }

                    // Create file name and move it
                    $newFileName = 'profil_' . $userId . '_' . time() . '.' . $ext;
                    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/profil/';
                    
                    // Create directory if not exists
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Delete old photo if exists
                    if (!empty($user['photo'])) {
                        $oldPhoto = $uploadDir . $user['photo'];
                        if (file_exists($oldPhoto)) {
                            unlink($oldPhoto);
                        }
                    }

                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                        $updateData['photo'] = $newFileName;
                        $_SESSION['photo'] = $newFileName; // update session
                    } else {
                        throw new Exception("Gagal mengunggah foto.");
                    }
                }

                // Save updates
                $userModel->update($userId, $updateData);
                $after = $userModel->find($userId);
                
                // Update session variables
                $_SESSION['full_name'] = $fullName;

                AuditLog::log("Update Profil Pengguna: " . $_SESSION['username'], 'users', $before, $after);
                $success = "Profil berhasil diperbarui.";
                
                // Refresh local model data
                $user = $after;

            } catch (Exception $e) {
                $errors['general'] = $e->getMessage();
            }
        }

        $this->view('users/profil', [
            'title' => 'Profil Saya',
            'user' => $user,
            'success' => $success,
            'errors' => $errors
        ]);
    }

    /**
     * Change password self (All roles - FR-23)
     */
    public function ubahPassword() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);
        $this->validatePostRequest();

        $userId = $_SESSION['user_id'];
        $userModel = new User();
        $user = $userModel->find($userId);

        $passwordLama = $_POST['password_lama'] ?? '';
        $passwordBaru = $_POST['password_baru'] ?? '';
        $konfirmasiBaru = $_POST['konfirmasi_baru'] ?? '';

        try {
            if (empty($passwordLama) || empty($passwordBaru) || empty($konfirmasiBaru)) {
                throw new Exception("Seluruh kolom kata sandi wajib diisi.");
            }

            // Verify current password (FR-23b / TC-23)
            if (!password_verify($passwordLama, $user['password_hash'])) {
                throw new Exception("Kata sandi lama yang Anda masukkan salah.");
            }

            if (strlen($passwordBaru) < 8) {
                throw new Exception("Kata sandi baru minimal 8 karakter.");
            }

            if ($passwordBaru !== $konfirmasiBaru) {
                throw new Exception("Konfirmasi kata sandi baru tidak sesuai.");
            }

            $before = $userModel->find($userId);
            $newHash = password_hash($passwordBaru, PASSWORD_BCRYPT, ['cost' => 10]);
            
            $userModel->update($userId, ['password_hash' => $newHash]);
            $after = $userModel->find($userId);

            // Audit log without password payload (Security Requirement FR-23b)
            AuditLog::log("Ubah Password Mandiri oleh User: " . $user['username'], 'users');

            $this->json(['status' => 'success', 'message' => 'Kata sandi berhasil diperbarui.']);

        } catch (Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
