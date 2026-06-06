<?php
/**
 * Startup Validator Class
 * Validates the environment, database connection, schema, and directory permissions on app startup.
 */
class StartupValidator {
    private static $requiredSchema = [
        'roles' => ['id', 'name', 'description', 'created_at'],
        'permissions' => ['id', 'name', 'description'],
        'role_permissions' => ['role_id', 'permission_id'],
        'users' => [
            'id', 'username', 'password', 'role_id', 'status', 'last_login', 
            'created_at', 'updated_at', 'last_password_change', 'failed_attempts', 
            'force_password_change', 'password_expiry_days'
        ],
        'login_attempts' => ['id', 'ip_address', 'username', 'attempt_time'],
        'instructors' => [
            'id', 'user_id', 'service_no', 'rank', 'full_name', 'trade', 
            'contact_no', 'email', 'profile_photo', 'photo_uploaded_at', 
            'status', 'created_at', 'updated_at'
        ],
        'laboratories' => ['id', 'lab_code', 'lab_name', 'location', 'capacity', 'description', 'status', 'created_at', 'updated_at'],
        'computers' => [
            'id', 'asset_no', 'serial_no', 'brand', 'model', 'processor', 'ram', 
            'storage', 'os', 'purchase_date', 'warranty_status', 'lab_id', 'status', 
            'created_at', 'updated_at'
        ],
        'smart_boards' => ['id', 'asset_id', 'brand', 'model', 'installation_date', 'lab_id', 'status', 'created_at', 'updated_at'],
        'lessons' => ['id', 'lesson_code', 'lesson_name', 'trade', 'duration', 'description', 'created_at', 'updated_at'],
        'allocations' => [
            'id', 'instructor_id', 'lesson_id', 'lab_id', 'date', 'start_time', 'end_time', 
            'remarks', 'session_status', 'instructor_remarks', 'completed_at', 'completed_by', 
            'created_at', 'updated_at'
        ],
        'allocation_requests' => [
            'id', 'allocation_id', 'requester_id', 'type', 'new_date', 'new_start_time', 
            'new_end_time', 'new_lab_id', 'reason', 'status', 'reviewer_remarks', 
            'created_at', 'updated_at'
        ],
        'fault_reports' => ['id', 'reported_by', 'equipment_type', 'equipment_id', 'description', 'status', 'resolution_notes', 'created_at', 'updated_at'],
        'maintenance_records' => ['id', 'equipment_type', 'equipment_id', 'issue_type', 'assigned_technician', 'repair_date', 'status', 'notes', 'created_at', 'updated_at'],
        'notifications' => ['id', 'user_id', 'message', 'is_read', 'type', 'related_id', 'created_at'],
        'notices' => ['id', 'title', 'content', 'published_by', 'status', 'created_at', 'updated_at'],
        'audit_logs' => ['id', 'user_id', 'action', 'module', 'ip_address', 'details', 'created_at'],
        'system_settings' => ['setting_key', 'setting_value', 'description', 'updated_at'],
        'password_history' => ['id', 'user_id', 'password_hash', 'changed_at']
    ];

    /**
     * Run all startup checks
     */
    public static function run() {
        // 1. Validate Basic Config
        if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_NAME') || !defined('APPROOT')) {
            self::logError('Critical configuration constants are undefined.', 'CONFIG_MISMATCH');
            self::displayErrorPage('DB_SCHEMA_MISMATCH', 'Critical configuration constants are undefined. Please check app/config/config.php.');
        }

        // 2. Validate/Create Upload Directories
        $uploadBase = APPROOT . '/../uploads';
        $uploadInstructors = $uploadBase . '/instructors';
        $logsDir = APPROOT . '/logs';

        // Auto-create directories if missing
        if (!file_exists($uploadBase)) {
            @mkdir($uploadBase, 0777, true);
        }
        if (!file_exists($uploadInstructors)) {
            @mkdir($uploadInstructors, 0777, true);
        }
        if (!file_exists($logsDir)) {
            @mkdir($logsDir, 0777, true);
        }

        // Collect non-critical warnings
        $warnings = [];

        if (!is_writable($uploadBase)) {
            $warnings[] = "Upload base directory is not writable: " . realpath($uploadBase);
        }
        if (!is_writable($uploadInstructors)) {
            $warnings[] = "Instructors upload directory is not writable: " . realpath($uploadInstructors);
        }
        if (!is_writable($logsDir)) {
            $warnings[] = "Logs directory is not writable: " . realpath($logsDir);
        }

        // 3. Connect to Database and Validate Schema
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Fetch tables
            $tablesStmt = $pdo->query("SHOW TABLES");
            $existingTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

            // Lowercase mapping for case-insensitivity comparison
            $existingTablesLower = array_map('strtolower', $existingTables);

            foreach (self::$requiredSchema as $table => $columns) {
                $tableLower = strtolower($table);
                if (!in_array($tableLower, $existingTablesLower)) {
                    self::logError("Database table '$table' is missing.", 'DB_SCHEMA_MISMATCH');
                    self::displayErrorPage('DB_SCHEMA_MISMATCH', "The database table '$table' is missing. Please run database migrations or import the initial schema.");
                }

                // Table exists, find actual name in DB
                $idx = array_search($tableLower, $existingTablesLower);
                $actualTableName = $existingTables[$idx];

                // Fetch columns
                $colsStmt = $pdo->query("DESCRIBE `$actualTableName`");
                $existingCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
                $existingColsLower = array_map('strtolower', $existingCols);

                foreach ($columns as $column) {
                    if (!in_array(strtolower($column), $existingColsLower)) {
                        self::logError("Column '$column' is missing in table '$table'.", 'DB_SCHEMA_MISMATCH');
                        self::displayErrorPage('DB_SCHEMA_MISMATCH', "Required column '$column' in table '$table' is missing. The database schema is out of sync.");
                    }
                }
            }
        } catch (PDOException $e) {
            self::logError("Database Connection Failed: " . $e->getMessage(), 'DB_CONNECTION_ERROR');
            self::displayErrorPage('DB_CONNECTION_ERROR', "Could not connect to the database. Verify DB_HOST, DB_USER, DB_PASS, and DB_NAME settings.\nError details: " . $e->getMessage());
        }

        // Store warnings in session for administrator visibility
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['startup_warnings'] = $warnings;
        }
    }

    /**
     * Log error message internally
     */
    public static function logError($message, $code = 'SYSTEM_ERROR') {
        $logDir = APPROOT . '/logs';
        if (!file_exists($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$code] $message\n";
        @file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Display a clean, user-friendly system configuration error page
     */
    private static function displayErrorPage($reference, $details = '') {
        // Clean any output buffers
        if (ob_get_length()) {
            ob_clean();
        }

        http_response_code(500);

        // Render system error page in inline template
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>System Configuration Error</title>
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
                .details-box {
                    background: #0f172a;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    padding: 15px;
                    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                    font-size: 0.85rem;
                    color: #e2e8f0;
                    text-align: left;
                    margin-bottom: 25px;
                    max-height: 150px;
                    overflow-y: auto;
                    white-space: pre-wrap;
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
                <h1>System Configuration Error</h1>
                <p>The application configuration does not match the database schema or server environment. Please contact the system administrator to run migrations or update the configuration.</p>
                
                <?php if (!empty($details)): ?>
                    <div class="details-box"><?php echo htmlspecialchars($details); ?></div>
                <?php endif; ?>

                <div class="mt-2">
                    <span class="ref-badge">Error Reference: <?php echo htmlspecialchars($reference); ?></span>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}
