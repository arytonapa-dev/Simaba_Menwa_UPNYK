<?php
/**
 * Audit Log Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class AuditLogModel extends Model {
    protected $table = 'audit_log';
    protected $primaryKey = 'log_id';

    /**
     * Get logs with optional filters, sorting, and pagination (FR-20)
     */
    public function getLogsWithFilters($userFilter = null, $activityFilter = null, $moduleFilter = null, $startDate = null, $endDate = null, $limit = 20, $offset = 0, $orderBy = 'l.created_at DESC') {
        $sql = "SELECT l.*, u.username, u.full_name 
                FROM audit_log l
                LEFT JOIN users u ON l.user_id = u.user_id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($userFilter)) {
            $sql .= " AND (u.username LIKE :user OR u.full_name LIKE :user)";
            $params['user'] = '%' . $userFilter . '%';
        }

        if (!empty($activityFilter)) {
            $sql .= " AND l.aktivitas LIKE :activity";
            $params['activity'] = '%' . $activityFilter . '%';
        }

        if (!empty($moduleFilter)) {
            $sql .= " AND l.modul = :module";
            $params['module'] = $moduleFilter;
        }

        if (!empty($startDate)) {
            $sql .= " AND l.created_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }

        if (!empty($endDate)) {
            $sql .= " AND l.created_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        // Validate sorting parameter to prevent SQL Injection
        $allowedSorts = [
            'l.created_at DESC', 'l.created_at ASC', 
            'u.username ASC', 'u.username DESC', 
            'l.modul ASC', 'l.modul DESC', 
            'l.ip_address ASC', 'l.ip_address DESC'
        ];
        if (!in_array($orderBy, $allowedSorts)) {
            $orderBy = 'l.created_at DESC';
        }

        $sql .= " ORDER BY " . $orderBy . " LIMIT :limit OFFSET :offset";
        
        $db = $this->db;
        $stmt = $db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get total count of logs matching current filters (FR-20)
     */
    public function getLogsCount($userFilter = null, $activityFilter = null, $moduleFilter = null, $startDate = null, $endDate = null) {
        $sql = "SELECT COUNT(*) 
                FROM audit_log l
                LEFT JOIN users u ON l.user_id = u.user_id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($userFilter)) {
            $sql .= " AND (u.username LIKE :user OR u.full_name LIKE :user)";
            $params['user'] = '%' . $userFilter . '%';
        }

        if (!empty($activityFilter)) {
            $sql .= " AND l.aktivitas LIKE :activity";
            $params['activity'] = '%' . $activityFilter . '%';
        }

        if (!empty($moduleFilter)) {
            $sql .= " AND l.modul = :module";
            $params['module'] = $moduleFilter;
        }

        if (!empty($startDate)) {
            $sql .= " AND l.created_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }

        if (!empty($endDate)) {
            $sql .= " AND l.created_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
