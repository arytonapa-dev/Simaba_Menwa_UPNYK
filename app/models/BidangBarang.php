<?php
/**
 * Bidang Barang Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class BidangBarang extends Model {
    protected $table = 'bidang_barang';
    protected $primaryKey = 'bidang_id';

    /**
     * Check if section contains active items
     */
    public function hasActiveItems($id) {
        $sql = "SELECT COUNT(*) FROM barang WHERE bidang_id = :id";
        $stmt = $this->query($sql, ['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
