<?php
/**
 * Kategori Barang Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class KategoriBarang extends Model {
    protected $table = 'kategori_barang';
    protected $primaryKey = 'kategori_id';

    /**
     * Check if category contains active items
     */
    public function hasActiveItems($id) {
        $sql = "SELECT COUNT(*) FROM barang WHERE kategori_id = :id";
        $stmt = $this->query($sql, ['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
