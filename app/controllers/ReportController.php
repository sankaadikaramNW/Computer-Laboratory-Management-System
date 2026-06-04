<?php
/**
 * Report Controller Class
 * Generates audit reports, inventory listings, workload logs, and lab room utilization statistics.
 */
class ReportController extends Controller {
    private $computerModel;
    private $smartBoardModel;
    private $allocModel;
    private $labModel;

    public function __construct() {
        requireLogin();
        requireAdmin();
        $this->computerModel = $this->model('ComputerModel');
        $this->smartBoardModel = $this->model('SmartBoardModel');
        $this->allocModel = $this->model('AllocationModel');
        $this->labModel = $this->model('LabModel');
    }

    /**
     * Report landing dashboard
     */
    public function index() {
        $data = [
            'title' => 'System Reports Dashboard',
            'active_menu' => 'reports'
        ];

        $this->view('templates/header', $data);
        $this->view('reports/index', $data);
        $this->view('templates/footer');
    }

    /**
     * hardware & workstation inventory report
     */
    public function inventory() {
        $computers = $this->computerModel->getAllComputers();
        $smartboards = $this->smartBoardModel->getAllSmartBoards();

        $data = [
            'title' => 'Hardware Inventory Audit Report',
            'active_menu' => 'reports',
            'computers' => $computers,
            'smartboards' => $smartboards
        ];

        $this->view('templates/header', $data);
        $this->view('reports/inventory', $data);
        $this->view('templates/footer');
    }

    /**
     * Instructor workload report
     */
    public function workload() {
        $workloads = $this->allocModel->getInstructorWorkloadStats();

        $data = [
            'title' => 'Instructor Workload Audit Report',
            'active_menu' => 'reports',
            'workloads' => $workloads
        ];

        $this->view('templates/header', $data);
        $this->view('reports/workload', $data);
        $this->view('templates/footer');
    }

    /**
     * Laboratory utilization report
     */
    public function utilization() {
        // Use the proper model method to get lab utilization stats
        $utilization = $this->labModel->getLabUtilizationStats();

        $data = [
            'title' => 'Laboratory Room Utilization Report',
            'active_menu' => 'reports',
            'utilization' => $utilization
        ];

        $this->view('templates/header', $data);
        $this->view('reports/utilization', $data);
        $this->view('templates/footer');
    }
}
