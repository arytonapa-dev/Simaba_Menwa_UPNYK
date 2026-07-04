<?php
/**
 * Notifikasi Controller
 * Kelola Notifikasi (Semua Aktor) - FR-21, FR-22
 */

require_once dirname(__DIR__) . '/models/Notifikasi.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';
require_once dirname(dirname(__DIR__)) . '/core/AuditLog.php';

class NotifikasiController extends Controller {

    /**
     * View list of notifications for logged-in user (FR-22)
     */
    public function index() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);

        $model = new Notifikasi();
        $userId = $_SESSION['user_id'];
        
        $notifications = $model->getByRecipient($userId);

        $this->view('notifikasi/index', [
            'title' => 'Notifikasi Masuk',
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read and redirect to related link (FR-22)
     */
    public function read() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);

        $id = (int)$this->input('id');
        $model = new Notifikasi();
        $notif = $model->find($id);

        if ($notif) {
            // Row-level authorization check (Exception Handling FR-22)
            if ($notif['recipient_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                require_once dirname(dirname(__DIR__)) . '/app/views/layouts/403.php';
                exit();
            }

            // Mark as read
            $model->update($id, ['is_read' => 1]);

            // Redirect if link exists
            if (!empty($notif['link_terkait'])) {
                $this->redirect($notif['link_terkait']);
            }
        }

        $this->redirect('/index.php?controller=notifikasi&action=index');
    }

    /**
     * Mark all notifications as read for current user (TC-22 / FR-22)
     */
    public function markAllRead() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);
        $this->validatePostRequest();

        $model = new Notifikasi();
        $userId = $_SESSION['user_id'];
        $model->markAllAsRead($userId);

        $this->redirect('/index.php?controller=notifikasi&action=index');
    }

    /**
     * Delete a single notification (FR-22)
     */
    public function delete() {
        Auth::restrict([ROLE_ADMIN, ROLE_OPERATOR, ROLE_ANGGOTA, ROLE_DANSAT]);
        $this->validatePostRequest();

        $id = (int)$this->input('id');
        $model = new Notifikasi();
        $notif = $model->find($id);

        if ($notif) {
            // Row-level authorization check (Exception Handling FR-22)
            if ($notif['recipient_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                require_once dirname(dirname(__DIR__)) . '/app/views/layouts/403.php';
                exit();
            }

            $model->delete($id);
        }

        $this->redirect('/index.php?controller=notifikasi&action=index');
    }

    /**
     * JSON unread count & list for AJAX Polling (FR-21 / UC-21)
     */
    public function unreadList() {
        if (!Auth::check()) {
            $this->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $model = new Notifikasi();
        $userId = $_SESSION['user_id'];

        $count = $model->getUnreadCount($userId);

        // Fetch latest 5 notifications (unread and read) for the dropdown
        // (Assuming getByRecipient can be limited, if not just take top 5)
        $allNotifs = $model->getByRecipient($userId);
        $notifs = array_slice($allNotifs, 0, 5);

        $this->json([
            'status' => 'success',
            'count' => $count,
            'notifications' => $notifs
        ]);
    }
}
