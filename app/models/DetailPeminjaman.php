<?php
/**
 * Detail Peminjaman Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class DetailPeminjaman extends Model {
    protected $table = 'detail_peminjaman';
    protected $primaryKey = 'detail_peminjaman_id';

    /**
     * Get allocated units for a specific loan
     */
    public function getUnitsByPeminjaman($peminjamanId) {
        $sql = "SELECT dp.*, u.kode_unit, u.kondisi as kondisi_sebelum, b.nama_barang, b.satuan, k.is_critical, k.nama_kategori
                FROM detail_peminjaman dp
                JOIN unit_barang u ON dp.unit_id = u.unit_id
                JOIN barang b ON u.barang_id = b.barang_id
                JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                WHERE dp.peminjaman_id = :peminjaman_id";
        return $this->query($sql, ['peminjaman_id' => $peminjamanId])->fetchAll();
    }
}
