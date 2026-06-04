<?php
/**
 * Audit Controller Class
 * Handles retrieval and management of action log records.
 */
class AuditController extends Controller {
    private $auditModel;

    public function __construct() {
        requireLogin();
        requireAdmin();
        $this->auditModel = $this->model('AuditModel');
    }

    /**
     * Display Audit Logs table
     */
    public function index() {
        $logs = $this->auditModel->getAllLogs();
        
        $data = [
            'title' => 'System Audit Logs',
            'active_menu' => 'audit',
            'logs' => $logs
        ];

        $this->view('templates/header', $data);
        $this->view('audit/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Clear logs older than 90 days
     */
    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('audit');
            }

            if ($this->auditModel->clearOldLogs(90)) {
                $this->logActivity('CLEAR_AUDIT_LOGS', 'AUDIT', 'Archived/Purged audit logs older than 90 days.');
                flash('dashboard_success', 'Audit logs older than 90 days cleared.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to clear old logs.', 'alert alert-danger');
            }
            redirect('audit');
        }
    }
}
