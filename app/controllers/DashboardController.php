<?php
/**
 * Dashboard Controller Class
 * Displays statistics, notices, alerts, and aggregates resource statuses.
 */
class DashboardController extends Controller {
    public function __construct() {
        requireLogin();
        
        // Refresh session notification metrics
        $this->updateSessionCounters();
    }

    /**
     * Helper to update notification and request badges in session
     */
    private function updateSessionCounters() {
        $reqModel = $this->model('RequestModel');
        $faultModel = $this->model('FaultModel');
        $_SESSION['pending_requests_count'] = $reqModel->getPendingRequestsCount();
        $_SESSION['pending_faults_count'] = $faultModel->getPendingFaultsCount();
    }

    /**
     * Admin Dashboard
     */
    public function admin() {
        requireAdmin();

        // Load models
        $labModel = $this->model('LabModel');
        $compModel = $this->model('ComputerModel');
        $sbModel = $this->model('SmartBoardModel');
        $instModel = $this->model('InstructorModel');
        $allocModel = $this->model('AllocationModel');
        $reqModel = $this->model('RequestModel');
        $faultModel = $this->model('FaultModel');
        $maintModel = $this->model('MaintenanceModel');
        $noticeModel = $this->model('NoticeModel');
        $auditModel = $this->model('AuditModel');

        // Compile counts
        $data = [
            'title' => 'Admin Dashboard',
            'active_menu' => 'dashboard',
            'total_labs' => count($labModel->getAllLabs()),
            'total_computers' => count($compModel->getAllComputers()),
            'total_smartboards' => count($sbModel->getAllSmartBoards()),
            'total_instructors' => count($instModel->getAllInstructors()),
            'sessions_today' => $allocModel->getActiveSessionsToday(),
            'pending_requests' => $reqModel->getPendingRequestsCount(),
            'pending_faults' => $faultModel->getPendingFaultsCount(),
            'pending_maintenance' => $maintModel->getPendingMaintenanceCount(),
            'upcoming_sessions' => $allocModel->getUpcomingSessions(5),
            'recent_notices' => $noticeModel->getActiveNotices(),
            'recent_logs' => array_slice($auditModel->getAllLogs(), 0, 8),
            'utilization' => $labModel->getLabUtilizationStats()
        ];

        $this->view('templates/header', $data);
        $this->view('dashboard/admin', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Instructor Dashboard
     */
    public function instructor() {
        requireInstructor();

        $allocModel = $this->model('AllocationModel');
        $noticeModel = $this->model('NoticeModel');
        $reqModel = $this->model('RequestModel');
        $faultModel = $this->model('FaultModel');
        $notifModel = $this->model('NotificationModel');

        $instructorId = $_SESSION['instructor_id'] ?? null;
        $userId = $_SESSION['user_id'];

        if (!$instructorId) {
            flash('dashboard_error', 'Your user account is not linked to an instructor profile. Please contact the administrator.', 'alert alert-danger');
            $this->view('templates/header', ['title' => 'Instructor Dashboard', 'active_menu' => 'dashboard']);
            $this->view('dashboard/instructor', [
                'today_sessions' => [], 'upcoming_sessions' => [], 'active_notices' => [],
                'my_requests' => [], 'my_faults' => [], 'stats' => null, 'week_sessions' => [],
                'pending_requests_count' => 0, 'unread_notifs' => 0, 'next_session' => null
            ]);
            $this->view('templates/footer');
            return;
        }

        // Fetch instructor allocations
        $allAllocations = $allocModel->getAllocationsByInstructor($instructorId);
        
        // Filter Today's and Upcoming sessions
        $today = date('Y-m-d');
        $todaySessions = [];
        $upcomingSessions = [];
        $nextSession = null;

        foreach ($allAllocations as $a) {
            if ($a->date === $today) {
                $todaySessions[] = $a;
            } elseif ($a->date > $today) {
                $upcomingSessions[] = $a;
                if (!$nextSession) {
                    $nextSession = $a; // First upcoming = next session
                }
            }
        }

        // Get stats summary
        $stats = $allocModel->getInstructorStats($instructorId);

        // Get this week's schedule (Mon–Sun)
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $weekSessions = $allocModel->getWeekAllocations($instructorId, $weekStart, $weekEnd);

        // Pending requests count
        $allRequests = $reqModel->getRequestsByRequester($userId);
        $pendingRequests = 0;
        foreach ($allRequests as $r) {
            if ($r->status === 'pending') $pendingRequests++;
        }

        $data = [
            'title' => 'Instructor Dashboard',
            'active_menu' => 'dashboard',
            'today_sessions' => $todaySessions,
            'upcoming_sessions' => array_slice($upcomingSessions, 0, 5),
            'active_notices' => $noticeModel->getActiveNotices(),
            'my_requests' => array_slice($allRequests, 0, 5),
            'my_faults' => array_slice($faultModel->getFaultsByReporter($userId), 0, 5),
            'stats' => $stats,
            'week_sessions' => $weekSessions,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'pending_requests_count' => $pendingRequests,
            'unread_notifs' => $notifModel->getUnreadCount($userId),
            'next_session' => $nextSession
        ];

        $this->view('templates/header', $data);
        $this->view('dashboard/instructor', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Mark all notifications as read for current user
     */
    public function clearNotifications() {
        $notifModel = $this->model('NotificationModel');
        $notifModel->markAllAsRead($_SESSION['user_id']);
        
        flash('dashboard_success', 'All notifications cleared.', 'alert alert-success alert-dismissible fade show');
        
        // Redirect back to referring page or dashboard
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            redirect('dashboard/admin');
        }
        exit();
    }
}
