<?php
/**
 * Allocation Controller Class
 * Handles conflict checking, lab scheduling allocations, and FullCalendar integration.
 */
class AllocationController extends Controller {
    private $allocModel;
    private $instructorModel;
    private $labModel;
    private $lessonModel;

    public function __construct() {
        requireLogin();
        $this->allocModel = $this->model('AllocationModel');
        $this->instructorModel = $this->model('InstructorModel');
        $this->labModel = $this->model('LabModel');
        $this->lessonModel = $this->model('LessonModel');
    }

    /**
     * Scheduling allocation list & CRUD page
     */
    public function schedule() {
        requireAdmin();

        $allocations = $this->allocModel->getAllAllocations();
        $instructors = $this->instructorModel->getActiveInstructors();
        $labs = $this->labModel->getActiveLabs();
        $lessons = $this->lessonModel->getAllLessons();

        $data = [
            'title' => 'Lab Allocations',
            'active_menu' => 'schedule',
            'allocations' => $allocations,
            'instructors' => $instructors,
            'labs' => $labs,
            'lessons' => $lessons
        ];

        $this->view('templates/header', $data);
        $this->view('allocations/schedule', $data);
        $this->view('templates/footer');
    }

    /**
     * Interactive Calendar dashboard
     */
    public function calendar() {
        // Accessible by both admin and instructors
        $instructors = $this->instructorModel->getActiveInstructors();
        $labs = $this->labModel->getActiveLabs();
        $lessons = $this->lessonModel->getAllLessons();

        $data = [
            'title' => 'School Scheduling Calendar',
            'active_menu' => 'calendar',
            'instructors' => $instructors,
            'labs' => $labs,
            'lessons' => $lessons
        ];

        $this->view('templates/header', $data);
        $this->view('allocations/calendar', $data);
        $this->view('templates/footer');
    }

