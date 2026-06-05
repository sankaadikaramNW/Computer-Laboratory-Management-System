<?php
/**
 * PHP Built-in Server Router
 * Used by Railway/Render when running: php -S 0.0.0.0:$PORT -t . router.php
 *
 * Emulates .htaccess mod_rewrite:
 *   - Serves real files/directories directly
 *   - Routes everything else through index.php?url=...
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve real static files directly (css, js, images, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let built-in server handle it
}

// Strip leading slash and pass as url param
$url = ltrim($uri, '/');
$_GET['url'] = $url;

// Boot the app
require_once __DIR__ . '/index.php';
