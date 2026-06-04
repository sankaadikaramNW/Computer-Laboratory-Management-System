<?php
/**
 * User Controller Class
 * Handles User Account CRUD, Lockouts, Password Resets, and Login Audit History.
 */
class UserController extends Controller {
    private $userModel;
    private $auditModel;

    public function __construct() {
        requireLogin();
        requireAdmin();
        $this->userModel = $this->model('UserModel');
        $this->auditModel = $this->model('AuditModel');
    }

    /**
     * Display User Management Listing
     */
    public function index() {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $roleId = isset($_GET['role_id']) && $_GET['role_id'] !== '' ? (int)$_GET['role_id'] : null;

        if ($q !== '' || $roleId !== null) {
            $users = $this->userModel->searchUsers($q, $roleId);
        } else {
            $users = $this->userModel->getAllUsers();
        }

        $data = [
            'title' => 'User Account Management',
            'active_menu' => 'user_management',
            'users' => $users,
            'q' => $q,
            'role_id' => $roleId
        ];

        $this->view('templates/header', $data);
        $this->view('users/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create User Account
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('user');
            }

            $username = trim($_POST['username']);
            $password = $_POST['password'] ?? '';
            $roleId = (int)$_POST['role_id'];
            $status = $_POST['status'] ?? 'active';
            $forcePasswordChange = isset($_POST['force_password_change']) ? 1 : 0;
            $passwordExpiryDays = isset($_POST['password_expiry_days']) ? (int)$_POST['password_expiry_days'] : 90;

            // Simple validation
            if (empty($username) || empty($password)) {
                flash('dashboard_error', 'Username and Password are required.', 'alert alert-danger');
                redirect('user');
            }

            // Check if username already exists
            if ($this->userModel->findUserByUsername($username)) {
                flash('dashboard_error', "Username '{$username}' already exists.", 'alert alert-danger');
                redirect('user');
            }

            $userId = $this->userModel->createUser($username, $password, $roleId, $status, $forcePasswordChange, $passwordExpiryDays);
            if ($userId) {
                $roleName = ($roleId === 1) ? 'Administrator' : 'Instructor';
                $this->logActivity('CREATE_USER', 'USERS', "Created new user account: '{$username}' with role '{$roleName}'");
                flash('dashboard_success', 'User account created successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to create user account.', 'alert alert-danger');
            }
            redirect('user');
        }
    }

    /**
     * Update User Account
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('user');
            }

            $roleId = (int)$_POST['role_id'];
            $status = $_POST['status'];
            $forcePasswordChange = isset($_POST['force_password_change']) ? 1 : 0;
            $passwordExpiryDays = (int)$_POST['password_expiry_days'];

            // Prevent changing own role/status if editing self
            if ((int)$id === (int)$_SESSION['user_id']) {
                $currentUser = $this->userModel->getUserById($id);
                $roleId = (int)$currentUser->role_id;
                $status = $currentUser->status;
            }

            if ($this->userModel->updateUser($id, $roleId, $status, $forcePasswordChange, $passwordExpiryDays)) {
                $user = $this->userModel->getUserById($id);
                $this->logActivity('UPDATE_USER', 'USERS', "Updated user account '{$user->username}' (ID: {$id}) details.");
                flash('dashboard_success', 'User account updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update user account.', 'alert alert-danger');
            }
            redirect('user');
        }
    }

    /**
     * Activate User Account
     */
    public function activate($id) {
        if ($this->userModel->activateUserAccount($id)) {
            $user = $this->userModel->getUserById($id);
            $this->logActivity('ACTIVATE_USER', 'USERS', "Activated user account '{$user->username}' (ID: {$id})");
            flash('dashboard_success', 'User account activated successfully.', 'alert alert-success');
        } else {
            flash('dashboard_error', 'Failed to activate user account.', 'alert alert-danger');
        }
        redirect('user');
    }

    /**
     * Deactivate User Account
     */
    public function deactivate($id) {
        if ((int)$id === (int)$_SESSION['user_id']) {
            flash('dashboard_error', 'You cannot deactivate your own account.', 'alert alert-danger');
            redirect('user');
        }

        if ($this->userModel->deactivateUserAccount($id)) {
            $user = $this->userModel->getUserById($id);
            $this->logActivity('DEACTIVATE_USER', 'USERS', "Deactivated user account '{$user->username}' (ID: {$id})");
            flash('dashboard_success', 'User account deactivated successfully.', 'alert alert-success');
        } else {
            flash('dashboard_error', 'Failed to deactivate user account.', 'alert alert-danger');
        }
        redirect('user');
    }

    /**
     * Lock User Account
     */
    public function lock($id) {
        if ((int)$id === (int)$_SESSION['user_id']) {
            flash('dashboard_error', 'You cannot lock your own account.', 'alert alert-danger');
            redirect('user');
        }

        if ($this->userModel->lockUserAccount($id)) {
            $user = $this->userModel->getUserById($id);
            $this->logActivity('LOCK_USER', 'USERS', "Locked user account '{$user->username}' (ID: {$id})");
            flash('dashboard_success', 'User account locked successfully.', 'alert alert-success');
        } else {
            flash('dashboard_error', 'Failed to lock user account.', 'alert alert-danger');
        }
        redirect('user');
    }

    /**
     * Unlock User Account
     */
    public function unlock($id) {
        if ($this->userModel->unlockAccount($id)) {
            $user = $this->userModel->getUserById($id);
            $this->logActivity('UNLOCK_USER', 'USERS', "Unlocked user account '{$user->username}' (ID: {$id})");
            flash('dashboard_success', 'User account unlocked successfully.', 'alert alert-success');
        } else {
            flash('dashboard_error', 'Failed to unlock user account.', 'alert alert-danger');
        }
        redirect('user');
    }

    /**
     * Reset User Password
     */
    public function resetPassword($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('user');
            }

            $newPassword = $_POST['new_password'] ?? '';
            $forcePasswordChange = isset($_POST['force_password_change']) ? 1 : 0;

            if (strlen($newPassword) < 8) {
                flash('dashboard_error', 'Password must be at least 8 characters long.', 'alert alert-danger');
                redirect('user');
            }

            if ($this->userModel->resetPassword($id, $newPassword, $forcePasswordChange)) {
                $user = $this->userModel->getUserById($id);
                $this->logActivity('RESET_PASSWORD', 'USERS', "Reset password for user account '{$user->username}' (ID: {$id})");
                flash('dashboard_success', "Password for '{$user->username}' reset successfully.", 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to reset password.', 'alert alert-danger');
            }
            redirect('user');
        }
    }

    /**
     * AJAX Instant Search Users
     */
    public function search() {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $roleId = isset($_GET['role_id']) && $_GET['role_id'] !== '' ? (int)$_GET['role_id'] : null;

        $users = $this->userModel->searchUsers($q, $roleId);
        
        // Format response
        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'id' => $user->id,
                'username' => $user->username,
                'role_name' => $user->role_name,
                'role_id' => $user->role_id,
                'status' => $user->status,
                'created_at' => date('d M Y', strtotime($user->created_at)),
                'last_login' => $user->last_login ? date('d M Y H:i', strtotime($user->last_login)) : 'Never',
                'last_password_change' => $user->last_password_change ? date('d M Y', strtotime($user->last_password_change)) : 'Never',
                'failed_attempts' => $user->failed_attempts,
                'force_password_change' => $user->force_password_change,
                'password_expiry_days' => $user->password_expiry_days,
                'is_current_user' => ((int)$user->id === (int)$_SESSION['user_id'])
            ];
        }

        $this->json($response);
    }

    /**
     * Retrieve User Login History for AJAX modal view
     */
    public function loginHistory($id) {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }

        // Fetch audit logs related to this user ID and login events
        $this->auditModel->db->query("SELECT * FROM audit_logs 
                                      WHERE (user_id = :user_id AND (action = 'LOGIN' OR action = 'LOGOUT' OR action = 'PASSWORD_CHANGED')) 
                                         OR (details LIKE :username_lock AND action = 'ACCOUNT_LOCKED')
                                      ORDER BY created_at DESC 
                                      LIMIT 50");
        $this->auditModel->db->bind(':user_id', $id);
        $this->auditModel->db->bind(':username_lock', '%' . $user->username . '%');
        $logs = $this->auditModel->db->resultSet();

        $history = [];
        foreach ($logs as $log) {
            $history[] = [
                'action' => $log->action,
                'ip_address' => $log->ip_address,
                'details' => $log->details,
                'created_at' => date('d M Y H:i:s', strtotime($log->created_at))
            ];
        }

        $this->json([
            'username' => $user->username,
            'history' => $history
        ]);
    }
}
