# 🛡️ SLAF CLMS — Computer Laboratory Management System

**Sri Lanka Air Force | Trade Training School Ekala**

A web-based PHP MVC system for managing computer laboratory scheduling, equipment inventory, instructor accounts, fault reporting, and system audit logs.

---

## 📋 Table of Contents

1. [System Requirements](#1-system-requirements)
2. [Project Folder Structure](#2-project-folder-structure)
3. [Installation Steps](#3-installation-steps)
   - [Step 1 — Install a Local Server (XAMPP)](#step-1--install-a-local-server-xampp)
   - [Step 2 — Copy the Project Files](#step-2--copy-the-project-files)
   - [Step 3 — Enable URL Rewriting (mod_rewrite)](#step-3--enable-url-rewriting-mod_rewrite)
   - [Step 4 — Create the Database](#step-4--create-the-database)
   - [Step 5 — Import the Database Schema](#step-5--import-the-database-schema)
   - [Step 6 — Import the Seed Data](#step-6--import-the-seed-data)
   - [Step 7 — Configure the Database Connection](#step-7--configure-the-database-connection)
   - [Step 8 — First Login](#step-8--first-login)
4. [Default Login Credentials](#4-default-login-credentials)
5. [Database Tables Overview](#5-database-tables-overview)
6. [Common Errors & Fixes](#6-common-errors--fixes)
7. [Changing the System on a New PC](#7-checklist-for-moving-to-a-new-pc)

---

## 1. System Requirements

| Requirement | Minimum Version |
|---|---|
| **PHP** | 7.4 or higher (8.x recommended) |
| **MySQL / MariaDB** | 5.7 or higher |
| **Apache Web Server** | 2.4 or higher |
| **mod_rewrite** | Must be enabled |
| **Browser** | Chrome, Firefox, Edge (modern version) |

> **Recommended Tool:** [XAMPP](https://www.apachefriends.org/) — bundles Apache + MySQL + PHP together in one installer.

---

## 2. Project Folder Structure

```
Computer Laboratory Management System/
│
├── .htaccess                  ← Root URL rewrite rule (points to /public)
├── README.md                  ← This file
│
├── app/
│   ├── config/
│   │   └── config.php         ← ⚠️ DATABASE SETTINGS ARE HERE
│   ├── controllers/           ← PHP Controller classes
│   ├── core/                  ← MVC engine (App, Controller, Database, Model)
│   ├── helpers/               ← Auth, session, sanitization helpers
│   ├── models/                ← PHP Model classes
│   └── views/                 ← PHP view templates
│
├── database/
│   ├── schema.sql             ← Table structure (run this first)
│   └── seed.sql               ← Default data & admin account (run this second)
│
└── public/
    ├── index.php              ← Application entry point
    ├── css/                   ← Stylesheets
    ├── js/                    ← JavaScript files
    └── images/                ← Image assets (e.g., SLAF crest)
```

---

## 3. Installation Steps

### Step 1 — Install a Local Server (XAMPP)

1. Download XAMPP from: https://www.apachefriends.org/
2. Install it (default path: `C:\xampp` on Windows)
3. Open the **XAMPP Control Panel**
4. Start both **Apache** and **MySQL** services — both status lights should turn **green**

---

### Step 2 — Copy the Project Files

1. Copy the entire project folder:
   ```
   Computer Laboratory Management System
   ```
2. Paste it into XAMPP's web root directory:
   ```
   C:\xampp\htdocs\
   ```
   So the final path becomes:
   ```
   C:\xampp\htdocs\Computer Laboratory Management System\
   ```

> ⚠️ **Tip:** If your folder name has spaces, you can rename it to something simpler like `clms` for easier URL access:
> ```
> C:\xampp\htdocs\clms\
> ```

---

### Step 3 — Enable URL Rewriting (mod_rewrite)

The system uses clean URLs (e.g., `/instructor/register`). Apache's `mod_rewrite` must be enabled.

**Method A — Via XAMPP (easiest):**
1. Open XAMPP Control Panel
2. Click **Config** next to Apache → click **httpd.conf**
3. Find this line:
   ```
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Remove the `#` at the beginning so it becomes:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
5. Also search for `AllowOverride None` and change it to `AllowOverride All`
6. **Save** the file and **Restart Apache** in XAMPP

**Method B — Quick check:**
- Open your browser and go to: `http://localhost/clms/`
- If you see the login page → mod_rewrite is already working ✅
- If you see a **403 Forbidden** or blank page → follow Method A above

---

### Step 4 — Create the Database

**Option A — Using phpMyAdmin (Recommended for beginners):**

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** in the left sidebar
3. In the **Database name** field, type:
   ```
   slaf_clms
   ```
4. Set the collation to:
   ```
   utf8mb4_unicode_ci
   ```
5. Click **Create**

**Option B — Using MySQL Command Line:**

1. Open Command Prompt (Windows) or Terminal
2. Log in to MySQL:
   ```bash
   mysql -u root -p
   ```
   *(Press Enter when it asks for password — default XAMPP has no password)*
3. Run:
   ```sql
   CREATE DATABASE slaf_clms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Type `exit` to quit

---

### Step 5 — Import the Database Schema

This creates all the tables in the database.

**Option A — Using phpMyAdmin:**

1. Go to `http://localhost/phpmyadmin`
2. Click on **`slaf_clms`** in the left sidebar
3. Click the **Import** tab at the top
4. Click **"Choose File"** and select:
   ```
   database/schema.sql
   ```
5. Click **Go** — you should see a success message

**Option B — Using Command Line:**

```bash
mysql -u root -p slaf_clms < "C:\xampp\htdocs\Computer Laboratory Management System\database\schema.sql"
```

---

### Step 6 — Import the Seed Data

This inserts the default admin account, roles, sample labs, and sample instructors.

> ⚠️ **Important:** Run the **schema.sql first** before seed.sql. Do not skip Step 5.

**Option A — Using phpMyAdmin:**

1. Go to `http://localhost/phpmyadmin`
2. Click on **`slaf_clms`** in the left sidebar
3. Click the **Import** tab
4. Click **"Choose File"** and select:
   ```
   database/seed.sql
   ```
5. Click **Go**

**Option B — Using Command Line:**

```bash
mysql -u root -p slaf_clms < "C:\xampp\htdocs\Computer Laboratory Management System\database\seed.sql"
```

---

### Step 7 — Configure the Database Connection

Open the configuration file:
```
app/config/config.php
```

Find and update these lines to match your MySQL settings:

```php
// Database Parameters
define('DB_HOST', 'localhost');   // Usually 'localhost' — don't change unless needed
define('DB_USER', 'root');        // Your MySQL username (default: root)
define('DB_PASS', '');            // Your MySQL password (default: empty for XAMPP)
define('DB_NAME', 'slaf_clms');   // The database name you created in Step 4
```

**Common scenarios:**

| Scenario | DB_USER | DB_PASS |
|---|---|---|
| Fresh XAMPP install | `root` | *(leave empty)* |
| XAMPP with password set | `root` | `your_password` |
| Online hosting (cPanel) | `yourhost_dbuser` | `your_db_password` |
| Custom MySQL install | `your_username` | `your_password` |

> 💡 **Do not** change `DB_HOST` unless your MySQL is running on a different server.

---

### Step 8 — First Login

1. Open your browser and navigate to:
   ```
   http://localhost/Computer Laboratory Management System/
   ```
   Or if you renamed the folder:
   ```
   http://localhost/clms/
   ```

2. You should see the **SLAF CLMS Login Page**

3. Log in using the default admin credentials:
   ```
   Username : admin
   Password : admin
   ```

4. ✅ **After first login, immediately go to:**
   - Top-right profile dropdown → **"Change My Password"**
   - Change the admin password to a strong, unique password

---

## 4. Default Login Credentials

> ⚠️ **Security Warning:** Change all default passwords immediately after installation!

| Role | Username | Password | Notes |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Full system access |
| Sample Instructor | `sgt.jhon` | `password123` | Sample data only |
| Sample Instructor | `fg.kumara` | `password123` | Sample data only |

---

## 5. Database Tables Overview

The `slaf_clms` database contains **18 tables**:

| # | Table | Purpose |
|---|---|---|
| 1 | `roles` | User roles (Administrator, Instructor) |
| 2 | `permissions` | System permission definitions |
| 3 | `role_permissions` | Links roles to their permissions |
| 4 | `users` | Login accounts with hashed passwords |
| 5 | `login_attempts` | Tracks failed logins (brute-force protection) |
| 6 | `instructors` | Instructor service records linked to users |
| 7 | `laboratories` | Computer lab rooms and capacity |
| 8 | `computers` | Computer inventory per lab |
| 9 | `smart_boards` | Smart board inventory per lab |
| 10 | `lessons` | Syllabus lessons for allocation |
| 11 | `allocations` | Lab scheduling assignments |
| 12 | `allocation_requests` | Change/reschedule requests by instructors |
| 13 | `fault_reports` | Equipment fault tickets |
| 14 | `maintenance_records` | Maintenance and repair records |
| 15 | `notifications` | In-app notification messages |
| 16 | `notices` | Published notice board announcements |
| 17 | `audit_logs` | System-wide activity audit trail |
| 18 | `system_settings` | Key-value system configuration |

---

## 6. Common Errors & Fixes

### ❌ Error: "Connection Failed" or blank white page

**Cause:** Wrong database credentials in `config.php`  
**Fix:** Open `app/config/config.php` and verify `DB_USER`, `DB_PASS`, `DB_NAME` are correct. Also make sure MySQL is running in XAMPP.

---

### ❌ Error: 404 Not Found on any page after login

**Cause:** `mod_rewrite` is not enabled or `.htaccess` is not being read  
**Fix:** Follow [Step 3](#step-3--enable-url-rewriting-mod_rewrite) above to enable `mod_rewrite` and set `AllowOverride All`.

---

### ❌ Error: "Access forbidden" (403)

**Cause:** Apache cannot read the project folder  
**Fix:**
1. Right-click the project folder → Properties → Security
2. Give your user (and `SYSTEM`) **Full Control**
3. Restart Apache

---

### ❌ Error: "Table doesn't exist"

**Cause:** `schema.sql` was not imported  
**Fix:** Repeat [Step 5](#step-5--import-the-database-schema) to import the schema

---

### ❌ Login says "Invalid credentials" with correct password

**Cause:** `seed.sql` was not imported or was imported to the wrong database  
**Fix:** Make sure you clicked on `slaf_clms` in phpMyAdmin before importing `seed.sql`. Repeat [Step 6](#step-6--import-the-seed-data).

---

### ❌ Images (SLAF Crest) not showing

**Cause:** The `Picture1.png` image is missing from `public/images/`  
**Fix:** Copy `Picture1.png` to:
```
public/images/Picture1.png
```

---

## 7. Checklist for Moving to a New PC

Use this checklist every time you set up the project on a new machine:

```
[ ] 1. XAMPP installed and Apache + MySQL are running
[ ] 2. Project folder copied to C:\xampp\htdocs\
[ ] 3. mod_rewrite enabled and AllowOverride All set in httpd.conf
[ ] 4. Database 'slaf_clms' created in phpMyAdmin
[ ] 5. database/schema.sql imported successfully
[ ] 6. database/seed.sql imported successfully
[ ] 7. app/config/config.php updated with correct DB credentials
[ ] 8. public/images/Picture1.png is present (SLAF crest)
[ ] 9. Login tested with: admin / admin123
[ ] 10. Admin password changed immediately after first login
```

---

## 📁 Files That Need Editing on Each New Installation

| File | What to Change |
|---|---|
| `app/config/config.php` | `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` |
| `C:\xampp\apache\conf\httpd.conf` | Enable `mod_rewrite`, set `AllowOverride All` |

---

*SLAF CLMS v1.0 — Sri Lanka Air Force, Trade Training School Ekala*  
*For technical support, contact the system administrator.*