    /**
     * Create new allocation booking
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('allocation/schedule');
            }

            $instructorId = (int)$_POST['instructor_id'];
            $lessonId = (int)$_POST['lesson_id'];
            $labId = (int)$_POST['lab_id'];
            $date = trim($_POST['date']);
            $startTime = trim($_POST['start_time']);
            $endTime = trim($_POST['end_time']);
            $remarks = trim($_POST['remarks']);

            // 1. Validate inputs time logic
            if (strtotime($startTime) >= strtotime($endTime)) {
                flash('dashboard_error', 'Start time must be before end time.', 'alert alert-danger');
                redirect('allocation/schedule');
            }

            // 2. Perform Conflict Check (overlap rules)
            $conflicts = $this->allocModel->checkConflicts($date, $startTime, $endTime, $labId, $instructorId);
            if (!empty($conflicts)) {
                $errorMsg = '<strong>Scheduling conflicts detected:</strong><ul>';
                foreach ($conflicts as $err) {
                    $errorMsg .= '<li>' . e($err) . '</li>';
                }
                $errorMsg .= '</ul>';
                flash('dashboard_error', $errorMsg, 'alert alert-danger');
                redirect('allocation/schedule');
            }

            $data = [
                'instructor_id' => $instructorId,
                'lesson_id' => $lessonId,
                'lab_id' => $labId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'remarks' => $remarks
            ];

            $allocId = $this->allocModel->createAllocation($data);
            if ($allocId) {
                // Send notification to instructor
                $inst = $this->instructorModel->getInstructorById($instructorId);
                $lab = $this->labModel->getLabById($labId);
                $lesson = $this->lessonModel->getLessonById($lessonId);
                
                if ($inst && $inst->user_id) {
                    $notifModel = $this->model('NotificationModel');
                    $msg = "Scheduled for '{$lesson->lesson_code} - {$lesson->lesson_name}' in Laboratory '{$lab->lab_code}' on " . date('d M Y', strtotime($date)) . " from {$startTime} to {$endTime}.";
                    $notifModel->createNotification($inst->user_id, $msg, 'schedule', $allocId);
                }

                $this->logActivity('CREATE_ALLOCATION', 'ALLOCATIONS', "Booked lab {$lab->lab_code} for instructor {$inst->rank} {$inst->full_name} on {$date} ({$startTime}-{$endTime})");
                flash('dashboard_success', 'Laboratory allocated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to allocate laboratory.', 'alert alert-danger');
            }
            redirect('allocation/schedule');
        }
    }

    /**
     * Update an allocation
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('allocation/schedule');
            }

            $instructorId = (int)$_POST['instructor_id'];
            $lessonId = (int)$_POST['lesson_id'];
            $labId = (int)$_POST['lab_id'];
            $date = trim($_POST['date']);
            $startTime = trim($_POST['start_time']);
            $endTime = trim($_POST['end_time']);
            $remarks = trim($_POST['remarks']);

            if (strtotime($startTime) >= strtotime($endTime)) {
                flash('dashboard_error', 'Start time must be before end time.', 'alert alert-danger');
                redirect('allocation/schedule');
            }

            // Conflict check excluding current allocation
            $conflicts = $this->allocModel->checkConflicts($date, $startTime, $endTime, $labId, $instructorId, $id);
            if (!empty($conflicts)) {
                $errorMsg = '<strong>Scheduling conflicts detected:</strong><ul>';
                foreach ($conflicts as $err) {
                    $errorMsg .= '<li>' . e($err) . '</li>';
                }
                $errorMsg .= '</ul>';
                flash('dashboard_error', $errorMsg, 'alert alert-danger');
                redirect('allocation/schedule');
            }

            $data = [
                'instructor_id' => $instructorId,
                'lesson_id' => $lessonId,
                'lab_id' => $labId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'remarks' => $remarks
            ];

            if ($this->allocModel->updateAllocation($id, $data)) {
                // Notify instructor
                $inst = $this->instructorModel->getInstructorById($instructorId);
                $lab = $this->labModel->getLabById($labId);
                
                if ($inst && $inst->user_id) {
                    $notifModel = $this->model('NotificationModel');
                    $msg = "Schedule update: Laboratory allocation '{$lab->lab_code}' rescheduled to " . date('d M Y', strtotime($date)) . " from {$startTime} to {$endTime}.";
                    $notifModel->createNotification($inst->user_id, $msg, 'schedule', $id);
                }

                $this->logActivity('UPDATE_ALLOCATION', 'ALLOCATIONS', "Modified allocation ID: {$id} on {$date} ({$startTime}-{$endTime})");
                flash('dashboard_success', 'Allocation details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update allocation details.', 'alert alert-danger');
            }
            redirect('allocation/schedule');
        }
    }

    /**
     * Delete/Cancel an allocation
     */
    public function delete($id) {
        requireAdmin();

        $alloc = $this->allocModel->getAllocationById($id);
        if ($alloc) {
            if ($this->allocModel->deleteAllocation($id)) {
                // Notify instructor of cancellation
                if ($alloc->instructor_user_id) {
                    $notifModel = $this->model('NotificationModel');
                    $msg = "Schedule Cancelled: Your booking for '{$alloc->lesson_code}' in laboratory '{$alloc->lab_code}' on " . date('d M Y', strtotime($alloc->date)) . " has been cancelled.";
                    $notifModel->createNotification($alloc->instructor_user_id, $msg, 'cancellation');
                }

                $this->logActivity('DELETE_ALLOCATION', 'ALLOCATIONS', "Cancelled allocation: {$alloc->lesson_code} in {$alloc->lab_code} on {$alloc->date}");
                flash('dashboard_success', 'Allocation booking cancelled successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to cancel allocation.', 'alert alert-danger');
            }
        }
        redirect('allocation/schedule');
    }

