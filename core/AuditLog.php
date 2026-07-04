<?php
/**
 * Immutable Audit Trail Logger
 * Core File
 */

require_once __DIR__ . '/Database.php';

class AuditLog {
    /**
     * Add log entry to database
     */
    public static function log($activity, $module, $before = null, $after = null) {
        try {
            $db = Database::getInstance()->getConnection();
            $userId = $_SESSION['user_id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            if ($ip === '::1') {
                $ip = '127.0.0.1';
            }

            $sql = "INSERT INTO audit_log (user_id, aktivitas, modul, data_sebelum, data_sesudah, ip_address, created_at) 
                    VALUES (:user_id, :aktivitas, :modul, :data_sebelum, :data_sesudah, :ip_address, NOW())";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'aktivitas' => $activity,
                'modul' => $module,
                'data_sebelum' => $before ? json_encode($before) : null,
                'data_sesudah' => $after ? json_encode($after) : null,
                'ip_address' => $ip
            ]);
        } catch (Exception $e) {
            // Silently log database issues to php log, do not crash main process
            error_log("Failed to write audit log: " . $e->getMessage());
        }
    }
}
