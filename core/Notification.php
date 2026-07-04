<?php
/**
 * In-App Notification Engine
 * Core File
 */

require_once __DIR__ . '/Database.php';

class Notification {
    /**
     * Send notification to a specific user
     */
    public static function send($recipientId, $type, $title, $message, $link = null) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "INSERT INTO notifikasi (recipient_id, jenis, judul, pesan, link_terkait, is_read, created_at) 
                    VALUES (:recipient_id, :jenis, :judul, :pesan, :link_terkait, 0, NOW())";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'recipient_id' => $recipientId,
                'jenis' => $type,
                'judul' => $title,
                'pesan' => $message,
                'link_terkait' => $link
            ]);
        } catch (Exception $e) {
            error_log("Failed to send notification: " . $e->getMessage());
        }
    }

    /**
     * Send notification to all users of a specific role
     */
    public static function sendToRole($roleId, $type, $title, $message, $link = null) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Find all active users with this role
            $sql = "SELECT user_id FROM users WHERE role_id = :role_id AND is_active = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute(['role_id' => $roleId]);
            $users = $stmt->fetchAll();

            foreach ($users as $user) {
                self::send($user['user_id'], $type, $title, $message, $link);
            }
        } catch (Exception $e) {
            error_log("Failed to send notification to role: " . $e->getMessage());
        }
    }
}
