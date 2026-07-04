<?php
/**
 * Unit Barang Model
 * Single Source of Truth untuk update kondisi unit (NFR-MAINT-05)
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';
require_once dirname(__DIR__, 2) . '/core/AuditLog.php';
require_once dirname(__DIR__, 2) . '/core/Notification.php';

class UnitBarang extends Model {
    protected $table = 'unit_barang';
    protected $primaryKey = 'unit_id';

    /**
     * Get units of an item
     */
    public function getUnitsByBarang($barangId) {
        $sql = "SELECT u.*, b.nama_barang, b.satuan 
                FROM unit_barang u
                JOIN barang b ON u.barang_id = b.barang_id
                WHERE u.barang_id = :barang_id";
        return $this->query($sql, ['barang_id' => $barangId])->fetchAll();
    }

    /**
     * Get all units with item names
     */
    public function getAllWithBarang() {
        $sql = "SELECT u.*, b.nama_barang, b.satuan, k.nama_kategori, bi.nama_bidang
                FROM unit_barang u
                JOIN barang b ON u.barang_id = b.barang_id
                JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                JOIN bidang_barang bi ON b.bidang_id = bi.bidang_id
                ORDER BY u.created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Find available units of an item (Condition is NOT Rusak Berat)
     * For Ajukan Peminjaman (FR-09)
     */
    public function getAvailableUnitsByBarang($barangId) {
        $sql = "SELECT * FROM unit_barang 
                WHERE barang_id = :barang_id 
                AND status_ketersediaan = 'Tersedia' 
                AND kondisi != 'Rusak Berat'";
        return $this->query($sql, ['barang_id' => $barangId])->fetchAll();
    }

    /**
     * Single Source of Truth for Updating Condition and Status (BR-01, FR-08)
     */
    public function updateKondisiDanStatus($unitId, $kondisiBaru, $statusBaru) {
        // Enforce Business Rule BR-01 (Kondisi vs Status Ketersediaan)
        if ($kondisiBaru === 'Baik') {
            if ($statusBaru !== 'Tersedia' && $statusBaru !== 'Dipinjam') {
                throw new Exception("Pesan Error BR-01: Unit dengan kondisi Baik hanya boleh berstatus Tersedia atau Dipinjam.");
            }
        } else if ($kondisiBaru === 'Rusak Ringan') {
            if ($statusBaru !== 'Tersedia' && $statusBaru !== 'Perbaikan') {
                throw new Exception("Pesan Error BR-01: Unit dengan kondisi Rusak Ringan hanya boleh berstatus Tersedia atau Perbaikan.");
            }
        } else if ($kondisiBaru === 'Rusak Berat') {
            if ($statusBaru !== 'Perbaikan' && $statusBaru !== 'Hilang') {
                throw new Exception("Pesan Error BR-01: Unit dengan kondisi Rusak Berat hanya boleh berstatus Perbaikan atau Hilang (TIDAK BOLEH Tersedia/Dipinjam).");
            }
        }

        // Fetch state before update for logging
        $before = $this->find($unitId);
        if (!$before) {
            throw new Exception("Unit tidak ditemukan.");
        }

        // Perform update
        $this->update($unitId, [
            'kondisi' => $kondisiBaru,
            'status_ketersediaan' => $statusBaru,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $after = $this->find($unitId);

        // Record in Audit Log
        AuditLog::log(
            "Pembaruan kondisi unit " . $after['kode_unit'] . " menjadi " . $kondisiBaru . " (" . $statusBaru . ")",
            'unit_barang',
            $before,
            $after
        );

        // Trigger Notification to Admin on Rusak Berat (Alternative Flow UC-14 / FR-14)
        if ($kondisiBaru === 'Rusak Berat') {
            Notification::sendToRole(
                ROLE_ADMIN,
                'Kerusakan',
                'Pemberitahuan: Unit Rusak Berat Terdeteksi',
                'Unit ' . $after['kode_unit'] . ' dari barang ' . $this->getBarangName($after['barang_id']) . ' diubah kondisinya menjadi Rusak Berat.'
            );
        }

        return true;
    }

    private function getBarangName($barangId) {
        $sql = "SELECT nama_barang FROM barang WHERE barang_id = :id";
        $stmt = $this->query($sql, ['id' => $barangId]);
        $row = $stmt->fetch();
        return $row ? $row['nama_barang'] : '';
    }
}
