<?php
/**
 * Auth Controller
 * Autentikasi Pengguna (Login/Logout) - FR-01, FR-02
 */

require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';
require_once dirname(dirname(__DIR__)) . '/core/Session.php';

class AuthController extends Controller {

    /**
     * Handle Login Action (FR-01)
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            $this->redirect('/index.php?controller=dashboard&action=index');
        }

        $error = null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($ip === '::1') $ip = '127.0.0.1';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Verify CSRF
                $this->validatePostRequest();

                $username = trim($this->input('username'));
                $password = $this->input('password');

                // 1. Enforce Safety Lockout check (BR-05)
                if (Auth::isLockedOut($username, $ip)) {
                    throw new Exception("Akun terkunci sementara. Terlalu banyak percobaan login gagal. Silakan tunggu 15 menit.");
                }

                // 2. Validate input fields
                if (empty($username) || empty($password)) {
                    throw new Exception("Username/NIM dan Password wajib diisi.");
                }

                // 3. Find user
                $userModel = new User();
                $user = $userModel->findByCredentials($username);

                // 4. Verification
                if ($user) {
                    // Check if account is active (BR-07)
                    if ($user['is_active'] == 0) {
                        AuditLog::log("Login Gagal (Akun dinonaktifkan: " . $username . ")", 'auth');
                        throw new Exception("Akun Anda tidak aktif. Hubungi Admin.");
                    }

                    // Verify password hash (FR-01)
                    if (password_verify($password, $user['password_hash'])) {
                        // Success! Save Session
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['role_id'] = $user['role_id'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['nim_nip'] = $user['nim_nip'];
                        $_SESSION['photo'] = $user['photo'];
                        $_SESSION['last_activity'] = time();

                        // Log success
                        AuditLog::log("Login Berhasil (User: " . $user['username'] . ")", 'auth');

                        $this->redirect('/index.php?controller=dashboard&action=index');
                    }
                }

                // Failed credentials
                AuditLog::log("Login Gagal (Username: " . $username . ")", 'auth');
                throw new Exception("Username/NIM atau Password salah.");

            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        // Render Login Page
        $this->view('auth/login', [
            'title' => 'Masuk',
            'error' => $error
        ], 'auth');
    }

    /**
     * Handle Logout Action (FR-02)
     */
    public function logout() {
        if (Auth::check()) {
            $user = Auth::user();
            AuditLog::log("Logout Berhasil (User: " . $user['username'] . ")", 'auth');
        }
        
        Session::destroy();
        $this->redirect('/index.php?controller=auth&action=login');
    }
}
