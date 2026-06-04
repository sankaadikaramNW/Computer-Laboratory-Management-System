<?php
/**
 * Notification Controller Class
 * Handles user notifications listings, read statuses, and badge updates.
 */
class NotificationController extends Controller {
    private $notifModel;

    public function __construct() {
        requireLogin();
        $this->notifModel = $this->model('NotificationModel');
    }

    /**
     * Display User's Notifications Feed
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $notifications = $this->notifModel->getNotificationsByUser($userId);

        $data = [
            'title' => 'Notifications Inbox',
            'active_menu' => 'notifications',
            'notifications' => $notifications
        ];

        $this->view('templates/header', $data);
        $this->view('notifications/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Mark a specific notification as read (Redirect or API)
     */
    public function markAsRead($id) {
        $this->notifModel->markAsRead($id);
        
        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->json(['success' => true]);
            return;
        }

        flash('dashboard_success', 'Notification marked as read.', 'alert alert-success');
        redirect('notification');
    }

    /**
     * Mark all user notifications as read
     */
    public function markAllRead() {
        $userId = $_SESSION['user_id'];
        $this->notifModel->markAllAsRead($userId);

        flash('dashboard_success', 'All notifications marked as read.', 'alert alert-success');
        redirect('notification');
    }
}
