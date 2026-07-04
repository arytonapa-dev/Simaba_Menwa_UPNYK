<?php
/**
 * Peminjaman Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';
require_once dirname(__DIR__, 2) . '/core/AuditLog.php';
require_once dirname(__DIR__, 2) . '/core/Notification.php';

class Peminjaman extends Model {
    protected $table = 'peminjaman';
    protected $primaryKey = 'peminjaman_id';

    /**
     * Get loans by user_id (Row-Level Security for Anggota - FR-15)
     */
    public function getByUserId($userId) {
        $sql = "SELECT p.*, u.full_name as verifikator_name 
                FROM peminjaman p
                LEFT JOIN users u ON p.verifikator_id = u.user_id
                WHERE p.user_id = :user_id
                ORDER BY p.created_at DESC";
        return $this->query($sql, ['user_id' => $userId])->fetchAll();
    }

    /**
     * Get all loans with user full name (for Operator / Admin)
     */
    public function getAllWithUser($status = null) {
        $sql = "SELECT p.*, u.full_name, u.nim_nip, u.username,
                v.full_name as verifikator_name, d.full_name as dansat_name
                FROM peminjaman p
                JOIN users u ON p.user_id = u.user_id
                LEFT JOIN users v ON p.verifikator_id = v.user_id
                LEFT JOIN users d ON p.approver_dansat_id = d.user_id";
        
        $params = [];
        if ($status !== null) {
            $sql .= " WHERE p.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Find loan with detailed items and borrower info
     */
    public function findDetailed($id) {
        $sql = "SELECT p.*, u.full_name as borrower_name, u.nim_nip as borrower_nim, u.phone as borrower_phone,
                v.full_name as verifikator_name, d.full_name as dansat_name
                FROM peminjaman p
                JOIN users u ON p.user_id = u.user_id
                LEFT JOIN users v ON p.verifikator_id = v.user_id
                LEFT JOIN users d ON p.approver_dansat_id = d.user_id
                WHERE p.peminjaman_id = :id 
                LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Check if a loan has any critical items (BR-03)
     */
    public function hasCriticalItems($id) {
        $sql = "SELECT COUNT(*) 
                FROM detail_peminjaman dp
                JOIN unit_barang u ON dp.unit_id = u.unit_id
                JOIN barang b ON u.barang_id = b.barang_id
                JOIN kategori_barang k ON b.kategori_id = k.kategori_id
                WHERE dp.peminjaman_id = :peminjaman_id 
                AND k.is_critical = 1";
        $stmt = $this->query($sql, ['peminjaman_id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Create a borrowing request (Soft Lock active - FR-09)
     */
    public function createLoan($userId, $items, $tanggalPinjam, $tanggalRencanaKembali, $keperluan) {
        // Validate date consistency
        $pinjamDate = new DateTime($tanggalPinjam);
        $kembaliDate = new DateTime($tanggalRencanaKembali);
        if ($kembaliDate < $pinjamDate) {
            throw new Exception("Tanggal rencana kembali tidak boleh sebelum tanggal pinjam.");
        }

        try {
            $this->db->beginTransaction();

            // Insert into peminjaman
            $sql = "INSERT INTO peminjaman (user_id, status, tanggal_pinjam, tanggal_rencana_kembali, keperluan, created_at, updated_at) 
                    VALUES (:user_id, 'Menunggu Verifikasi', :tanggal_pinjam, :tanggal_rencana_kembali, :keperluan, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_rencana_kembali' => $tanggalRencanaKembali,
                'keperluan' => $keperluan
            ]);
            
            $peminjamanId = $this->db->lastInsertId();

            // Select and allocate units with soft-lock (BR-09b)
            foreach ($items as $barangId => $qty) {
                // Find units of this item that are 'Tersedia' and not in any active/unreturned details
                $sqlUnits = "SELECT u.unit_id, u.kode_unit 
                             FROM unit_barang u 
                             WHERE u.barang_id = :barang_id 
                             AND u.status_ketersediaan = 'Tersedia' 
                             AND u.kondisi != 'Rusak Berat'
                             AND u.unit_id NOT IN (
                                 SELECT dp.unit_id 
                                 FROM detail_peminjaman dp 
                                 JOIN peminjaman p ON dp.peminjaman_id = p.peminjaman_id 
                                 WHERE p.status IN ('Menunggu Verifikasi', 'Menunggu Persetujuan Dansat', 'Disetujui', 'Dipinjam (Berjalan)')
                             )
                             LIMIT :qty";
                
                $stmtUnits = $this->db->prepare($sqlUnits);
                $stmtUnits->bindValue(':barang_id', $barangId, PDO::PARAM_INT);
                $stmtUnits->bindValue(':qty', (int)$qty, PDO::PARAM_INT);
                $stmtUnits->execute();
                $allocatedUnits = $stmtUnits->fetchAll();

                if (count($allocatedUnits) < $qty) {
                    throw new Exception("Stok tidak mencukupi untuk barang " . $barangId);
                }

                // Insert allocated units into detail_peminjaman
                $sqlDetail = "INSERT INTO detail_peminjaman (peminjaman_id, unit_id, kondisi_saat_pinjam, created_at) 
                              VALUES (:peminjaman_id, :unit_id, 'Baik', NOW())";
                $stmtDetail = $this->db->prepare($sqlDetail);

                foreach ($allocatedUnits as $unit) {
                    $stmtDetail->execute([
                        'peminjaman_id' => $peminjamanId,
                        'unit_id' => $unit['unit_id']
                    ]);
                }
            }

            $this->db->commit();
            
            // Log to audit log
            AuditLog::log("Pengajuan Peminjaman Baru #" . $peminjamanId, 'peminjaman', null, ['peminjaman_id' => $peminjamanId]);

            // Notify Operators
            Notification::sendToRole(ROLE_OPERATOR, 'Peminjaman Baru', 'Pengajuan Peminjaman Baru', 'Terdapat pengajuan peminjaman baru dari Anggota yang membutuhkan verifikasi.', "/index.php?controller=peminjaman&action=detail&id=" . $peminjamanId);

            return $peminjamanId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
