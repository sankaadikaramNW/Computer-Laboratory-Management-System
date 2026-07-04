# Implementation Plan: Move DB Credentials to .env and Allow Login Page to Load Gracefully

This plan details the changes to move hardcoded database credentials to a `.env` file and handle missing/unconfigured database states gracefully so that the login page can load without showing a system configuration error page.

## User Review Required

> [!IMPORTANT]
> **Security Protection**: Since the `.env` file resides in the application's root directory, we will add a rule in `.htaccess` to block direct access to it from the browser. This prevents exposing database credentials to visitors.
>
> **Fallback Behavior**: If the database is missing or connection fails, the login page will load, but it will display a warning banner indicating that system functions are unavailable or the database connection failed. If a user attempts to sign in while the database is down, the form will refresh with a red error alert instead of crashing with a 500 error page.

## Open Questions

No open questions. The requirements are clear and follow standard MVC web app practices.

## Proposed Changes

---

### Environment Configuration & Routing Security

#### [NEW] [.env](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/.env)
Define database credentials locally.
```ini
DB_HOST=localhost
DB_USER=root
DB_PASS=""
DB_NAME=slaf_clms
```

#### [NEW] [.env.example](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/.env.example)
Create a template for environment configuration.
```ini
DB_HOST=localhost
DB_USER=root
DB_PASS=""
DB_NAME=slaf_clms
```

#### [MODIFY] [.gitignore](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/.gitignore)
Ignore the `.env` file to prevent committing credentials.
```diff
+ # ── Credentials & Environment ──────────────────────────────────────────────
+ .env
```

#### [MODIFY] [.htaccess](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/.htaccess)
Add a rule to prevent public browser access to `.env` files.
```diff
+ # Prevent access to .env files
+ <FilesMatch "^\.env">
+     Order allow,deny
+     Deny from all
+ </FilesMatch>
```

---

### Core Refactoring & Application Initialization

#### [MODIFY] [config.php](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/app/config/config.php)
Implement a simple `.env` file parser to read database credentials and fall back to defaults if not set.
```php
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

// Database Parameters (from environment or defaults)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'slaf_clms');
```

#### [MODIFY] [Database.php](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/app/core/Database.php)
Make database connections lazy. Instead of establishing a connection inside the constructor immediately, establish it only when query execution is triggered. This prevents connection failure exceptions during page load/rendering.

#### [MODIFY] [StartupValidator.php](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/app/core/StartupValidator.php)
Adjust startup checking logic: if the current request is for the login page and the database connection fails or the schema is missing, log the event and set a flash warning banner instead of showing a system error screen.

#### [MODIFY] [AuthController.php](file:///c:/xampp/htdocs/Computer-Laboratory-Management-System/app/controllers/AuthController.php)
Wrap the POST authentication logic in a `try...catch` block. If the database connection fails or tables are missing, capture the exception, flash a red alert message, and reload the login page cleanly.

---

## Verification Plan

### Automated Tests
N/A (The project does not have automated PHPUnit/testing suites).

### Manual Verification
1. Rename/delete the local database or change the password in `.env` to trigger a connection failure.
2. Load the project root. Verify that the login page loads normally and shows a system warning banner ("System Warning: Database connection failed...").
3. Try to log in. Verify that the system fails gracefully showing "Database connection error. Please verify the database is active and schema is imported" without a 500 error screen.
4. Restore correct database configurations and verify that logging in functions correctly and accesses the dashboard.
