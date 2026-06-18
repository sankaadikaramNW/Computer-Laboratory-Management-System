<?php
// Database Parameters (local dev — replaced by deploy config on server)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'itwekala_slaf_clms');

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

