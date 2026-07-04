<?php
/**
 * Detail Pengembalian Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class DetailPengembalian extends Model {
    protected $table = 'detail_pengembalian';
    protected $primaryKey = 'detail_pengembalian_id';

    /**
     * Get units of a specific return request
     */
    public function getUnitsByPengembalian($pengembalianId) {
        $sql = "SELECT dp.*, u.kode_unit, b.nama_barang, b.satuan
                FROM detail_pengembalian dp
                JOIN unit_barang u ON dp.unit_id = u.unit_id
                JOIN barang b ON u.barang_id = b.barang_id
                WHERE dp.pengembalian_id = :pengembalian_id";
        return $this->query($sql, ['pengembalian_id' => $pengembalianId])->fetchAll();
    }
}
