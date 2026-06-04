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
            // 1. Verify CSRF Token
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('login_error', 'Invalid security token. Please try again.', 'alert alert-danger');
                $this->view('auth/login');
                return;
            }

            // 2. Sanitize user inputs
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = $_POST['password'] ?? '';
            
            // Get IP Address
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            // 3. Brute Force Check (Max 5 attempts in 10 minutes)
            $attempts = $this->userModel->getLoginAttemptsCount($ipAddress, $username);
            if ($attempts >= 5) {
                // Lock the account if it exists
                $user = $this->userModel->findUserByUsername($username);
                if ($user && $user->status !== 'locked') {
                    $this->userModel->lockAccount($username);
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

                // Successful login -> Clear login attempts log
                $this->userModel->clearLoginAttempts($ipAddress, $username);

                // Set session variables
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user_role_id'] = (int)$user->role_id;
                $_SESSION['user_role_name'] = $user->role_name;

                // Load Instructor details if role is Instructor
                if ($user->role_id === 2) {
                    $instructorModel = $this->model('InstructorModel');
                    $instructor = $instructorModel->getInstructorByUserId($user->id);
                    if ($instructor) {
                        $_SESSION['instructor_id'] = $instructor->id;
                        $_SESSION['instructor_name'] = $instructor->full_name;
                        $_SESSION['instructor_rank'] = $instructor->rank;
                        $_SESSION['instructor_service_no'] = $instructor->service_no;
                    }
                }

                // Update last login timestamp
                $this->userModel->updateLastLogin($user->id);

                // Log Activity
                $this->logActivity('LOGIN', 'AUTH', "User '{$username}' logged in successfully.");

                // Redirect based on role
                if ($user->role_id === 1) {
                    redirect('dashboard/admin');
                } else {
                    redirect('dashboard/instructor');
                }
            } else {
                // Failed login -> Track attempt
                $this->userModel->trackLoginAttempt($ipAddress, $username);
                
                flash('login_error', 'Invalid username or password. Remaining attempts: ' . (5 - ($attempts + 1)), 'alert alert-danger');
                $this->view('auth/login');
            }
        } else {
            // Load Login Form
            $this->view('auth/login');
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
        // Start a fresh session to hold the flash message
        session_start();
        flash('login_success', 'You have been logged out successfully.', 'alert alert-success');
        redirect('auth/login');
    }
}
