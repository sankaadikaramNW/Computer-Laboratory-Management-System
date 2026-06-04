<?php
// Database Parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'slaf_clms');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));

// Dynamic URL Root Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /Computer Laboratory Management System/public/index.php
$publicDir = str_replace('\\', '/', dirname($scriptName)); // e.g., /Computer Laboratory Management System/public

// Build the base URL by stripping 'public' from the path
$baseUrlPath = preg_replace('/\/public$/', '', rtrim($publicDir, '/'));
$baseUrl = $protocol . $host . $baseUrlPath . '/';

define('URLROOT', $baseUrl);
define('SITENAME', 'SLAF CLMS');

// System Name
define('SYSTEM_TITLE', 'SLAF Trade Training School Ekala');
define('SYSTEM_SUBTITLE', 'Computer Laboratory Management System');
define('MILITARY_BRANCH', 'Sri Lanka Air Force');
