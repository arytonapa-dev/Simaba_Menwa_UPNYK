<?php
/**
 * Barang (Master Item) Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class Barang extends Model {
    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

    /**
     * Find item by ID with Category and Section names
     */
    public function findWithRelations($id) {
        $sql = "SELECT b.*, k.nama_kategori, k.is_critical, bi.nama_bidang, bi.penanggung_jawab 
                FROM barang b
                JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                JOIN bidang_barang bi ON b.bidang_id = bi.bidang_id
                WHERE b.barang_id = :id 
                LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Get all master items with Category, Section and unit aggregates (total, available, etc)
     */
    public function getAllWithAggregates() {
        $sql = "SELECT b.*, k.nama_kategori, k.is_critical, bi.nama_bidang,
                COUNT(u.unit_id) as total_unit,
                SUM(CASE WHEN u.status_ketersediaan = 'Tersedia' AND u.kondisi != 'Rusak Berat' THEN 1 ELSE 0 END) as tersedia_unit,
                SUM(CASE WHEN u.status_ketersediaan = 'Dipinjam' THEN 1 ELSE 0 END) as dipinjam_unit,
                SUM(CASE WHEN u.status_ketersediaan = 'Perbaikan' OR u.kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as perbaikan_unit,
                SUM(CASE WHEN u.status_ketersediaan = 'Hilang' THEN 1 ELSE 0 END) as hilang_unit
                FROM barang b
                JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                JOIN bidang_barang bi ON b.bidang_id = bi.bidang_id
                LEFT JOIN unit_barang u ON b.barang_id = u.barang_id
                GROUP BY b.barang_id
                ORDER BY b.created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Check if item has child units
     */
    public function hasUnits($id) {
        $sql = "SELECT COUNT(*) FROM unit_barang WHERE barang_id = :id";
        $stmt = $this->query($sql, ['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
