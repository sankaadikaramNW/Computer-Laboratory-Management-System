<?php
// Secure session initialization
if (session_status() === PHP_SESSION_NONE) {
    // Prevent access to session cookie via JavaScript
    ini_set('session.cookie_httponly', 1);
    
    // Enable secure cookies if HTTPS is used
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    // Prevent session hijacking by binding cookie to referrer path (standard)
    ini_set('session.use_only_cookies', 1);
    
    session_start();
}

/**
 * Flash Message Helper
 * Usage:
 *   Set: flash('post_message', 'Your post has been added');
 *   Display: flash('post_message');
 */
function flash($name = '', $message = '', $class = 'alert alert-success alert-dismissible fade show') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" role="alert" id="msg-flash">';
            echo '  <i class="bi bi-info-circle-fill me-2"></i>';
            echo    $_SESSION[$name];
            echo '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

/**
 * Generate CSRF Token for form submissions
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF Token
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
