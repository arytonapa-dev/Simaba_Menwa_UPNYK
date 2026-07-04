<?php
/**
 * Daily Reminder Scheduler (H-1 Due Date Alerts - BR-08 / FR-21)
 * Can be run from CLI: php cron/reminder.php
 */

// Load Configuration and Database Core
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/core/Database.php';
require_once dirname(__DIR__) . '/core/Notification.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Find ongoing loans that are due tomorrow (H-1)
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    $sql = "SELECT p.*, u.full_name, u.username, u.email 
            FROM peminjaman p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.status = :status 
            AND p.tanggal_rencana_kembali = :due_date";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'status' => STATUS_PINJAM_ONGOING,
        'due_date' => $tomorrow
    ]);
    
    $loans = $stmt->fetchAll();
    $notifiedCount = 0;

    foreach ($loans as $loan) {
        $pId = $loan['peminjaman_id'];
        $borrowerId = $loan['user_id'];
        $borrowerName = $loan['full_name'];
        $dueDateFormatted = date('d/m/Y', strtotime($loan['tanggal_rencana_kembali']));

        // 1. Notify Borrower (FR-21 / BR-08)
        Notification::send(
            $borrowerId,
            'Pengingat H-1',
            'Pengingat Batas Pengembalian H-1',
            'Halo ' . $borrowerName . ', barang pinjaman Anda dengan nomor permohonan #PJ-' . $pId . ' akan jatuh tempo besok pada ' . $dueDateFormatted . '. Mohon segera ajukan pengembalian.',
            '/index.php?controller=peminjaman&action=riwayat'
        );

        // 2. Notify Operators (BR-08)
        Notification::sendToRole(
            ROLE_OPERATOR,
            'Pengingat H-1 Operator',
            'Pemberitahuan Jatuh Tempo H-1 #' . $pId,
            'Peminjaman #' . $pId . ' oleh ' . $borrowerName . ' akan jatuh tempo besok pada ' . $dueDateFormatted . '.',
            '/index.php?controller=pengembalian&action=verifikasiList'
        );

        $notifiedCount++;
    }

    // Log cron execution (BR-08 / logs/cron.log)
    $logMsg = "[" . date('Y-m-d H:i:s') . "] CRON reminder.php completed. Processed " . $notifiedCount . " H-1 reminder notifications.\n";
    file_put_contents(CRON_LOG_PATH, $logMsg, FILE_APPEND);

    echo "CRON OK: Notified " . $notifiedCount . " active borrowings.\n";

} catch (Exception $e) {
    $errorMsg = "[" . date('Y-m-d H:i:s') . "] CRON ERROR: " . $e->getMessage() . "\n";
    file_put_contents(CRON_LOG_PATH, $errorMsg, FILE_APPEND);
    echo "CRON ERROR: " . $e->getMessage() . "\n";
}
