<?php
// ============================================================
// InfinityFree Deployment Config — TEMPLATE
// ============================================================
// INSTRUCTIONS:
//   1. Copy this file to: app/config/config.php  (on the server)
//   2. Fill in YOUR real InfinityFree credentials below
//   3. Get credentials from: InfinityFree Control Panel → MySQL Databases
// ============================================================

// ⚠️ REPLACE WITH YOUR REAL INFINITYFREE CREDENTIALS:
define('DB_HOST', 'sqlXXX.infinityfree.com');   // e.g., sql108.infinityfree.com
define('DB_USER', 'epXXXXXX_username');          // e.g., if0_12345678
define('DB_PASS', 'your_database_password');      // your MySQL password
define('DB_NAME', 'epXXXXXX_dbname');            // e.g., if0_12345678_itwekala_slaf_clms

// App Root — resolves to htdocs/app/ when config.php is at htdocs/app/config/config.php
define('APPROOT', dirname(dirname(__FILE__)));

// Dynamic URL Root Detection (no /public subfolder on InfinityFree)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'];
$publicDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('URLROOT', $protocol . $host . rtrim($publicDir, '/') . '/');

define('SITENAME',         'SLAF CLMS');
define('SYSTEM_TITLE',     'SLAF Trade Training School Ekala');
define('SYSTEM_SUBTITLE',  'Computer Laboratory Management System');
define('MILITARY_BRANCH',  'Sri Lanka Air Force');
