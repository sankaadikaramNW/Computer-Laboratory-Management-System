<?php
// DIAGNOSTIC FILE - Upload to htdocs/ root and visit yoursite.com/test.php
// DELETE THIS FILE after diagnosing the issue!

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Diagnostic Test</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 1: Check config file path
$configPath = __DIR__ . '/app/config/config.php';
echo "<p>Config path: <code>$configPath</code></p>";
echo "<p>Config exists: " . (file_exists($configPath) ? '<b style="color:green">YES</b>' : '<b style="color:red">NO</b>') . "</p>";

// Test 2: Load config
if (file_exists($configPath)) {
    require_once $configPath;
    echo "<p>APPROOT: <code>" . APPROOT . "</code></p>";
    echo "<p>URLROOT: <code>" . URLROOT . "</code></p>";
    echo "<p>DB_HOST: <code>" . DB_HOST . "</code></p>";
    echo "<p>DB_NAME: <code>" . DB_NAME . "</code></p>";
}

// Test 3: PDO / MySQL connection
echo "<h3>Database Connection Test</h3>";
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "<p style='color:green'><b>Database connected successfully!</b></p>";
    $pdo = null;
} catch (PDOException $e) {
    echo "<p style='color:red'><b>DB Error: " . htmlspecialchars($e->getMessage()) . "</b></p>";
}

// Test 4: Check key directories exist
echo "<h3>Directory Structure Check</h3>";
$dirs = ['app/controllers', 'app/core', 'app/helpers', 'app/models', 'app/views'];
foreach ($dirs as $dir) {
    $full = __DIR__ . '/' . $dir;
    $exists = is_dir($full) ? '<b style="color:green">OK</b>' : '<b style="color:red">MISSING</b>';
    echo "<p>$dir: $exists</p>";
}

echo "<hr><p style='color:gray'>Delete test.php from the server after reading this!</p>";
