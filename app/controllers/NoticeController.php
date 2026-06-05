<?php
/**
 * Notice Controller Class
 * Handles notice board updates, announcements publishing, and archiving.
 */
class NoticeController extends Controller {
    private $noticeModel;
    private $notifModel;
    private $userModel;

    public function __construct() {
        requireLogin();
        $this->noticeModel = $this->model('NoticeModel');
        $this->notifModel  = $this->model('NotificationModel');
        $this->userModel   = $this->model('UserModel');
    }

    /**
     * Fan out a notification to all active instructor users for a given notice.
     * Skips any user who already received a notification for this notice.
     */
    private function notifyInstructors($noticeId, $title) {
        $instructors = $this->userModel->getAllInstructorUsers();
        $message = "📢 New Announcement: \"{$title}\"";
        foreach ($instructors as $instructor) {
            if (!$this->notifModel->noticeAlreadyNotified($noticeId, $instructor->id)) {
                $this->notifModel->createNotification($instructor->id, $message, 'NOTICE', $noticeId);
            }
        }
    }

    /**
     * Display notices configuration page
     */
    public function index() {
        requireAdmin();

        $notices = $this->noticeModel->getAllNotices();
        
        $data = [
            'title'       => 'Notice Board Announcements',
            'active_menu' => 'notices',
            'notices'     => $notices
        ];

        $this->view('templates/header', $data);
        $this->view('notices/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create Notice — and notify all instructors if status is active
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('notice');
            }

            $title   = trim($_POST['title']);
            $content = trim($_POST['content']);
            $status  = $_POST['status'] ?? 'active';

            $data = [
                'title'        => $title,
                'content'      => $content,
                'published_by' => $_SESSION['user_id'],
                'status'       => $status
            ];

            $noticeId = $this->noticeModel->createNotice($data);
            if ($noticeId) {
                $this->logActivity('CREATE_NOTICE', 'NOTICES', "Published announcement: '{$title}'");

                // Fan out notification to every active instructor if notice is active
                if ($status === 'active') {
                    $this->notifyInstructors($noticeId, $title);
                }

                flash('dashboard_success', 'Notice published on dashboard board.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to publish notice.', 'alert alert-danger');
            }
            redirect('notice');
        }
    }

    /**
     * Update Notice details — notify instructors if being set to active for the first time
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('notice');
            }

            $title   = trim($_POST['title']);
            $content = trim($_POST['content']);
            $status  = $_POST['status'] ?? 'active';

            $data = [
                'title'   => $title,
                'content' => $content,
                'status'  => $status
            ];

            if ($this->noticeModel->updateNotice($id, $data)) {
                $this->logActivity('UPDATE_NOTICE', 'NOTICES', "Modified announcement ID: {$id} - {$title}");

                // If admin is activating/re-publishing the notice, notify any instructor who hasn't seen it yet
                if ($status === 'active') {
                    $this->notifyInstructors($id, $title);
                }

                flash('dashboard_success', 'Notice announcement modified.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to save announcement changes.', 'alert alert-danger');
            }
            redirect('notice');
        }
    }

    /**
     * Delete Notice
     */
    public function delete($id) {
        requireAdmin();

        $notice = $this->noticeModel->getNoticeById($id);
        if ($notice) {
            if ($this->noticeModel->deleteNotice($id)) {
                $this->logActivity('DELETE_NOTICE', 'NOTICES', "Deleted announcement ID: {$id}");
                flash('dashboard_success', 'Notice removed from board.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to remove notice.', 'alert alert-danger');
            }
        }
        redirect('notice');
    }

    /**
     * Manually push notifications for an existing active notice to all instructors.
     * Used for notices that existed before the auto-fanout feature was added.
     */
    public function notify($id) {
        requireAdmin();

        $notice = $this->noticeModel->getNoticeById($id);
        if (!$notice || $notice->status !== 'active') {
            flash('dashboard_error', 'Notice not found or not active.', 'alert alert-danger');
            redirect('notice');
        }

        $this->notifyInstructors($id, $notice->title);
        $this->logActivity('NOTIFY_INSTRUCTORS', 'NOTICES', "Manually pushed notification for notice ID: {$id} - \"{$notice->title}\"");
        flash('dashboard_success', "Notification sent to all instructors for: \"{$notice->title}\"", 'alert alert-success');
        redirect('notice');
    }
}
