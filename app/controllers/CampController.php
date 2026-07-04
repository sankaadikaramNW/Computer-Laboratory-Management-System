<?php
/**
 * Camp Controller Class
 * Handles CRUD operations for SLAF bases and stations.
 */
class CampController extends Controller {
    private $campModel;

    public function __construct() {
        requireLogin();
        requireSuperAdmin(); // Only Super Admins can manage camps
        $this->campModel = $this->model('CampModel');
    }

    /**
     * Display Camps List
     */
    public function index() {
        $camps = $this->campModel->getAllCamps();
        
        $data = [
            'title' => 'SLAF Camps',
            'active_menu' => 'camps',
            'camps' => $camps
        ];

        $this->view('templates/header', $data);
        $this->view('camps/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create Camp
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('camp');
            }

            $name = trim($_POST['name']);
            $code = strtoupper(trim($_POST['code']));
            $address = trim($_POST['address']);
            $status = $_POST['status'] ?? 'active';

            // Validate fields
            if (empty($name) || empty($code)) {
                flash('dashboard_error', 'Camp Name and Code are required.', 'alert alert-danger');
                redirect('camp');
            }

            // Check duplicate name
            if ($this->campModel->checkCampNameExists($name)) {
                flash('dashboard_error', "Camp name '{$name}' already exists.", 'alert alert-danger');
                redirect('camp');
            }

            // Check duplicate code
            if ($this->campModel->checkCampCodeExists($code)) {
                flash('dashboard_error', "Camp code '{$code}' already exists.", 'alert alert-danger');
                redirect('camp');
            }

            $data = [
                'name' => $name,
                'code' => $code,
                'address' => $address,
                'status' => $status
            ];

            if ($this->campModel->createCamp($data)) {
                $this->logActivity('CREATE_CAMP', 'CAMPS', "Created new camp location: {$name} ({$code})");
                flash('dashboard_success', 'Camp location registered successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to register camp location.', 'alert alert-danger');
            }
            redirect('camp');
        }
    }

    /**
     * Update Camp
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('camp');
            }

            $name = trim($_POST['name']);
            $code = strtoupper(trim($_POST['code']));
            $address = trim($_POST['address']);
            $status = $_POST['status'];

            // Validate fields
            if (empty($name) || empty($code)) {
                flash('dashboard_error', 'Camp Name and Code are required.', 'alert alert-danger');
                redirect('camp');
            }

            // Check duplicate name excluding self
            if ($this->campModel->checkCampNameExists($name, $id)) {
                flash('dashboard_error', "Another camp is using the name '{$name}'.", 'alert alert-danger');
                redirect('camp');
            }

            // Check duplicate code excluding self
            if ($this->campModel->checkCampCodeExists($code, $id)) {
                flash('dashboard_error', "Another camp is using the code '{$code}'.", 'alert alert-danger');
                redirect('camp');
            }

            $data = [
                'name' => $name,
                'code' => $code,
                'address' => $address,
                'status' => $status
            ];

            if ($this->campModel->updateCamp($id, $data)) {
                $this->logActivity('UPDATE_CAMP', 'CAMPS', "Updated camp location: {$name} (ID: {$id})");
                flash('dashboard_success', 'Camp location details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update camp location.', 'alert alert-danger');
            }
            redirect('camp');
        }
    }

    /**
     * Toggle Status
     */
    public function toggle($id) {
        $camp = $this->campModel->getCampById($id);
        if ($camp) {
            $newStatus = ($camp->status === 'active') ? 'inactive' : 'active';
            if ($this->campModel->updateCampStatus($id, $newStatus)) {
                $this->logActivity('TOGGLE_CAMP_STATUS', 'CAMPS', "Changed status of camp '{$camp->name}' to {$newStatus}");
                flash('dashboard_success', "Camp location status changed to {$newStatus}.", 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to change camp status.', 'alert alert-danger');
            }
        }
        redirect('camp');
    }
}
