<?php
/**
 * User Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * Find user by username or NIM/NBP
     */
    public function findByCredentials($usernameOrNim) {
        $sql = "SELECT u.*, r.role_name 
                FROM users u
                JOIN roles r ON u.role_id = r.role_id 
                WHERE u.username = :login1 OR u.nim_nip = :login2 
                LIMIT 1";
        return $this->query($sql, ['login1' => $usernameOrNim, 'login2' => $usernameOrNim])->fetch();
    }

    /**
     * Get all users with their role description
     */
    public function getAllWithRoles() {
        $sql = "SELECT u.*, r.role_name 
                FROM users u
                JOIN roles r ON u.role_id = r.role_id
                ORDER BY u.created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Deactivate user (Soft Delete - BR-07)
     */
    public function deactivate($id) {
        return $this->update($id, ['is_active' => 0]);
    }

    /**
     * Activate user
     */
    public function activate($id) {
        return $this->update($id, ['is_active' => 1]);
    }
}
