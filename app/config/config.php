<?php
/**
 * Simple Custom Dotenv Loader
 * Loads environment variables from a .env file into putenv(), $_ENV, and $_SERVER.
 */
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments and empty lines
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        // Split at the first '='
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);

            // Strip quotes if they surround the value
            if (preg_match('/^["\'`](.*)["\'`]$/', $val, $matches)) {
                $val = $matches[1];
            }

            // Set environment variable if not already set by system environment
            if (getenv($key) === false) {
                putenv("$key=$val");
            }
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $val;
            }
            if (!isset($_SERVER[$key])) {
                $_SERVER[$key] = $val;
            }
        }
    }
    return true;
}

// Load root .env file (two levels up from app/config/config.php)
loadEnv(dirname(dirname(__DIR__)) . '/.env');

// Set System Timezone
$timezone = getenv('TIMEZONE') ?: 'Asia/Colombo';
date_default_timezone_set($timezone);

// Database Parameters (loaded from environment variables)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'itwekala_slaf_clms');

// App Root — points to the app/ directory
// When at htdocs/app/config/config.php:
//   dirname(__FILE__)         = htdocs/app/config
//   dirname(dirname(__FILE__)) = htdocs/app  ✅
define('APPROOT', dirname(dirname(__FILE__)));

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

