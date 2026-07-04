<?php
/**
 * Audit Controller
 * Monitoring Jejak Audit (Admin Only) - FR-20
 */

require_once dirname(__DIR__) . '/models/AuditLogModel.php';
require_once dirname(dirname(__DIR__)) . '/core/Auth.php';

class AuditController extends Controller {

    /**
     * Display list of audit logs (FR-20 / TC-20)
     */
    public function index() {
        Auth::restrict([ROLE_ADMIN]);

        $model = new AuditLogModel();
        
        $userFilter = $this->input('user');
        $activityFilter = $this->input('activity');
        $moduleFilter = $this->input('module');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        // Sorting configuration
        $sort = $this->input('sort') ?: 'waktu';
        $order = $this->input('order') ?: 'desc';
        
        $orderByMap = [
            'waktu' => 'l.created_at',
            'user' => 'u.username',
            'modul' => 'l.modul',
            'ip' => 'l.ip_address'
        ];
        
        $orderByCol = isset($orderByMap[$sort]) ? $orderByMap[$sort] : 'l.created_at';
        $orderByOrder = strtolower($order) === 'asc' ? 'ASC' : 'DESC';
        $orderBy = $orderByCol . ' ' . $orderByOrder;

        // Pagination configuration
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $totalLogs = 0;
        $totalPages = 1;

        $logs = [];

        // Fetch Overview Stats directly from Database
        $statTotal = 0;
        $statLogin = 0;
        $statCrud = 0;
        $statApprove = 0;
        $statToday = 0;

        $this->view('audit/index', [
            'title' => 'Audit Trail Logger',
            'logs' => $logs,
            'userFilter' => $userFilter,
            'activityFilter' => $activityFilter,
            'moduleFilter' => $moduleFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs,
            'sort' => $sort,
            'order' => $order,
            'stats' => [
                'total' => $statTotal,
                'login' => $statLogin,
                'crud' => $statCrud,
                'approve' => $statApprove,
                'today' => $statToday
            ]
        ]);
    }

    /**
     * Get JSON detail of a single audit log entry for modal display (UC-20 / TC-20)
     */
    public function detail() {
        Auth::restrict([ROLE_ADMIN]);
        
        $id = (int)$this->input('id');
        $model = new AuditLogModel();
        $log = $model->find($id);

        if ($log) {
            $this->json([
                'status' => 'success',
                'data' => [
                    'log_id' => $log['log_id'],
                    'ip_address' => $log['ip_address'],
                    'data_sebelum' => $log['data_sebelum'] ? json_decode($log['data_sebelum'], true) : null,
                    'data_sesudah' => $log['data_sesudah'] ? json_decode($log['data_sesudah'], true) : null
                ]
            ]);
        } else {
            $this->json(['status' => 'error', 'message' => 'Log tidak ditemukan.'], 404);
        }
    }
}
