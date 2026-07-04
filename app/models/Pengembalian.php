<?php
/**
 * Pengembalian Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';
require_once dirname(__DIR__, 2) . '/core/AuditLog.php';
require_once dirname(__DIR__, 2) . '/core/Notification.php';
require_once __DIR__ . '/UnitBarang.php';

class Pengembalian extends Model {
    protected $table = 'pengembalian';
    protected $primaryKey = 'pengembalian_id';

    /**
     * Get returns by user_id
     */
    public function getByUserId($userId) {
        $sql = "SELECT r.*, p.tanggal_pinjam, p.tanggal_rencana_kembali 
                FROM pengembalian r
                JOIN peminjaman p ON r.peminjaman_id = p.peminjaman_id
                WHERE r.user_id = :user_id
                ORDER BY r.created_at DESC";
        return $this->query($sql, ['user_id' => $userId])->fetchAll();
    }

    /**
     * Get all returns with borrower names
     */
    public function getAllWithUser($status = null) {
        $sql = "SELECT r.*, u.full_name, u.nim_nip, p.tanggal_rencana_kembali,
                v.full_name as verifikator_name
                FROM pengembalian r
                JOIN users u ON r.user_id = u.user_id
                JOIN peminjaman p ON r.peminjaman_id = p.peminjaman_id
                LEFT JOIN users v ON r.verifikator_id = v.user_id";
        
        $params = [];
        if ($status !== null) {
            $sql .= " WHERE r.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Find return detailed
     */
    public function findDetailed($id) {
        $sql = "SELECT r.*, u.full_name as borrower_name, u.nim_nip as borrower_nim, u.phone as borrower_phone,
                p.tanggal_pinjam, p.tanggal_rencana_kembali, p.keperluan,
                v.full_name as verifikator_name
                FROM pengembalian r
                JOIN users u ON r.user_id = u.user_id
                JOIN peminjaman p ON r.peminjaman_id = p.peminjaman_id
                LEFT JOIN users v ON r.verifikator_id = v.user_id
                WHERE r.pengembalian_id = :id 
                LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Create a return request (FR-13)
     */
    public function createReturn($peminjamanId, $userId, $unitData) {
        try {
            $this->db->beginTransaction();

            // Fetch parent loan
            $sqlP = "SELECT tanggal_rencana_kembali FROM peminjaman WHERE peminjaman_id = :id";
            $stmtP = $this->db->prepare($sqlP);
            $stmtP->execute(['id' => $peminjamanId]);
            $loan = $stmtP->fetch();

            if (!$loan) {
                throw new Exception("Transaksi peminjaman tidak ditemukan.");
            }

            $rencanaKembali = new DateTime($loan['tanggal_rencana_kembali']);
            $today = new DateTime(date('Y-m-d'));
            
            $isTerlambat = 0;
            $hariTerlambat = 0;

            if ($today > $rencanaKembali) {
                $isTerlambat = 1;
                $diff = $today->diff($rencanaKembali);
                $hariTerlambat = $diff->days;
            }

            // Insert into pengembalian table
            $sql = "INSERT INTO pengembalian (peminjaman_id, user_id, status, tanggal_pengajuan, is_terlambat, hari_terlambat, created_at, updated_at) 
                    VALUES (:peminjaman_id, :user_id, 'Menunggu Verifikasi', NOW(), :is_terlambat, :hari_terlambat, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'peminjaman_id' => $peminjamanId,
                'user_id' => $userId,
                'is_terlambat' => $isTerlambat,
                'hari_terlambat' => $hariTerlambat
            ]);
            
            $pengembalianId = $this->db->lastInsertId();

            // Insert into detail_pengembalian
            $sqlD = "INSERT INTO detail_pengembalian (pengembalian_id, unit_id, kondisi_self_report, created_at) 
                     VALUES (:pengembalian_id, :unit_id, :kondisi_self_report, NOW())";
            $stmtD = $this->db->prepare($sqlD);

            foreach ($unitData as $unitId => $selfReport) {
                $stmtD->execute([
                    'pengembalian_id' => $pengembalianId,
                    'unit_id' => $unitId,
                    'kondisi_self_report' => $selfReport
                ]);
            }

            $this->db->commit();

            // Log to audit log
            AuditLog::log("Pengajuan Pengembalian Baru #" . $pengembalianId, 'pengembalian', null, ['pengembalian_id' => $pengembalianId]);

            // Notify Operator
            Notification::sendToRole(ROLE_OPERATOR, 'Pengembalian Baru', 'Pengajuan Pengembalian', 'Terdapat pengajuan pengembalian barang baru dari Anggota yang membutuhkan verifikasi.', "/index.php?controller=pengembalian&action=detail&id=" . $pengembalianId);

            return $pengembalianId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Verify a return request (FR-14, BR-01, BR-04)
     */
    public function verifyReturn($pengembalianId, $verifikatorId, $unitConditions, $unitStatuses, $catatan) {
        try {
            $this->db->beginTransaction();

            // Update pengembalian status
            $sqlU = "UPDATE pengembalian SET 
                     status = 'Selesai', 
                     tanggal_verifikasi = NOW(), 
                     verifikator_id = :verifikator_id, 
                     catatan = :catatan, 
                     updated_at = NOW() 
                     WHERE pengembalian_id = :pengembalian_id";
            
            $stmtU = $this->db->prepare($sqlU);
            $stmtU->execute([
                'verifikator_id' => $verifikatorId,
                'catatan' => $catatan,
                'pengembalian_id' => $pengembalianId
            ]);

            // Fetch return details to process each unit
            $sqlD = "SELECT * FROM detail_pengembalian WHERE pengembalian_id = :id";
            $stmtD = $this->db->prepare($sqlD);
            $stmtD->execute(['id' => $pengembalianId]);
            $details = $stmtD->fetchAll();

            $unitModel = new UnitBarang();

            $sqlDUpdate = "UPDATE detail_pengembalian SET kondisi_akhir = :kondisi_akhir WHERE detail_pengembalian_id = :id";
            $stmtDUpdate = $this->db->prepare($sqlDUpdate);

            foreach ($details as $row) {
                $unitId = $row['unit_id'];
                $kondisiAkhir = $unitConditions[$unitId] ?? 'Baik';
                $statusBaru = $unitStatuses[$unitId] ?? 'Tersedia';

                // Update unit kondisi_akhir in detail_pengembalian
                $stmtDUpdate->execute([
                    'kondisi_akhir' => $kondisiAkhir,
                    'id' => $row['detail_pengembalian_id']
                ]);

                // Call UnitBarang Single Source of Truth
                $unitModel->updateKondisiDanStatus($unitId, $kondisiAkhir, $statusBaru);
            }

            // Get Peminjaman ID associated with this return
            $sqlGetP = "SELECT peminjaman_id, user_id FROM pengembalian WHERE pengembalian_id = :id";
            $stmtGetP = $this->db->prepare($sqlGetP);
            $stmtGetP->execute(['id' => $pengembalianId]);
            $returnInfo = $stmtGetP->fetch();
            $peminjamanId = $returnInfo['peminjaman_id'];
            $borrowerId = $returnInfo['user_id'];

            // Check if ALL units of this loan have been returned (BR-04)
            // 1. Get total units in the loan
            $sqlTotalLoan = "SELECT COUNT(*) FROM detail_peminjaman WHERE peminjaman_id = :id";
            $stmtTotal = $this->db->prepare($sqlTotalLoan);
            $stmtTotal->execute(['id' => $peminjamanId]);
            $totalUnits = $stmtTotal->fetchColumn();

            // 2. Get total unique verified returned units for this loan
            $sqlTotalReturned = "SELECT COUNT(DISTINCT dp.unit_id) 
                                 FROM detail_pengembalian dp
                                 JOIN pengembalian r ON dp.pengembalian_id = r.pengembalian_id
                                 WHERE r.peminjaman_id = :id AND r.status = 'Selesai'";
            
            $stmtReturned = $this->db->prepare($sqlTotalReturned);
            $stmtReturned->execute(['id' => $peminjamanId]);
            $totalReturnedUnits = $stmtReturned->fetchColumn();

            if ($totalReturnedUnits >= $totalUnits) {
                // All units returned, status becomes Selesai
                $sqlPUpdate = "UPDATE peminjaman SET status = 'Selesai', updated_at = NOW() WHERE peminjaman_id = :id";
                $stmtPUpdate = $this->db->prepare($sqlPUpdate);
                $stmtPUpdate->execute(['id' => $peminjamanId]);

                // Notify borrower
                Notification::send($borrowerId, 'Peminjaman Selesai', 'Peminjaman Selesai Terverifikasi', 'Peminjaman #' . $peminjamanId . ' telah dinyatakan selesai setelah seluruh unit diverifikasi pengembaliannya.', "/index.php?controller=peminjaman&action=riwayat");
            } else {
                // Partial return, parent remains Dipinjam (Berjalan)
                $sqlPUpdate = "UPDATE peminjaman SET status = 'Dipinjam (Berjalan)', updated_at = NOW() WHERE peminjaman_id = :id";
                $stmtPUpdate = $this->db->prepare($sqlPUpdate);
                $stmtPUpdate->execute(['id' => $peminjamanId]);

                // Notify borrower about partial return
                Notification::send($borrowerId, 'Pengembalian Parsial', 'Verifikasi Pengembalian Parsial', 'Pengembalian parsial untuk peminjaman #' . $peminjamanId . ' telah diverifikasi. Sisa barang masih terhitung dipinjam.', "/index.php?controller=peminjaman&action=riwayat");
            }

            $this->db->commit();

            // Log to audit log
            AuditLog::log("Verifikasi Pengembalian #" . $pengembalianId, 'pengembalian', null, ['pengembalian_id' => $pengembalianId]);

            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
