<?php
// Load Config
require_once 'app/config/config.php';

// Register Global Exception Handler
set_exception_handler(function($exception) {
    // Log the error
    if (class_exists('StartupValidator')) {
        StartupValidator::logError($exception->getMessage() . "\n" . $exception->getTraceAsString(), 'UNCAUGHT_EXCEPTION');
    } else {
        error_log($exception->getMessage());
    }

    // Clean buffers
    if (ob_get_length()) {
        ob_clean();
    }

    http_response_code(500);

    // Is it a DB mismatch?
    $isDbMismatch = false;
    $msg = $exception->getMessage();
    if ($exception instanceof PDOException) {
        $sqlState = $exception->getCode();
        if ($sqlState === '42S22' || strpos($msg, '42S22') !== false || $sqlState === '42S02' || strpos($msg, '42S02') !== false) {
            $isDbMismatch = true;
        }
    }

    $ref = $isDbMismatch ? 'DB_SCHEMA_MISMATCH' : 'RUNTIME_ERROR';
    $title = $isDbMismatch ? 'System Configuration Error' : 'System Error';
    $desc = $isDbMismatch ? 'The application configuration does not match the database schema. Please contact the administrator.' : 'An unexpected error occurred. Please try again later.';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <style>
            :root {
                --bg: #0f172a;
                --card-bg: #1e293b;
                --text: #f8fafc;
                --muted: #94a3b8;
                --border: #334155;
                --error: #ef4444;
            }
            body {
                background: var(--bg);
                color: var(--text);
                font-family: system-ui, -apple-system, sans-serif;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                padding: 20px;
            }
            .error-card {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: 16px;
                padding: 40px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
                text-align: center;
            }
            .error-icon {
                font-size: 4rem;
                color: var(--error);
                margin-bottom: 20px;
            }
            h1 {
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 15px;
            }
            p {
                color: var(--muted);
                font-size: 1rem;
                line-height: 1.6;
                margin-bottom: 25px;
            }
            .ref-badge {
                background: rgba(239, 68, 68, 0.1);
                color: var(--error);
                border: 1px solid rgba(239, 68, 68, 0.2);
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 0.85rem;
                font-weight: 600;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="error-icon">
                <i class="bi bi-shield-fill-exclamation"></i>
            </div>
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <p><?php echo htmlspecialchars($desc); ?></p>
            <div class="mt-2">
                <span class="ref-badge">Error Reference: <?php echo htmlspecialchars($ref); ?></span>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
});

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
require_once 'app/helpers/session_helper.php';
require_once 'app/helpers/auth_helper.php';
require_once 'app/helpers/sanitization_helper.php';

// Load Core MVC Engine Libraries
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';
require_once 'app/core/Model.php';

// Load Startup Validator and Validate Environment
require_once 'app/core/StartupValidator.php';
StartupValidator::run();

// Boot the MVC Router
$app = new App();

