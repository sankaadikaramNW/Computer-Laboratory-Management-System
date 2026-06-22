<?php
/**
 * Request Controller Class
 * Handles rescheduling, cancellation, and lab transfer requests from instructors.
 */
class RequestController extends Controller {
    private $requestModel;
    private $allocModel;
    private $labModel;
    private $notifModel;

    public function __construct() {
        requireLogin();
        $this->requestModel = $this->model('RequestModel');
        $this->allocModel = $this->model('AllocationModel');
        $this->labModel = $this->model('LabModel');
        $this->notifModel = $this->model('NotificationModel');
    }

    /**
     * Admin view list & review panel
     */
    public function index() {
        requireAdmin();

        if (isSuperAdmin()) {
            $requests = $this->requestModel->getAllRequests();
        } else {
            $campId = $_SESSION['camp_id'];
            $requests = $this->requestModel->getAllRequests($campId);
        }
        
        $data = [
            'title' => 'Manage Change Requests',
            'active_menu' => 'requests',
            'requests' => $requests
        ];

        $this->view('templates/header', $data);
        $this->view('requests/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Instructor view request list & submission forms
     */
    public function instructor() {
        requireInstructor();

        $userId = $_SESSION['user_id'];
        $instructorId = $_SESSION['instructor_id'] ?? null;

        if (!$instructorId) {
            flash('dashboard_error', 'Your account is not linked to an instructor profile.', 'alert alert-danger');
            redirect('dashboard/instructor');
        }

        // Fetch instructor allocations
        $allocations = $this->allocModel->getAllocationsByInstructor($instructorId);
        $labs = $this->labModel->getActiveLabs();
        $requests = $this->requestModel->getRequestsByRequester($userId);

        $data = [
            'title' => 'My Change Requests',
            'active_menu' => 'requests',
            'allocations' => $allocations,
            'labs' => $labs,
            'requests' => $requests
        ];

        $this->view('templates/header', $data);
        $this->view('requests/instructor', $data);
        $this->view('templates/footer');
    }

    /**
     * Submit a new change request (Instructor action)
     */
    public function create() {
        requireInstructor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('request/instructor');
            }

            $allocationId = (int)$_POST['allocation_id'];
            $type = trim($_POST['type']); // reschedule, cancel, change_lab
            $reason = trim($_POST['reason']);
            
            $newDate = !empty($_POST['new_date']) ? trim($_POST['new_date']) : null;
            $newStartTime = !empty($_POST['new_start_time']) ? trim($_POST['new_start_time']) : null;
            $newEndTime = !empty($_POST['new_end_time']) ? trim($_POST['new_end_time']) : null;
            $newLabId = !empty($_POST['new_lab_id']) ? (int)$_POST['new_lab_id'] : null;

            // Fetch the allocation details to make sure instructor owns it
            $alloc = $this->allocModel->getAllocationById($allocationId);
            if (!$alloc || (int)$alloc->instructor_id !== (int)$_SESSION['instructor_id']) {
                flash('dashboard_error', 'Invalid allocation selection.', 'alert alert-danger');
                redirect('request/instructor');
            }

            // Validate that reschedule request is not for a past date/time
            if ($type === 'reschedule') {
                if (strtotime($newDate . ' ' . $newStartTime) < time()) {
                    flash('dashboard_error', 'Cannot request a reschedule to a past date or time.', 'alert alert-danger');
                    redirect('request/instructor');
                }
            }

            $data = [
                'allocation_id' => $allocationId,
                'requester_id' => $_SESSION['user_id'],
                'type' => $type,
                'new_date' => $newDate,
                'new_start_time' => $newStartTime,
                'new_end_time' => $newEndTime,
                'new_lab_id' => $newLabId,
                'reason' => $reason
            ];

            if ($this->requestModel->createRequest($data)) {
                $this->logActivity('SUBMIT_REQUEST', 'REQUESTS', "Submitted {$type} request for allocation ID {$allocationId}");
                flash('dashboard_success', 'Change request submitted successfully. Awaiting administrator review.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to submit change request.', 'alert alert-danger');
            }
            redirect('request/instructor');
        }
    }

