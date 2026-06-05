<?php
/**
 * Simple Redirect Helper
 */
function redirect($page) {
    header('location: ' . URLROOT . $page);
    exit();
}

/**
 * Check if the user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get logged in user's role ID
 */
function getUserRole() {
    return isset($_SESSION['user_role_id']) ? (int)$_SESSION['user_role_id'] : null;
}

/**
 * Check if user is an Administrator
 */
function isAdmin() {
    return isLoggedIn() && getUserRole() === 1;
}

/**
 * Check if user is an Instructor
 */
function isInstructor() {
    return isLoggedIn() && getUserRole() === 2;
}

/**
 * Require login middleware
 */
function requireLogin() {
    if (!isLoggedIn()) {
        flash('login_error', 'Access Denied. Please log in first.', 'alert alert-danger alert-dismissible fade show');
        redirect('auth/login');
    }

    // Check for session timeout (default 30 minutes / 1800 seconds)
    if (isset($_SESSION['last_activity'])) {
        $timeout = 1800; // default 30 mins
        if (time() - $_SESSION['last_activity'] > $timeout) {
            $username = $_SESSION['username'] ?? '';
            // Clear session
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            session_start();
            flash('login_error', 'Session expired due to inactivity. Please log in again.', 'alert alert-warning alert-dismissible fade show');
            redirect('auth/login');
        }
    }
    $_SESSION['last_activity'] = time();

    // Check for forced password change
    if (isset($_SESSION['must_change_password']) && $_SESSION['must_change_password'] === true) {
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
        if ($url !== 'auth/changePassword' && $url !== 'auth/logout') {
            flash('change_password_warning', 'Security policy requires you to change your password.', 'alert alert-warning alert-dismissible fade show');
            redirect('auth/changePassword');
        }
    }
}

/**
 * Require Administrator role middleware
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        flash('dashboard_error', 'Unauthorized access. Administrator privileges required.', 'alert alert-danger alert-dismissible fade show');
        
        // Redirect to instructor dashboard or appropriate page
        if (isInstructor()) {
            redirect('dashboard/instructor');
        } else {
            redirect('auth/login');
        }
    }
}

/**
 * Require Instructor role middleware
 */
function requireInstructor() {
    requireLogin();
    if (!isInstructor()) {
        flash('dashboard_error', 'Unauthorized access. Instructor privileges required.', 'alert alert-danger alert-dismissible fade show');
        if (isAdmin()) {
            redirect('dashboard/admin');
        } else {
            redirect('auth/login');
        }
    }
}
