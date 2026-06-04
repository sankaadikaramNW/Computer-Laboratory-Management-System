<?php
/**
 * Laboratory Controller Class
 * Handles CRUD operations for school labs and capacity configuration.
 */
class LaboratoryController extends Controller {
    private $labModel;

    public function __construct() {
        requireLogin();
        $this->labModel = $this->model('LabModel');
    }

    /**
     * Display Labs CRUD Registry page
     */
    public function index() {
        requireAdmin();

        $labs = $this->labModel->getAllLabs();
        
        $data = [
            'title' => 'Laboratory Management',
            'active_menu' => 'laboratories',
            'labs' => $labs
        ];

        $this->view('templates/header', $data);
        $this->view('laboratories/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create a Laboratory
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('laboratory');
            }

            $labCode = strtoupper(trim($_POST['lab_code']));
            $labName = trim($_POST['lab_name']);
            $location = trim($_POST['location']);
            $capacity = (int)$_POST['capacity'];
            $description = trim($_POST['description']);
            $status = $_POST['status'] ?? 'active';

            // Validate duplicate code
            if ($this->labModel->checkLabCodeExists($labCode)) {
                flash('dashboard_error', "Laboratory code '{$labCode}' already exists.", 'alert alert-danger');
                redirect('laboratory');
            }

            $data = [
                'lab_code' => $labCode,
                'lab_name' => $labName,
                'location' => $location,
                'capacity' => $capacity,
                'description' => $description,
                'status' => $status
            ];

            if ($this->labModel->createLab($data)) {
                $this->logActivity('CREATE_LABORATORY', 'LABORATORIES', "Configured new laboratory: {$labCode} - {$labName}");
                flash('dashboard_success', 'Laboratory configured successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to configure laboratory.', 'alert alert-danger');
            }
            redirect('laboratory');
        }
    }

    /**
     * Update Laboratory Details
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('laboratory');
            }

            $labCode = strtoupper(trim($_POST['lab_code']));
            $labName = trim($_POST['lab_name']);
            $location = trim($_POST['location']);
            $capacity = (int)$_POST['capacity'];
            $description = trim($_POST['description']);
            $status = $_POST['status'] ?? 'active';

            // Validate duplicate code excluding current lab
            if ($this->labModel->checkLabCodeExists($labCode, $id)) {
                flash('dashboard_error', "Another laboratory is already using code '{$labCode}'.", 'alert alert-danger');
                redirect('laboratory');
            }

            $data = [
                'lab_code' => $labCode,
                'lab_name' => $labName,
                'location' => $location,
                'capacity' => $capacity,
                'description' => $description,
                'status' => $status
            ];

            if ($this->labModel->updateLab($id, $data)) {
                $this->logActivity('UPDATE_LABORATORY', 'LABORATORIES', "Modified laboratory details: {$labCode} (ID: {$id})");
                flash('dashboard_success', 'Laboratory details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update laboratory.', 'alert alert-danger');
            }
            redirect('laboratory');
        }
    }

    /**
     * Delete Laboratory Details
     */
    public function delete($id) {
        requireAdmin();

        $lab = $this->labModel->getLabById($id);
        if ($lab) {
            if ($this->labModel->deleteLab($id)) {
                $this->logActivity('DELETE_LABORATORY', 'LABORATORIES', "Removed laboratory '{$lab->lab_code}' - {$lab->lab_name}");
                flash('dashboard_success', 'Laboratory removed from database.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to delete laboratory (check if referenced by schedules).', 'alert alert-danger');
            }
        }
        redirect('laboratory');
    }
}
