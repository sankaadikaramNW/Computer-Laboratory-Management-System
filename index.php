<?php
// Load Config
require_once '../app/config/config.php';

// Register Autoloader for Core and Models
spl_autoload_register(function($className) {
    $paths = [
        APPROOT . '/core/',
        APPROOT . '/models/',
        APPROOT . '/controllers/'
    ];
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load Security & Utility Helpers
require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/auth_helper.php';
require_once '../app/helpers/sanitization_helper.php';

// Load Core MVC Engine Libraries
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';

// Boot the MVC Router
$app = new App();

