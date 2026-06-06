<?php
/**
 * System Controller
 * Handles system health check and administrative diagnostics.
 */
class SystemController extends Controller {
    public function __construct() {
        requireAdmin();
    }

    public function health() {
        $results = [];

        // 1. PHP Version Check
        $phpVersion = PHP_VERSION;
        $phpPass = version_compare($phpVersion, '8.0.0', '>=');
        $results['php_version'] = [
            'name' => 'PHP Version',
            'value' => $phpVersion,
            'status' => $phpPass ? 'PASS' : 'WARNING',
            'message' => $phpPass ? 'PHP version is compatible (8.0+ required).' : 'PHP version is below 8.0. Update recommended.'
        ];

        // 2. PHP Extensions Check
        $requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo', 'mbstring', 'openssl'];
        $missingExtensions = [];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            }
        }
        $results['extensions'] = [
            'name' => 'Required PHP Extensions',
            'value' => implode(', ', array_diff($requiredExtensions, $missingExtensions)),
            'status' => empty($missingExtensions) ? 'PASS' : 'ERROR',
            'message' => empty($missingExtensions) ? 'All required extensions are loaded.' : 'Missing required extensions: ' . implode(', ', $missingExtensions)
        ];

        // 3. Database Connection Check
        $dbConnected = false;
        $dbErrorMsg = '';
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $dbConnected = true;
        } catch (PDOException $e) {
            $dbErrorMsg = $e->getMessage();
        }

        $results['db_connection'] = [
            'name' => 'Database Connection',
            'value' => DB_HOST . ' / ' . DB_NAME,
            'status' => $dbConnected ? 'PASS' : 'ERROR',
            'message' => $dbConnected ? 'Database connection successfully established.' : 'Failed to connect: ' . $dbErrorMsg
        ];

        // 4. Schema Verification (Tables and Columns)
        $schemaStatus = 'PASS';
        $schemaMsg = 'All required tables and columns are present.';
        $schemaDetails = [];
        $requiredSchemaCount = 0;
        
        $requiredSchema = [
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

        if ($dbConnected) {
            $requiredSchemaCount = count($requiredSchema);

            try {
                $tablesStmt = $pdo->query("SHOW TABLES");
                $existingTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
                $existingTablesLower = array_map('strtolower', $existingTables);

                foreach ($requiredSchema as $table => $columns) {
                    $tableLower = strtolower($table);
                    if (!in_array($tableLower, $existingTablesLower)) {
                        $schemaStatus = 'ERROR';
                        $schemaDetails[] = "Table '$table' is missing.";
                        continue;
                    }

                    $idx = array_search($tableLower, $existingTablesLower);
                    $actualTableName = $existingTables[$idx];

                    $colsStmt = $pdo->query("DESCRIBE `$actualTableName`");
                    $existingCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
                    $existingColsLower = array_map('strtolower', $existingCols);

                    foreach ($columns as $column) {
                        if (!in_array(strtolower($column), $existingColsLower)) {
                            $schemaStatus = 'ERROR';
                            $schemaDetails[] = "Column '$column' is missing in table '$table'.";
                        }
                    }
                }

                if (!empty($schemaDetails)) {
                    $schemaMsg = implode('<br>', $schemaDetails);
                }
            } catch (PDOException $e) {
                $schemaStatus = 'ERROR';
                $schemaMsg = 'Failed to inspect schema: ' . $e->getMessage();
            }
        } else {
            $schemaStatus = 'ERROR';
            $schemaMsg = 'Unable to check schema due to database connection failure.';
        }

        $results['database_schema'] = [
            'name' => 'Database Schema Integrity',
            'value' => $requiredSchemaCount . ' tables checked',
            'status' => $schemaStatus,
            'message' => $schemaMsg
        ];

        // 5. Directories and Writable check
        $uploadBase = APPROOT . '/../uploads';
        $uploadInstructors = $uploadBase . '/instructors';
        $logsDir = APPROOT . '/logs';

        $dirChecks = [
            'Upload Base' => $uploadBase,
            'Instructors Upload' => $uploadInstructors,
            'System Logs' => $logsDir
        ];

        $dirsStatus = 'PASS';
        $dirsMsg = [];

        foreach ($dirChecks as $name => $path) {
            if (!file_exists($path)) {
                $dirsStatus = 'ERROR';
                $dirsMsg[] = "$name directory is missing.";
            } elseif (!is_writable($path)) {
                if ($dirsStatus !== 'ERROR') $dirsStatus = 'WARNING';
                $dirsMsg[] = "$name directory is not writable (Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . ").";
            } else {
                $dirsMsg[] = "$name directory is ready and writable.";
            }
        }

        $results['directories'] = [
            'name' => 'Directory & Write Permissions',
            'value' => count($dirChecks) . ' directories verified',
            'status' => $dirsStatus,
            'message' => implode('<br>', $dirsMsg)
        ];

        // Render page
        $data = [
            'title' => 'System Health Check',
            'active_menu' => 'system_health',
            'results' => $results
        ];

        $this->view('templates/header', $data);
        $this->view('system/health', $data);
        $this->view('templates/footer', $data);
    }
}
