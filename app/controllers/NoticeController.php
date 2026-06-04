<?php
/**
 * Notice Controller Class
 * Handles notice board updates, announcements publishing, and archiving.
 */
class NoticeController extends Controller {
    private $noticeModel;

    public function __construct() {
        requireLogin();
        $this->noticeModel = $this->model('NoticeModel');
    }

    /**
     * Display notices configuration page
     */
    public function index() {
        requireAdmin();

        $notices = $this->noticeModel->getAllNotices();
        
        $data = [
            'title' => 'Notice Board Announcements',
            'active_menu' => 'notices',
            'notices' => $notices
        ];

        $this->view('templates/header', $data);
        $this->view('notices/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create Notice
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('notice');
            }

            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $status = $_POST['status'] ?? 'active';

            $data = [
                'title' => $title,
                'content' => $content,
                'published_by' => $_SESSION['user_id'],
                'status' => $status
            ];

            if ($this->noticeModel->createNotice($data)) {
                $this->logActivity('CREATE_NOTICE', 'NOTICES', "Published announcement: '{$title}'");
                flash('dashboard_success', 'Notice published on dashboard board.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to publish notice.', 'alert alert-danger');
            }
            redirect('notice');
        }
    }

    /**
     * Update Notice details
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('notice');
            }

            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $status = $_POST['status'] ?? 'active';

            $data = [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ];

            if ($this->noticeModel->updateNotice($id, $data)) {
                $this->logActivity('UPDATE_NOTICE', 'NOTICES', "Modified announcement ID: {$id} - {$title}");
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
}