    /**
     * API: Get range events for FullCalendar (JSON Output)
     */
    public function getCalendarEvents() {
        $start = filter_input(INPUT_GET, 'start', FILTER_DEFAULT);
        $end = filter_input(INPUT_GET, 'end', FILTER_DEFAULT);

        if (!$start || !$end) {
            $this->json(['error' => 'Missing date range parameters.'], 400);
            return;
        }

        // Standardize formats (FullCalendar gives ISO8601 strings)
        $startDate = date('Y-m-d', strtotime($start));
        $endDate = date('Y-m-d', strtotime($end));

        $allocations = $this->allocModel->getAllocationsByDateRange($startDate, $endDate);
        
        $events = [];
        // Map beautiful military hues to labs dynamically
        $colors = [
            '1' => '#1d3557', // Dark navy blue
            '2' => '#457b9d', // Air force steel blue
            '3' => '#2a9d8f', // Teal
            '4' => '#7209b7', // Purple
            '5' => '#c1121f', // Red
            '6' => '#d4a373'  // Tan
        ];

        foreach ($allocations as $a) {
            $color = $colors[$a->lab_id] ?? '#457b9d';
            
            $events[] = [
                'id' => $a->id,
                'title' => "{$a->lab_code} - {$a->lesson_code} ({$a->instructor_rank} {$a->instructor_name})",
                'start' => "{$a->date}T{$a->start_time}",
                'end' => "{$a->date}T{$a->end_time}",
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'labName' => $a->lab_name,
                    'labCode' => $a->lab_code,
                    'labId' => $a->lab_id,
                    'instructorName' => "{$a->instructor_rank} {$a->instructor_name}",
                    'instructorId' => $a->instructor_id,
                    'lessonName' => $a->lesson_name,
                    'lessonCode' => $a->lesson_code,
                    'lessonId' => $a->lesson_id,
                    'remarks' => $a->remarks ?: 'None',
                    'sessionStatus' => $a->session_status,
                    'instructorRemarks' => $a->instructor_remarks ?: ''
                ]
            ];
        }

