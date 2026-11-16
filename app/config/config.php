<?php
// Load environment variables from .env file if it exists
$envPath = dirname(dirname(dirname(__FILE__))) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
                $value = $matches[1];
            }
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Database Parameters
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'slaf_clms');

// App Root — points to the app/ directory
// When at htdocs/app/config/config.php:
//   dirname(__FILE__)         = htdocs/app/config
//   dirname(dirname(__FILE__)) = htdocs/app  ✅
define('APPROOT', dirname(dirname(__FILE__)));

// Detect reverse proxy SSL termination
if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
    (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTOCOL']) && $_SERVER['HTTP_X_FORWARDED_PROTOCOL'] === 'https') ||
    (!empty($_SERVER['HTTP_X_URL_SCHEME']) && $_SERVER['HTTP_X_URL_SCHEME'] === 'https')
) {
    $_SERVER['HTTPS'] = 'on';
}

// Dynamic URL Root Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']); // e.g., /public/index.php or /index.php
$publicDir = dirname($scriptName);

// Strip /public suffix if present (local dev with public/ folder structure)
$baseUrlPath = preg_replace('/\/public$/', '', rtrim($publicDir, '/'));

// Ensure we always have a clean trailing slash
define('URLROOT', $protocol . $host . $baseUrlPath . '/');
define('SITENAME', 'SLAF CLMS');

// System Name
define('SYSTEM_TITLE', 'SLAF Trade Training School Ekala');
define('SYSTEM_SUBTITLE', 'Computer Laboratory Management System');
define('MILITARY_BRANCH', 'Sri Lanka Air Force');

