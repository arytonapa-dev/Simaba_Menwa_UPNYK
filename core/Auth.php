<?php
/**
 * Authentication and RBAC Helper
 * Core File
 */

require_once __DIR__ . '/Database.php';

class Auth {
    /**
     * Check if a user is logged in
     */
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get logged-in user details
     */
    public static function user() {
        if (!self::check()) return null;
        return [
            'id' => $_SESSION['user_id'],
            'role_id' => $_SESSION['role_id'],
            'full_name' => $_SESSION['full_name'],
            'username' => $_SESSION['username'],
            'nim_nip' => $_SESSION['nim_nip'],
            'photo' => $_SESSION['photo'] ?? null
        ];
    }

    /**
     * Enforce role restriction (RBAC)
     */
    public static function restrict($allowedRoles = []) {
        if (!self::check()) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $userRole = $_SESSION['role_id'];
        if (!in_array($userRole, $allowedRoles)) {
            // Forbidden access
            http_response_code(403);
            require_once dirname(__DIR__) . '/app/views/layouts/403.php';
            exit();
        }
    }

    /**
     * Check lock out status for a user/IP (BR-05)
     */
    public static function isLockedOut($username, $ip) {
        $db = Database::getInstance()->getConnection();
        $fifteenMinsAgo = date('Y-m-d H:i:s', time() - LOCKOUT_TIME);
        
        // Count fail logs in the database in last 15 minutes
        $sql = "SELECT COUNT(*) FROM audit_log 
                WHERE (ip_address = :ip OR aktivitas LIKE :user_pattern)
                AND aktivitas LIKE 'Login Gagal%' 
                AND created_at >= :time_limit";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'ip' => $ip,
            'user_pattern' => '%' . $username . '%',
            'time_limit' => $fifteenMinsAgo
        ]);
        
        return $stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS;
    }
}