        $this->json($events);
    }

    /**
     * API: Move Allocation Event (Drag-and-drop or resize in FullCalendar)
     */
    public function apiMoveEvent() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get JSON input body
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);

            if (!$json) {
                $this->json(['success' => false, 'message' => 'Malformed request parameters.'], 400);
                return;
            }

            // Verify CSRF via request header (Central AJAX wrapper passes it as X-CSRF-TOKEN)
            $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!verifyCsrfToken($csrfToken)) {
                $this->json(['success' => false, 'message' => 'Security validation token mismatch.'], 403);
                return;
            }

            $id = (int)($json['id'] ?? 0);
            $newDate = date('Y-m-d', strtotime($json['start']));
            $newStartTime = date('H:i:s', strtotime($json['start']));
            $newEndTime = date('H:i:s', strtotime($json['end']));

            // Fetch current allocation details
            $alloc = $this->allocModel->getAllocationById($id);
            if (!$alloc) {
                $this->json(['success' => false, 'message' => 'Laboratory allocation booking not found.'], 404);
                return;
            }

            // Conflict checking
            $conflicts = $this->allocModel->checkConflicts($newDate, $newStartTime, $newEndTime, $alloc->lab_id, $alloc->instructor_id, $id);
            if (!empty($conflicts)) {
                $msg = 'Overlapping conflict: ' . implode(' | ', $conflicts);
                $this->json(['success' => false, 'message' => $msg]);
                return;
            }

            // Update allocation
            $data = [
                'instructor_id' => $alloc->instructor_id,
                'lesson_id' => $alloc->lesson_id,
                'lab_id' => $alloc->lab_id,
                'date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime,
                'remarks' => $alloc->remarks
            ];

            if ($this->allocModel->updateAllocation($id, $data)) {
                // Notify instructor
                if ($alloc->instructor_user_id) {
                    $notifModel = $this->model('NotificationModel');
                    $msg = "Schedule update: Your slot for '{$alloc->lesson_code}' in lab '{$alloc->lab_code}' was moved to " . date('d M Y', strtotime($newDate)) . " at {$newStartTime}-{$newEndTime}.";
                    $notifModel->createNotification($alloc->instructor_user_id, $msg, 'schedule', $id);
                }

                $this->logActivity('DRAG_ALLOCATION', 'ALLOCATIONS', "FullCalendar moved allocation ID {$id} to {$newDate} ({$newStartTime}-{$newEndTime})");
                $this->json(['success' => true, 'message' => 'Allocation updated successfully.']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to save changes.']);
            }
        }
    }

    /**
     * Show instructor's allocated schedule (Pending Completion & Upcoming)
     */
    public function mySchedule() {
        requireInstructor();
        $instructorId = $_SESSION['instructor_id'] ?? null;
        if (!$instructorId) {
            flash('dashboard_error', 'Your user account is not associated with an instructor profile.', 'alert alert-danger');
            redirect('dashboard/instructor');
        }

        $pending = $this->allocModel->getPendingCompletionSessions($instructorId);
        $completed = $this->allocModel->getRecentlyCompletedSessions($instructorId, 10);
        $upcoming = $this->allocModel->getUpcomingSessionsForInstructor($instructorId, 20);

        $data = [
            'title' => 'My Schedule',
            'active_menu' => 'my_schedule',
            'pending_sessions' => $pending,
            'completed_sessions' => $completed,
            'upcoming_sessions' => $upcoming
        ];

        $this->view('templates/header', $data);
        $this->view('allocations/my_schedule', $data);
        $this->view('templates/footer');
    }

    /**
     * Complete Session and add remarks (Instructor action)
     */
    public function complete($id) {
        requireInstructor();
        $instructorId = $_SESSION['instructor_id'] ?? null;
        if (!$instructorId) {
            flash('dashboard_error', 'Access denied. Instructor profile missing.', 'alert alert-danger');
            redirect('dashboard/instructor');
        }

        $alloc = $this->allocModel->getAllocationById($id);
        if (!$alloc || (int)$alloc->instructor_id !== (int)$instructorId) {
            flash('dashboard_error', 'Invalid allocation or access denied.', 'alert alert-danger');
            redirect('allocation/mySchedule');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('allocation/complete/' . $id);
            }

            $status = trim($_POST['session_status'] ?? '');
            $remarks = trim($_POST['instructor_remarks'] ?? '');

            // Validation
            $allowedStatuses = ['Completed Successfully', 'Partially Completed', 'Cancelled'];
            if (!in_array($status, $allowedStatuses)) {
                flash('dashboard_error', 'Please select a valid session status.', 'alert alert-danger');
                redirect('allocation/complete/' . $id);
            }

            if (strlen($remarks) > 1000) {
                flash('dashboard_error', 'Remarks must be less than 1000 characters.', 'alert alert-danger');
                redirect('allocation/complete/' . $id);
            }

            if ($this->allocModel->completeSession($id, $status, $remarks, $_SESSION['user_id'])) {
                $this->logActivity('COMPLETE_SESSION', 'ALLOCATIONS', "Completed session ID {$id} with status '{$status}'.");
                flash('dashboard_success', 'Session completion logged successfully.', 'alert alert-success');
                redirect('allocation/mySchedule');
            } else {
                flash('dashboard_error', 'Failed to save session completion state.', 'alert alert-danger');
                redirect('allocation/complete/' . $id);
            }
        } else {
            $data = [
                'title' => 'Complete Session',
                'active_menu' => 'my_schedule',
                'alloc' => $alloc
            ];

            $this->view('templates/header', $data);
            $this->view('allocations/complete', $data);
            $this->view('templates/footer');
        }
    }

    /**
     * View instructor's completed, cancelled, and rescheduled session history
     */
    public function myHistory() {
        requireInstructor();
        $instructorId = $_SESSION['instructor_id'] ?? null;
        if (!$instructorId) {
            flash('dashboard_error', 'Your user account is not associated with an instructor profile.', 'alert alert-danger');
            redirect('dashboard/instructor');
        }

        $history = $this->allocModel->getInstructorSessionHistory($instructorId);

        $data = [
            'title' => 'My Session History',
            'active_menu' => 'my_history',
            'history' => $history
        ];

        $this->view('templates/header', $data);
        $this->view('allocations/my_history', $data);
        $this->view('templates/footer');
    }
}
