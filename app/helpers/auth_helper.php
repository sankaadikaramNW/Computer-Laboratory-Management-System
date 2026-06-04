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
