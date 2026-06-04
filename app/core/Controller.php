<?php
/**
 * Base Controller Class
 * Provides helpers to load models, load views, and return standardized JSON responses.
 */
class Controller {
    /**
     * Load Model
     */
    public function model($model) {
        // Require model file
        require_once APPROOT . '/models/' . $model . '.php';
        // Instantiate model
        return new $model();
    }

    /**
     * Load View
     */
    public function view($view, $data = []) {
        // Check for view file
        $viewFile = APPROOT . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View '{$view}' does not exist");
        }
    }

    /**
     * Send JSON Response
     */
    public function json($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * Write an audit log entry
     */
    public function logActivity($action, $module, $details = '') {
        $auditModel = $this->model('AuditModel');
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        
        // Handle IP address retrieval securely
        $ipAddress = '127.0.0.1';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        
        $auditModel->log($userId, $action, $module, $ipAddress, $details);
    }
}
