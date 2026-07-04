<?php
/**
 * Notifikasi Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class Notifikasi extends Model {
    protected $table = 'notifikasi';
    protected $primaryKey = 'notifikasi_id';

    /**
     * Get active notifications for a specific user (row-level security - FR-22)
     */
    public function getByRecipient($recipientId) {
        $sql = "SELECT * FROM notifikasi 
                WHERE recipient_id = :recipient_id 
                ORDER BY created_at DESC";
        return $this->query($sql, ['recipient_id' => $recipientId])->fetchAll();
    }

    /**
     * Get count of unread notifications for a user
     */
    public function getUnreadCount($recipientId) {
        $sql = "SELECT COUNT(*) FROM notifikasi 
                WHERE recipient_id = :recipient_id AND is_read = 0";
        $stmt = $this->query($sql, ['recipient_id' => $recipientId]);
        return $stmt->fetchColumn();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($recipientId) {
        $sql = "UPDATE notifikasi SET is_read = 1 WHERE recipient_id = :recipient_id";
        return $this->query($sql, ['recipient_id' => $recipientId]);
    }
}
