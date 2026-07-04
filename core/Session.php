<?php
/**
 * Session and CSRF Security Manager
 * Core File
 */

require_once dirname(__DIR__) . '/config/config.php';

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::checkTimeout();
        self::generateCsrfToken();
    }

    /**
     * Enforce Session Timeout (BR-06)
     */
    private static function checkTimeout() {
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
                // Sesi kedaluwarsa, destroy dan redirect
                self::destroy();
                header("Location: index.php?controller=auth&action=login&timeout=1");
                exit();
            }
            $_SESSION['last_activity'] = time();
        }
    }

    /**
     * Generate CSRF Token per session if not exists
     */
    public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Get active CSRF Token
     */
    public static function getCsrfToken() {
        return $_SESSION['csrf_token'] ?? self::generateCsrfToken();
    }

    /**
     * Validate CSRF Token on POST requests
     */
    public static function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Destroy current session (Logout)
     */
    public static function destroy() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
