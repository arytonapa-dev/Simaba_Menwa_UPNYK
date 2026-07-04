<?php
/**
 * Dashboard Controller
 * Tampilan Dashboard sesuai RBAC (FR-16)
 */

require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/Database.php';

class DashboardController extends Controller {

    public function index() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);
        
        $user = Auth::user();
        $roleId = $user['role_id'];
        $db = Database::getInstance()->getConnection();

        $data = [
            'fullName' => $user['full_name'],
            'roleName' => ROLE_NAMES[$roleId]
        ];

        // Fetch metrics based on role (Tabel 2.2 / FR-16)
        if ($roleId == ROLE_ADMIN) {
            // Admin metrics: total barang, total unit tersedia, total pengajuan pending, pengguna aktif
            $data['total_barang'] = $db->query("SELECT COUNT(*) FROM barang")->fetchColumn();
            $data['total_unit_tersedia'] = $db->query("SELECT COUNT(*) FROM unit_barang WHERE status_ketersediaan = 'Tersedia' AND kondisi != 'Rusak Berat'")->fetchColumn();
            $data['total_pending_verif'] = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Menunggu Verifikasi'")->fetchColumn();
            $data['total_pengguna'] = $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
            
            // 5 Recent audit log entries
            $data['recent_logs'] = [];

            // Condition counts for Pie Chart
            $data['kondisi_baik'] = $db->query("SELECT COUNT(*) FROM unit_barang WHERE kondisi = 'Baik'")->fetchColumn();
            $data['kondisi_rusak_ringan'] = $db->query("SELECT COUNT(*) FROM unit_barang WHERE kondisi = 'Rusak Ringan'")->fetchColumn();
            $data['kondisi_rusak_berat'] = $db->query("SELECT COUNT(*) FROM unit_barang WHERE kondisi = 'Rusak Berat'")->fetchColumn();

            $this->view('dashboard/admin', $data);

        } elseif ($roleId == ROLE_OPERATOR) {
            // Operator metrics: pending loans verif, pending returns verif, units borrowed, units in repair
            $data['pending_loans'] = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Menunggu Verifikasi'")->fetchColumn();
            $data['pending_returns'] = $db->query("SELECT COUNT(*) FROM pengembalian WHERE status = 'Menunggu Verifikasi'")->fetchColumn();
            $data['units_borrowed'] = $db->query("SELECT COUNT(*) FROM unit_barang WHERE status_ketersediaan = 'Dipinjam'")->fetchColumn();
            $data['units_repair'] = $db->query("SELECT COUNT(*) FROM unit_barang WHERE status_ketersediaan = 'Perbaikan' OR kondisi = 'Rusak Berat'")->fetchColumn();

            // List of pending loans for quick verifications
            $sqlPending = "SELECT p.*, u.full_name, u.nim_nip 
                           FROM peminjaman p 
                           JOIN users u ON p.user_id = u.user_id 
                           WHERE p.status = 'Menunggu Verifikasi' 
                           ORDER BY p.created_at ASC LIMIT 5";
            $data['quick_loans'] = $db->query($sqlPending)->fetchAll();

            $this->view('dashboard/operator', $data);

        } elseif ($roleId == ROLE_ANGGOTA) {
            // Anggota metrics: units currently borrowed, loans waiting verification
            $userId = $user['id'];
            
            $sqlB = "SELECT COUNT(DISTINCT dp.unit_id) 
                     FROM detail_peminjaman dp 
                     JOIN peminjaman p ON dp.peminjaman_id = p.peminjaman_id 
                     WHERE p.user_id = :user_id AND p.status = 'Dipinjam (Berjalan)'";
            $stmtB = $db->prepare($sqlB);
            $stmtB->execute(['user_id' => $userId]);
            $data['units_borrowed'] = $stmtB->fetchColumn();

            $sqlW = "SELECT COUNT(*) FROM peminjaman 
                     WHERE user_id = :user_id 
                     AND status IN ('Menunggu Verifikasi', 'Menunggu Persetujuan Dansat')";
            $stmtW = $db->prepare($sqlW);
            $stmtW->execute(['user_id' => $userId]);
            $data['loans_waiting'] = $stmtW->fetchColumn();

            // List of active borrowing items
            $sqlList = "SELECT p.peminjaman_id, p.tanggal_pinjam, p.tanggal_rencana_kembali, p.status,
                        (SELECT COUNT(*) FROM detail_peminjaman dp WHERE dp.peminjaman_id = p.peminjaman_id) as total_items
                        FROM peminjaman p
                        WHERE p.user_id = :user_id AND p.status = 'Dipinjam (Berjalan)'
                        ORDER BY p.tanggal_pinjam DESC";
            $stmtList = $db->prepare($sqlList);
            $stmtList->execute(['user_id' => $userId]);
            $data['active_loans'] = $stmtList->fetchAll();

            $this->view('dashboard/anggota', $data);

        } elseif ($roleId == ROLE_DANSAT) {
            // Dansat metrics: pending critical loans, total broken items
            $data['pending_critical'] = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Menunggu Persetujuan Dansat'")->fetchColumn();
            
            $totalUnits = $db->query("SELECT COUNT(*) FROM unit_barang")->fetchColumn();
            $damagedUnits = $db->query("SELECT COUNT(*) FROM unit_barang WHERE kondisi IN ('Rusak Ringan', 'Rusak Berat')")->fetchColumn();
            $data['damage_percentage'] = $totalUnits > 0 ? round(($damagedUnits / $totalUnits) * 100, 1) : 0;

            // List of pending critical loans
            $sqlCrit = "SELECT p.*, u.full_name, u.nim_nip 
                        FROM peminjaman p 
                        JOIN users u ON p.user_id = u.user_id 
                        WHERE p.status = 'Menunggu Persetujuan Dansat' 
                        ORDER BY p.created_at ASC";
            $data['critical_loans'] = $db->query($sqlCrit)->fetchAll();

            $this->view('dashboard/dansat', $data);
        }
    }
}
