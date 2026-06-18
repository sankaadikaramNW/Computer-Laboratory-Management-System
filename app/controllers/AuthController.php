<?php
/**
 * Auth Controller Class
 * Handles login, logout, and session lifecycle.
 */
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Default route - redirect to login
     */
    public function index() {
        $this->login();
    }

    /**
     * Handle Login View and Authentication POST
     */
    public function login() {
        // Redirect to dashboard if already logged in
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('dashboard/admin');
            } elseif (isInstructor()) {
                redirect('dashboard/instructor');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get client IP address
            $ipAddress = '127.0.0.1';
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
            }

            // 1. Verify CSRF Token
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('login_error', 'Invalid security token. Please try again.', 'alert alert-danger');
                $this->view('auth/login');
                return;
            }

            // 2. Sanitize user inputs
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = $_POST['password'] ?? '';
            
            // 3. Brute Force Check (Max 5 attempts in 10 minutes)
            $attempts = $this->userModel->getLoginAttemptsCount($ipAddress, $username);
            
            // Also check DB column failed_attempts
            $dbUser = $this->userModel->findUserByUsername($username);
            $dbFailedAttempts = $dbUser ? (int)$dbUser->failed_attempts : 0;
            
            if ($attempts >= 5 || $dbFailedAttempts >= 5) {
                // Lock the account if it exists
                if ($dbUser && $dbUser->status !== 'locked') {
                    $this->userModel->lockUserAccount($dbUser->id);
                    $this->logActivity('ACCOUNT_LOCKED', 'USERS', "User account {$username} locked due to too many failed attempts from IP {$ipAddress}");
                }
                
                flash('login_error', 'Account locked due to too many failed attempts. Contact Administrator.', 'alert alert-danger');
                $this->view('auth/login');
                return;
            }

            // 4. Authenticate
            $user = $this->userModel->authenticate($username, $password);

            if ($user) {
                // Check if account status is active
                if ($user->status === 'inactive') {
                    flash('login_error', 'Your account has been deactivated.', 'alert alert-danger');
                    $this->view('auth/login');
                    return;
                } elseif ($user->status === 'locked') {
                    flash('login_error', 'Account is locked. Please contact Administrator.', 'alert alert-danger');
                    $this->view('auth/login');
                    return;
                }

                // Check password expiry
                $passwordExpired = false;
                if ($user->last_password_change && $user->password_expiry_days > 0) {
                    $lastChange = strtotime($user->last_password_change);
                    $expiryDays = (int)$user->password_expiry_days;
                    if (time() > $lastChange + ($expiryDays * 24 * 60 * 60)) {
                        $passwordExpired = true;
                    }
                }

                // Check force password change requirement
                $mustChangePassword = ((int)$user->force_password_change === 1) || $passwordExpired;

                // Successful login -> Clear login attempts log and reset failed attempts column
                $this->userModel->clearLoginAttempts($ipAddress, $username);
                $this->userModel->resetFailedAttempts($username);

                // Set session variables
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user_role_id'] = (int)$user->role_id;
                $_SESSION['user_role_name'] = $user->role_name;
                $_SESSION['last_activity'] = time();

                // Set password change flag in session if needed
                if ($mustChangePassword) {
                    $_SESSION['must_change_password'] = true;
                }

                // Load Instructor details if role is Instructor
                if ($user->role_id === 2) {
                    $instructorModel = $this->model('InstructorModel');
                    $instructor = $instructorModel->getInstructorByUserId($user->id);
                    if ($instructor) {
                        $_SESSION['instructor_id'] = $instructor->id;
                        $_SESSION['instructor_name'] = $instructor->full_name;
                        $_SESSION['instructor_rank'] = $instructor->rank;
                        $_SESSION['instructor_service_no'] = $instructor->service_no;
                        $_SESSION['instructor_photo'] = $instructor->profile_photo;
                    }
                }

                // Update last login timestamp
                $this->userModel->updateLastLogin($user->id);

                // Log Activity
                $this->logActivity('LOGIN', 'AUTH', "User '{$username}' logged in successfully.");

                // Redirect based on role and security status
                if ($mustChangePassword) {
                    flash('change_password_warning', 'Security policy requires you to change your password.', 'alert alert-warning alert-dismissible fade show');
                    redirect('auth/changePassword');
                } elseif ($user->role_id === 1) {
                    redirect('dashboard/admin');
                } else {
                    redirect('dashboard/instructor');
                }
            } else {
                // Failed login -> Track attempt
                $this->userModel->trackLoginAttempt($ipAddress, $username);
                $isNowLocked = $this->userModel->incrementFailedAttempts($username);
                
                $currentAttempts = $this->userModel->getLoginAttemptsCount($ipAddress, $username);
                if ($isNowLocked) {
                    $this->logActivity('ACCOUNT_LOCKED', 'USERS', "User account {$username} locked due to too many failed attempts.");
                    flash('login_error', 'Account locked due to too many failed attempts. Contact Administrator.', 'alert alert-danger');
                } else {
                    flash('login_error', 'Invalid username or password. Remaining attempts: ' . (5 - $currentAttempts), 'alert alert-danger');
                }
                $this->view('auth/login');
            }
        } else {
            // Load Login Form
            $this->view('auth/login');
        }
    }

    /**
     * Self-Service Password Change (Admin / Instructor — inside dashboard layout)
     */
    public function myPassword() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF Token
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('change_password_error', 'Invalid security token. Please try again.', 'alert alert-danger');
                redirect('auth/myPassword');
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                flash('change_password_error', 'All fields are required.', 'alert alert-danger');
                redirect('auth/myPassword');
            }

            if ($newPassword !== $confirmPassword) {
                flash('change_password_error', 'New passwords do not match.', 'alert alert-danger');
                redirect('auth/myPassword');
            }

            if (strlen($newPassword) < 8) {
                flash('change_password_error', 'New password must be at least 8 characters long.', 'alert alert-danger');
                redirect('auth/myPassword');
            }

            // Verify current password
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            if (!password_verify($currentPassword, $user->password)) {
                flash('change_password_error', 'Current password is incorrect.', 'alert alert-danger');
                redirect('auth/myPassword');
            }

            // Validate against password history
            $history = $this->userModel->getPasswordHistory($user->id);
            foreach ($history as $h) {
                if (password_verify($newPassword, $h->password_hash)) {
                    flash('change_password_error', 'You cannot reuse a password you have used in the past.', 'alert alert-danger');
                    redirect('auth/myPassword');
                }
            }

            // Save new password (force_password_change = 0, already chosen by user)
            if ($this->userModel->resetPassword($user->id, $newPassword, 0)) {
                unset($_SESSION['must_change_password']);
                $this->logActivity('PASSWORD_CHANGED', 'AUTH', "User '{$user->username}' changed their own password.");
                flash('dashboard_success', 'Password changed successfully.', 'alert alert-success alert-dismissible fade show');
                if (isAdmin()) {
                    redirect('dashboard/admin');
                } else {
                    redirect('dashboard/instructor');
                }
            } else {
                flash('change_password_error', 'Failed to update password. Please try again.', 'alert alert-danger');
                redirect('auth/myPassword');
            }
        } else {
            $data = [
                'title'       => 'Change My Password',
                'active_menu' => 'my_password',
            ];
            $this->view('templates/header', $data);
            $this->view('auth/my_password', $data);
            $this->view('templates/footer');
        }
    }

    /**
     * Handle Forced / Standard Password Change
     */
    public function changePassword() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF Token
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('change_password_error', 'Invalid security token. Please try again.', 'alert alert-danger');
                $this->view('auth/change_password');
                return;
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                flash('change_password_error', 'All fields are required.', 'alert alert-danger');
                $this->view('auth/change_password');
                return;
            }

            if ($newPassword !== $confirmPassword) {
                flash('change_password_error', 'New passwords do not match.', 'alert alert-danger');
                $this->view('auth/change_password');
                return;
            }

            if (strlen($newPassword) < 8) {
                flash('change_password_error', 'New password must be at least 8 characters long.', 'alert alert-danger');
                $this->view('auth/change_password');
                return;
            }

            // Authenticate with current password
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            if (!password_verify($currentPassword, $user->password)) {
                flash('change_password_error', 'Current password is incorrect.', 'alert alert-danger');
                $this->view('auth/change_password');
                return;
            }

            // Validate against password history
            $history = $this->userModel->getPasswordHistory($user->id);
            foreach ($history as $h) {
                if (password_verify($newPassword, $h->password_hash)) {
                    flash('change_password_error', 'You cannot reuse a password you have used in the past.', 'alert alert-danger');
                    $this->view('auth/change_password');
                    return;
                }
            }

            // Save new password
            if ($this->userModel->resetPassword($user->id, $newPassword, 0)) {
                // Clear the session flag
                unset($_SESSION['must_change_password']);
                
                $this->logActivity('PASSWORD_CHANGED', 'AUTH', "User changed password.");
                
                flash('dashboard_success', 'Password changed successfully.', 'alert alert-success alert-dismissible fade show');
                
                if ($user->role_id === 1) {
                    redirect('dashboard/admin');
                } else {
                    redirect('dashboard/instructor');
                }
            } else {
                flash('change_password_error', 'Failed to update password. Please try again.', 'alert alert-danger');
                $this->view('auth/change_password');
            }
        } else {
            $this->view('auth/change_password');
        }
    }

    /**
     * Handle Logout
     */
    public function logout() {
        if (isLoggedIn()) {
            $username = $_SESSION['username'];
            $this->logActivity('LOGOUT', 'AUTH', "User '{$username}' logged out.");
        }

        // Unset and destroy session
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // Redirect with success message
        session_start();
        flash('login_success', 'You have been logged out successfully.', 'alert alert-success');
        redirect('auth/login');
    }

    /**
     * Display logged-in user's own login activity history
     */
    public function myLoginActivity() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }

        $auditModel = $this->model('AuditModel');
        $logs = $auditModel->getUserLoginLogs($_SESSION['user_id'], $_SESSION['username'], 100);

        $data = [
            'title' => 'My Login & Security Activity',
            'active_menu' => 'my_login_activity',
            'logs' => $logs
        ];

        $this->view('templates/header', $data);
        $this->view('auth/my_login_activity', $data);
        $this->view('templates/footer');
    }

    /**
     * Print-friendly login activity report
     */
    public function myLoginActivityReport() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }

        $auditModel = $this->model('AuditModel');
        $logs = $auditModel->getUserLoginLogs($_SESSION['user_id'], $_SESSION['username'], 100);

        $data = [
            'title' => 'My Login Activity Audit Report',
            'logs' => $logs,
            'target_user' => $_SESSION['username'],
            'target_role' => $_SESSION['user_role_name']
        ];

        $this->view('reports/login_activity_report', $data);
    }
}
