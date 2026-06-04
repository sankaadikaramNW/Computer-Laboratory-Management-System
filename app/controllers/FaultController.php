<?php
/**
 * Fault Controller Class
 * Handles fault logging, ticket status updates, and automated hardware status updates.
 */
class FaultController extends Controller {
    private $faultModel;
    private $computerModel;
    private $smartBoardModel;
    private $notifModel;

    public function __construct() {
        requireLogin();
        $this->faultModel = $this->model('FaultModel');
        $this->computerModel = $this->model('ComputerModel');
        $this->smartBoardModel = $this->model('SmartBoardModel');
        $this->notifModel = $this->model('NotificationModel');
    }

    /**
     * Admin view list & review panel
     */
    public function index() {
        requireAdmin();

        $faults = $this->faultModel->getAllFaults();
        
        $data = [
            'title' => 'Fault Ticket Registry',
            'active_menu' => 'faults',
            'faults' => $faults
        ];

        $this->view('templates/header', $data);
        $this->view('faults/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Instructor view submission form & history list
     */
    public function instructor() {
        requireInstructor();

        $userId = $_SESSION['user_id'];

        // Get hardware lists for the selector
        $computers = $this->computerModel->getAllComputers();
        $smartboards = $this->smartBoardModel->getAllSmartBoards();
        $myFaults = $this->faultModel->getFaultsByReporter($userId);

        $data = [
            'title' => 'Report Fault / Support Ticket',
            'active_menu' => 'faults',
            'computers' => $computers,
            'smartboards' => $smartboards,
            'faults' => $myFaults
        ];

        $this->view('templates/header', $data);
        $this->view('faults/instructor', $data);
        $this->view('templates/footer');
    }

    /**
     * Report a new fault ticket (Instructor action)
     */
    public function create() {
        requireInstructor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('fault/instructor');
            }

            $equipmentType = trim($_POST['equipment_type']); // computer, smart_board, network, other
            $equipmentId = !empty($_POST['equipment_id']) ? (int)$_POST['equipment_id'] : null;
            $description = trim($_POST['description']);

            $data = [
                'reported_by' => $_SESSION['user_id'],
                'equipment_type' => $equipmentType,
                'equipment_id' => $equipmentId,
                'description' => $description
            ];

            if ($this->faultModel->createFault($data)) {
                // Automate hardware status update (mark as faulty)
                if ($equipmentId) {
                    if ($equipmentType === 'computer') {
                        $this->computerModel->updateStatus($equipmentId, 'faulty');
                    } elseif ($equipmentType === 'smart_board') {
                        $this->smartBoardModel->updateStatus($equipmentId, 'faulty');
                    }
                }

                $this->logActivity('REPORT_FAULT', 'FAULTS', "Reported {$equipmentType} fault. ID: {$equipmentId}");
                flash('dashboard_success', 'Fault ticket reported successfully. Tech team will review.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to report fault ticket.', 'alert alert-danger');
            }
            redirect('fault/instructor');
        }
    }

    /**
     * Update Fault Ticket status (Admin action)
     */
    public function review($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('fault');
            }

            $status = trim($_POST['status']); // reported, in_progress, resolved, closed
            $notes = trim($_POST['notes']);

            $ticket = $this->faultModel->getFaultById($id);
            if (!$ticket) {
                flash('dashboard_error', 'Fault ticket not found.', 'alert alert-danger');
                redirect('fault');
            }

            if ($this->faultModel->updateStatus($id, $status, $notes)) {
                // Update equipment status based on fault status
                if ($ticket->equipment_id) {
                    $newEquipStatus = 'active';
                    if ($status === 'in_progress') {
                        $newEquipStatus = 'maintenance';
                    } elseif ($status === 'reported') {
                        $newEquipStatus = 'faulty';
                    } elseif ($status === 'resolved' || $status === 'closed') {
                        $newEquipStatus = 'active';
                    }

                    if ($ticket->equipment_type === 'computer') {
                        $this->computerModel->updateStatus($ticket->equipment_id, $newEquipStatus);
                    } elseif ($ticket->equipment_type === 'smart_board') {
                        $this->smartBoardModel->updateStatus($ticket->equipment_id, $newEquipStatus);
                    }
                }

                // Notify reporter
                $notifMsg = "Your reported ticket #FLT-{$id} status updated to: {$status}. Resolution Notes: {$notes}";
                $this->notifModel->createNotification($ticket->reported_by, $notifMsg, 'fault_update', $id);

                $this->logActivity('REVIEW_FAULT', 'FAULTS', "Updated fault ticket #FLT-{$id} status to {$status}");
                flash('dashboard_success', 'Fault ticket status updated.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update ticket status.', 'alert alert-danger');
            }
            redirect('fault');
        }
    }
}
