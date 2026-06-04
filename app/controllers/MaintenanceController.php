<?php
/**
 * Maintenance Controller Class
 * Handles servicing schedules, technician logs, and status synchronization.
 */
class MaintenanceController extends Controller {
    private $maintModel;
    private $computerModel;
    private $smartBoardModel;

    public function __construct() {
        requireLogin();
        $this->maintModel = $this->model('MaintenanceModel');
        $this->computerModel = $this->model('ComputerModel');
        $this->smartBoardModel = $this->model('SmartBoardModel');
    }

    /**
     * Display servicing scheduler & CRUD table
     */
    public function index() {
        requireAdmin();

        $records = $this->maintModel->getAllRecords();
        $computers = $this->computerModel->getAllComputers();
        $smartboards = $this->smartBoardModel->getAllSmartBoards();

        $data = [
            'title' => 'Servicing & preventative logs',
            'active_menu' => 'maintenance',
            'records' => $records,
            'computers' => $computers,
            'smartboards' => $smartboards
        ];

        $this->view('templates/header', $data);
        $this->view('maintenance/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create maintenance log
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('maintenance');
            }

            $equipmentType = trim($_POST['equipment_type']);
            $equipmentId = !empty($_POST['equipment_id']) ? (int)$_POST['equipment_id'] : null;
            $issueType = trim($_POST['issue_type']); // preventative, hardware_repair, software_install, upgrade
            $technician = trim($_POST['assigned_technician']);
            $repairDate = trim($_POST['repair_date']);
            $status = trim($_POST['status']); // scheduled, in_progress, completed
            $notes = trim($_POST['notes']);

            $data = [
                'equipment_type' => $equipmentType,
                'equipment_id' => $equipmentId,
                'issue_type' => $issueType,
                'assigned_technician' => $technician,
                'repair_date' => $repairDate,
                'status' => $status,
                'notes' => $notes
            ];

            if ($this->maintModel->createRecord($data)) {
                // Sync equipment status
                if ($equipmentId) {
                    $newEquipStatus = 'active';
                    if ($status === 'in_progress') {
                        $newEquipStatus = 'maintenance';
                    } elseif ($status === 'completed') {
                        $newEquipStatus = 'active';
                    }

                    if ($equipmentType === 'computer') {
                        $this->computerModel->updateStatus($equipmentId, $newEquipStatus);
                    } elseif ($equipmentType === 'smart_board') {
                        $this->smartBoardModel->updateStatus($equipmentId, $newEquipStatus);
                    }
                }

                $this->logActivity('CREATE_MAINTENANCE', 'MAINTENANCE', "Logged {$issueType} servicing for {$equipmentType} ID {$equipmentId} status {$status}");
                flash('dashboard_success', 'Servicing record logged successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to log servicing record.', 'alert alert-danger');
            }
            redirect('maintenance');
        }
    }

    /**
     * Update maintenance log
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('maintenance');
            }

            $equipmentType = trim($_POST['equipment_type']);
            $equipmentId = !empty($_POST['equipment_id']) ? (int)$_POST['equipment_id'] : null;
            $issueType = trim($_POST['issue_type']);
            $technician = trim($_POST['assigned_technician']);
            $repairDate = trim($_POST['repair_date']);
            $status = trim($_POST['status']);
            $notes = trim($_POST['notes']);

            $data = [
                'equipment_type' => $equipmentType,
                'equipment_id' => $equipmentId,
                'issue_type' => $issueType,
                'assigned_technician' => $technician,
                'repair_date' => $repairDate,
                'status' => $status,
                'notes' => $notes
            ];

            if ($this->maintModel->updateRecord($id, $data)) {
                // Sync equipment status
                if ($equipmentId) {
                    $newEquipStatus = 'active';
                    if ($status === 'in_progress') {
                        $newEquipStatus = 'maintenance';
                    } elseif ($status === 'completed') {
                        $newEquipStatus = 'active';
                    }

                    if ($equipmentType === 'computer') {
                        $this->computerModel->updateStatus($equipmentId, $newEquipStatus);
                    } elseif ($equipmentType === 'smart_board') {
                        $this->smartBoardModel->updateStatus($equipmentId, $newEquipStatus);
                    }
                }

                $this->logActivity('UPDATE_MAINTENANCE', 'MAINTENANCE', "Updated servicing log ID {$id} status {$status}");
                flash('dashboard_success', 'Servicing record updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update servicing record.', 'alert alert-danger');
            }
            redirect('maintenance');
        }
    }

    /**
     * Delete maintenance log
     */
    public function delete($id) {
        requireAdmin();

        $record = $this->maintModel->getRecordById($id);
        if ($record) {
            if ($this->maintModel->deleteRecord($id)) {
                $this->logActivity('DELETE_MAINTENANCE', 'MAINTENANCE', "Deleted servicing log ID: {$id}");
                flash('dashboard_success', 'Servicing record deleted successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to delete servicing record.', 'alert alert-danger');
            }
        }
        redirect('maintenance');
    }
}