    /**
     * Review / Approve / Reject Change Request (Admin action)
     */
    public function review($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('request');
            }

            $status = trim($_POST['status']); // approved, rejected
            $remarks = trim($_POST['remarks']);

            $req = $this->requestModel->getRequestById($id);
            if (!$req || $req->status !== 'pending') {
                flash('dashboard_error', 'Invalid or already reviewed change request.', 'alert alert-danger');
                redirect('request');
            }

            // Camp admin access isolation check
            if (isCampAdmin() && (int)$req->camp_id !== (int)$_SESSION['camp_id']) {
                flash('dashboard_error', 'Access denied. You can only review requests in your own camp.', 'alert alert-danger');
                redirect('request');
            }

            if ($status === 'approved') {
                // Fetch the actual booking
                $alloc = $this->allocModel->getAllocationById($req->allocation_id);
                if (!$alloc) {
                    flash('dashboard_error', 'The referenced laboratory allocation booking no longer exists.', 'alert alert-danger');
                    redirect('request');
                }

                // Execute modification based on request type
                if ($req->type === 'cancel') {
                    // Delete booking slot
                    $this->allocModel->deleteAllocation($req->allocation_id);
                } 
                elseif ($req->type === 'reschedule') {
                    // Check conflicts on new slots
                    $conflicts = $this->allocModel->checkConflicts($req->new_date, $req->new_start_time, $req->new_end_time, $alloc->lab_id, $alloc->instructor_id, $alloc->id);
                    if (!empty($conflicts)) {
                        $msg = 'Failed to approve. Rescheduled slot overlaps with: ' . implode(', ', $conflicts);
                        flash('dashboard_error', $msg, 'alert alert-danger');
                        redirect('request');
                    }

                    // Perform update
                    $data = [
                        'instructor_id' => $alloc->instructor_id,
                        'lesson_id' => $alloc->lesson_id,
                        'lab_id' => $alloc->lab_id,
                        'date' => $req->new_date,
                        'start_time' => $req->new_start_time,
                        'end_time' => $req->new_end_time,
                        'remarks' => $alloc->remarks . " (Rescheduled via Request #{$id})"
                    ];
                    $this->allocModel->updateAllocation($req->allocation_id, $data);
                } 
                elseif ($req->type === 'change_lab') {
                    // Check conflicts for the new lab on old date/time
                    $conflicts = $this->allocModel->checkConflicts($alloc->date, $alloc->start_time, $alloc->end_time, $req->new_lab_id, $alloc->instructor_id, $alloc->id);
                    if (!empty($conflicts)) {
                        $msg = 'Failed to approve. The requested lab slot overlaps with: ' . implode(', ', $conflicts);
                        flash('dashboard_error', $msg, 'alert alert-danger');
                        redirect('request');
                    }

                    // Perform update
                    $data = [
                        'instructor_id' => $alloc->instructor_id,
                        'lesson_id' => $alloc->lesson_id,
                        'lab_id' => $req->new_lab_id,
                        'date' => $alloc->date,
                        'start_time' => $alloc->start_time,
                        'end_time' => $alloc->end_time,
                        'remarks' => $alloc->remarks . " (Lab Changed via Request #{$id})"
                    ];
                    $this->allocModel->updateAllocation($req->allocation_id, $data);
                }
            }

            // Save review state
            if ($this->requestModel->updateRequestStatus($id, $status, $remarks)) {
                // Send notification to requesting instructor
                $notifMsg = "Your {$req->type} request for '{$req->lesson_name}' was {$status} by Admin. Remarks: {$remarks}";
                $this->notifModel->createNotification($req->requester_id, $notifMsg, 'request_update', $id);

                $this->logActivity('REVIEW_REQUEST', 'REQUESTS', "Reviewed request ID: {$id} as {$status}. Remarks: {$remarks}");
                flash('dashboard_success', "Change request status set to {$status}.", 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update request state.', 'alert alert-danger');
            }
            redirect('request');
        }
    }
}
