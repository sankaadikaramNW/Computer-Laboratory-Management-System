<?php
/**
 * Equipment Controller Class
 * Handles hardware asset tracking, technical specs configuration, and lab transfers.
 */
class EquipmentController extends Controller {
    private $computerModel;
    private $smartBoardModel;
    private $labModel;

    public function __construct() {
        requireLogin();
        $this->computerModel = $this->model('ComputerModel');
        $this->smartBoardModel = $this->model('SmartBoardModel');
        $this->labModel = $this->model('LabModel');
    }

    /**
     * Computers Inventory List & Management Page
     */
    public function computers() {
        requireAdmin();

        if (isSuperAdmin()) {
            $computers = $this->computerModel->getAllComputers();
            $labs = $this->labModel->getActiveLabs();
        } else {
            $campId = $_SESSION['camp_id'];
            $computers = $this->computerModel->getAllComputers($campId);
            $labs = $this->labModel->getActiveLabs($campId);
        }

        $data = [
            'title' => 'Computers Inventory',
            'active_menu' => 'computers',
            'computers' => $computers,
            'labs' => $labs
        ];

        $this->view('templates/header', $data);
        $this->view('equipment/computers', $data);
        $this->view('templates/footer');
    }

    /**
     * Add a new computer workstation
     */
    public function createComputer() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('equipment/computers');
            }

            $assetNo = strtoupper(trim($_POST['asset_no']));
            $serialNo = trim($_POST['serial_no']);
            $brand = trim($_POST['brand']);
            $model = trim($_POST['model']);
            $processor = trim($_POST['processor']);
            $ram = trim($_POST['ram']);
            $storage = trim($_POST['storage']);
            $os = trim($_POST['os']);
            $purchaseDate = trim($_POST['purchase_date']);
            $warrantyStatus = trim($_POST['warranty_status']);
            $labId = !empty($_POST['lab_id']) ? (int)$_POST['lab_id'] : null;
            $status = $_POST['status'] ?? 'active';

            // Verify lab camp ID if camp admin
            if ($labId && isCampAdmin()) {
                $lab = $this->labModel->getLabById($labId);
                if (!$lab || (int)$lab->camp_id !== (int)$_SESSION['camp_id']) {
                    flash('dashboard_error', 'Access denied. You can only assign computers to labs in your own camp.', 'alert alert-danger');
                    redirect('equipment/computers');
                }
            }

            // Check uniqueness of asset number
            if ($this->computerModel->checkAssetNoExists($assetNo)) {
                flash('dashboard_error', "Computer asset number '{$assetNo}' already exists in inventory.", 'alert alert-danger');
                redirect('equipment/computers');
            }

            $data = [
                'asset_no' => $assetNo,
                'serial_no' => $serialNo,
                'brand' => $brand,
                'model' => $model,
                'processor' => $processor,
                'ram' => $ram,
                'storage' => $storage,
                'os' => $os,
                'purchase_date' => $purchaseDate,
                'warranty_status' => $warrantyStatus,
                'lab_id' => $labId,
                'status' => $status
            ];

            if ($this->computerModel->createComputer($data)) {
                $this->logActivity('CREATE_COMPUTER', 'EQUIPMENT', "Added computer '{$assetNo}' to inventory.");
                flash('dashboard_success', 'Workstation registered successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to register workstation.', 'alert alert-danger');
            }
            redirect('equipment/computers');
        }
    }

    /**
     * Edit computer workstation details
     */
    public function updateComputer($id) {
        requireAdmin();

        $comp = $this->computerModel->getComputerById($id);
        if (!$comp) {
            flash('dashboard_error', 'Workstation not found.', 'alert alert-danger');
            redirect('equipment/computers');
        }

        // Camp isolation check
        if (isCampAdmin() && $comp->camp_id !== null && (int)$comp->camp_id !== (int)$_SESSION['camp_id']) {
            flash('dashboard_error', 'Access denied. You can only update workstations in your own camp.', 'alert alert-danger');
            redirect('equipment/computers');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('equipment/computers');
            }

            $assetNo = strtoupper(trim($_POST['asset_no']));
            $serialNo = trim($_POST['serial_no']);
            $brand = trim($_POST['brand']);
            $model = trim($_POST['model']);
            $processor = trim($_POST['processor']);
            $ram = trim($_POST['ram']);
            $storage = trim($_POST['storage']);
            $os = trim($_POST['os']);
            $purchaseDate = trim($_POST['purchase_date']);
            $warrantyStatus = trim($_POST['warranty_status']);
            $labId = !empty($_POST['lab_id']) ? (int)$_POST['lab_id'] : null;
            $status = $_POST['status'] ?? 'active';

            // Verify lab camp ID if camp admin
            if ($labId && isCampAdmin()) {
                $lab = $this->labModel->getLabById($labId);
                if (!$lab || (int)$lab->camp_id !== (int)$_SESSION['camp_id']) {
                    flash('dashboard_error', 'Access denied. You can only assign computers to labs in your own camp.', 'alert alert-danger');
                    redirect('equipment/computers');
                }
            }

            // Check duplicate asset numbers excluding self
            if ($this->computerModel->checkAssetNoExists($assetNo, $id)) {
                flash('dashboard_error', "Another computer workstation is already using asset number '{$assetNo}'.", 'alert alert-danger');
                redirect('equipment/computers');
            }

            $data = [
                'asset_no' => $assetNo,
                'serial_no' => $serialNo,
                'brand' => $brand,
                'model' => $model,
                'processor' => $processor,
                'ram' => $ram,
                'storage' => $storage,
                'os' => $os,
                'purchase_date' => $purchaseDate,
                'warranty_status' => $warrantyStatus,
                'lab_id' => $labId,
                'status' => $status
            ];

            if ($this->computerModel->updateComputer($id, $data)) {
                $this->logActivity('UPDATE_COMPUTER', 'EQUIPMENT', "Modified computer workstation specs: {$assetNo} (ID: {$id})");
                flash('dashboard_success', 'Workstation details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update workstation.', 'alert alert-danger');
            }
            redirect('equipment/computers');
        }
    }

    /**
     * Delete computer from inventory
     */
    public function deleteComputer($id) {
        requireAdmin();

        $comp = $this->computerModel->getComputerById($id);
        if (!$comp) {
            flash('dashboard_error', 'Workstation not found.', 'alert alert-danger');
            redirect('equipment/computers');
        }

        // Camp isolation check
        if (isCampAdmin() && $comp->camp_id !== null && (int)$comp->camp_id !== (int)$_SESSION['camp_id']) {
            flash('dashboard_error', 'Access denied. You can only delete workstations in your own camp.', 'alert alert-danger');
            redirect('equipment/computers');
        }

        if ($this->computerModel->deleteComputer($id)) {
            $this->logActivity('DELETE_COMPUTER', 'EQUIPMENT', "Deleted computer asset '{$comp->asset_no}' from inventory.");
            flash('dashboard_success', 'Workstation removed from inventory.', 'alert alert-success');
        } else {
            flash('dashboard_error', 'Failed to remove workstation.', 'alert alert-danger');
        }
        redirect('equipment/computers');
    }

    public function smartboards() {
        requireAdmin();

        if (isSuperAdmin()) {
            $smartboards = $this->smartBoardModel->getAllSmartBoards();
            $labs = $this->labModel->getActiveLabs();
        } else {
            $campId = $_SESSION['camp_id'];
            $smartboards = $this->smartBoardModel->getAllSmartBoards($campId);
            $labs = $this->labModel->getActiveLabs($campId);
        }

        $data = [
            'title' => 'Smart Boards Registry',
            'active_menu' => 'smartboards',
            'smartboards' => $smartboards,
            'labs' => $labs
        ];

        $this->view('templates/header', $data);
        $this->view('equipment/smartboards', $data);
        $this->view('templates/footer');
    }

    /**
     * Add a Smart Board
     */
    public function createSmartBoard() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('equipment/smartboards');
            }

            $assetId = strtoupper(trim($_POST['asset_id']));
            $brand = trim($_POST['brand']);
            $model = trim($_POST['model']);
            $installationDate = trim($_POST['installation_date']);
            $labId = !empty($_POST['lab_id']) ? (int)$_POST['lab_id'] : null;
            $status = $_POST['status'] ?? 'active';

            // Verify lab camp ID if camp admin
            if ($labId && isCampAdmin()) {
                $lab = $this->labModel->getLabById($labId);
                if (!$lab || (int)$lab->camp_id !== (int)$_SESSION['camp_id']) {
                    flash('dashboard_error', 'Access denied. You can only assign smart boards to labs in your own camp.', 'alert alert-danger');
                    redirect('equipment/smartboards');
                }
            }

            // Check duplicate asset ID
            if ($this->smartBoardModel->checkAssetIdExists($assetId)) {
                flash('dashboard_error', "Smart board asset ID '{$assetId}' already exists in inventory.", 'alert alert-danger');
                redirect('equipment/smartboards');
            }

            $data = [
                'asset_id' => $assetId,
                'brand' => $brand,
                'model' => $model,
                'installation_date' => $installationDate,
                'lab_id' => $labId,
                'status' => $status
            ];

            if ($this->smartBoardModel->createSmartBoard($data)) {
                $this->logActivity('CREATE_SMART_BOARD', 'EQUIPMENT', "Registered smart board device: {$assetId}");
                flash('dashboard_success', 'Smart board registered successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to register smart board.', 'alert alert-danger');
            }
            redirect('equipment/smartboards');
        }
    }

    /**
     * Edit Smart Board
     */
    public function updateSmartBoard($id) {
        requireAdmin();

        $sb = $this->smartBoardModel->getSmartBoardById($id);
        if (!$sb) {
            flash('dashboard_error', 'Smart board not found.', 'alert alert-danger');
            redirect('equipment/smartboards');
        }

        // Camp isolation check
        if (isCampAdmin() && $sb->camp_id !== null && (int)$sb->camp_id !== (int)$_SESSION['camp_id']) {
            flash('dashboard_error', 'Access denied. You can only update smart boards in your own camp.', 'alert alert-danger');
            redirect('equipment/smartboards');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('equipment/smartboards');
            }

            $assetId = strtoupper(trim($_POST['asset_id']));
            $brand = trim($_POST['brand']);
            $model = trim($_POST['model']);
            $installationDate = trim($_POST['installation_date']);
            $labId = !empty($_POST['lab_id']) ? (int)$_POST['lab_id'] : null;
            $status = $_POST['status'] ?? 'active';

            // Verify lab camp ID if camp admin
            if ($labId && isCampAdmin()) {
                $lab = $this->labModel->getLabById($labId);
                if (!$lab || (int)$lab->camp_id !== (int)$_SESSION['camp_id']) {
                    flash('dashboard_error', 'Access denied. You can only assign smart boards to labs in your own camp.', 'alert alert-danger');
                    redirect('equipment/smartboards');
                }
            }

            // Check duplicate ID excluding self
            if ($this->smartBoardModel->checkAssetIdExists($assetId, $id)) {
                flash('dashboard_error', "Another smart board is using asset ID '{$assetId}'.", 'alert alert-danger');
                redirect('equipment/smartboards');
            }

            $data = [
                'asset_id' => $assetId,
                'brand' => $brand,
                'model' => $model,
                'installation_date' => $installationDate,
                'lab_id' => $labId,
                'status' => $status
            ];

            if ($this->smartBoardModel->updateSmartBoard($id, $data)) {
                $this->logActivity('UPDATE_SMART_BOARD', 'EQUIPMENT', "Updated smart board: {$assetId} (ID: {$id})");
                flash('dashboard_success', 'Smart board details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update smart board.', 'alert alert-danger');
            }
            redirect('equipment/smartboards');
        }
    }

    /**
     * Delete Smart Board
     */
    public function deleteSmartBoard($id) {
        requireAdmin();

        $sb = $this->smartBoardModel->getSmartBoardById($id);
        if (!$sb) {
            flash('dashboard_error', 'Smart board not found.', 'alert alert-danger');
            redirect('equipment/smartboards');
        }

        // Camp isolation check
        if (isCampAdmin() && $sb->camp_id !== null && (int)$sb->camp_id !== (int)$_SESSION['camp_id']) {
            flash('dashboard_error', 'Access denied. You can only delete smart boards in your own camp.', 'alert alert-danger');
            redirect('equipment/smartboards');
        }

        if ($this->smartBoardModel->deleteSmartBoard($id)) {
            $this->logActivity('DELETE_SMART_BOARD', 'EQUIPMENT', "Removed smart board '{$sb->asset_id}' from registry.");
            flash('dashboard_success', 'Smart board removed from registry.', 'alert alert-success');
        } else {
            flash('dashboard_error', 'Failed to remove smart board.', 'alert alert-danger');
        }
        redirect('equipment/smartboards');
    }
}
