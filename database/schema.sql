-- SLAF Computer Laboratory Management System Database Schema & Initial Seed
CREATE DATABASE IF NOT EXISTS clms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clms;

-- =========================================================================
-- SECTION 1: DATABASE TABLES SCHEMA
-- =========================================================================

-- 1. Roles Table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Permissions Table
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB;

-- 3. Role Permissions (Junction Table)
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Camps Table
CREATE TABLE IF NOT EXISTS camps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(50) NOT NULL UNIQUE,
    address TEXT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive', 'locked') DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_password_change DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    failed_attempts INT DEFAULT 0,
    force_password_change TINYINT(1) DEFAULT 0,
    password_expiry_days INT DEFAULT 90,
    camp_id INT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (camp_id) REFERENCES camps(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. Login Attempts Table (Brute-force prevention)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. Instructors Table
CREATE TABLE IF NOT EXISTS instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL UNIQUE,
    service_no VARCHAR(20) NOT NULL UNIQUE,
    `rank` VARCHAR(20) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    trade VARCHAR(50) NOT NULL,
    contact_no VARCHAR(20),
    email VARCHAR(100),
    profile_photo VARCHAR(255) NULL,
    photo_uploaded_at TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. Laboratories Table
CREATE TABLE IF NOT EXISTS laboratories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_code VARCHAR(20) NOT NULL UNIQUE,
    lab_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    camp_id INT NULL,
    FOREIGN KEY (camp_id) REFERENCES camps(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 9. Computers Table
CREATE TABLE IF NOT EXISTS computers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_no VARCHAR(50) NOT NULL UNIQUE,
    serial_no VARCHAR(100) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    processor VARCHAR(50) NOT NULL,
    ram VARCHAR(20) NOT NULL,
    storage VARCHAR(50) NOT NULL,
    os VARCHAR(50) NOT NULL,
    purchase_date DATE NULL,
    warranty_status VARCHAR(100),
    lab_id INT NULL,
    status ENUM('active', 'faulty', 'maintenance', 'removed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lab_id) REFERENCES laboratories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 10. Smart Boards Table
CREATE TABLE IF NOT EXISTS smart_boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id VARCHAR(50) NOT NULL UNIQUE,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    installation_date DATE NULL,
    lab_id INT NULL,
    status ENUM('active', 'faulty', 'maintenance', 'removed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lab_id) REFERENCES laboratories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. Lessons Table
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_code VARCHAR(20) NOT NULL UNIQUE,
    lesson_name VARCHAR(150) NOT NULL,
    trade VARCHAR(50) NOT NULL,
    duration INT NOT NULL, -- Duration in minutes
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 12. Laboratory Allocations Table
CREATE TABLE IF NOT EXISTS allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    lesson_id INT NOT NULL,
    lab_id INT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    remarks TEXT,
    session_status VARCHAR(50) NOT NULL DEFAULT 'Scheduled',
    instructor_remarks TEXT NULL,
    completed_at DATETIME NULL,
    completed_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    camp_id INT NULL,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (lab_id) REFERENCES laboratories(id) ON DELETE CASCADE,
    FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (camp_id) REFERENCES camps(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 13. Allocation Requests (Change Requests) Table
CREATE TABLE IF NOT EXISTS allocation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    allocation_id INT NULL,
    requester_id INT NOT NULL, -- user_id of requester
    type ENUM('reschedule', 'cancel', 'change_lab') NOT NULL,
    new_date DATE NULL,
    new_start_time TIME NULL,
    new_end_time TIME NULL,
    new_lab_id INT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewer_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (allocation_id) REFERENCES allocations(id) ON DELETE SET NULL,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (new_lab_id) REFERENCES laboratories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 14. Equipment Fault Reports Table
CREATE TABLE IF NOT EXISTS fault_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reported_by INT NOT NULL, -- user_id of instructor
    equipment_type ENUM('computer', 'smart_board', 'network', 'other') NOT NULL,
    equipment_id INT NULL, -- ID pointing to computers or smart_boards if applicable
    description TEXT NOT NULL,
    status ENUM('reported', 'in_progress', 'resolved', 'closed') DEFAULT 'reported',
    resolution_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 15. Maintenance Management Table
CREATE TABLE IF NOT EXISTS maintenance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_type ENUM('computer', 'smart_board', 'network', 'other') NOT NULL,
    equipment_id INT NULL, -- computer_id or smart_board_id
    issue_type VARCHAR(100) NOT NULL,
    assigned_technician VARCHAR(100) NOT NULL,
    repair_date DATE NOT NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 16. Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    type VARCHAR(50) NOT NULL, -- e.g. allocation_change, fault_update, request_status, notice
    related_id INT NULL, -- Related item ID (e.g. allocation_id, fault_id)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 17. Notices Table (Digital Notice Board)
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    published_by INT NOT NULL,
    status ENUM('active', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 18. Audit Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 19. System Settings Table
CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 20. Password History Table
CREATE TABLE IF NOT EXISTS password_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================================
-- SECTION 2: SEED DATA INSERTIONS
-- =========================================================================

-- Seed Roles
INSERT INTO roles (id, name, description) VALUES
(1, 'Administrator', 'Full system access and control'),
(2, 'Instructor', 'Access to own lessons, allocations, notice board, and fault reporting')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- Seed Permissions
INSERT INTO permissions (id, name, description) VALUES
(1, 'manage_users', 'Ability to create, update, lock user accounts'),
(2, 'manage_instructors', 'Ability to manage instructor service records'),
(3, 'manage_laboratories', 'Ability to add, edit, or deactivate laboratory rooms'),
(4, 'manage_equipment', 'Ability to manage computers and smart boards'),
(5, 'manage_lessons', 'Ability to manage syllabus lessons'),
(6, 'manage_allocations', 'Ability to schedule and allocate labs for instructors'),
(7, 'view_allocations', 'Ability to view laboratory calendar and scheduling'),
(8, 'manage_requests', 'Ability to approve or reject change requests'),
(9, 'submit_requests', 'Ability to submit date/time change requests'),
(10, 'report_faults', 'Ability to submit fault reports for equipment'),
(11, 'manage_faults', 'Ability to handle and update status of fault tickets'),
(12, 'manage_maintenance', 'Ability to schedule technicians and repairs'),
(13, 'view_reports', 'Ability to generate lab utilization and instructor workload logs'),
(14, 'view_audit_logs', 'Ability to monitor system audit records')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- Seed Role Permissions
-- Administrator permissions (all permissions)
INSERT INTO role_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Instructor permissions
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, 7),  -- view_allocations
(2, 9),  -- submit_requests
(2, 10)  -- report_faults
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Seed Camps
INSERT INTO camps (id, name, code, address, status) VALUES
(1, 'SLAF Trade Training School Ekala', 'TTS-EKA', 'Ekala, Ja-Ela, Sri Lanka', 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), code=VALUES(code), address=VALUES(address), status=VALUES(status);

-- Seed Default Admin User
-- Username: admin
-- Password: admin123 (hashed using bcrypt)
INSERT INTO users (id, username, password, role_id, status, camp_id) VALUES
(1, 'admin', '$2y$10$5d7u2n2edWUnxvi2PN0dW.ZLa0F/BkHMC9zgHO7ZFoPba/6J0ykgm', 1, 'active', 1)
ON DUPLICATE KEY UPDATE username=VALUES(username), password=VALUES(password), role_id=VALUES(role_id), status=VALUES(status), camp_id=VALUES(camp_id);

-- Seed System Settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('system_name', 'SLAF CLMS', 'System Name Displayed on Title and Branding'),
('org_name', 'Trade Training School Ekala', 'Parent Organization Name'),
('session_timeout', '1800', 'Session Timeout duration in seconds'),
('max_login_attempts', '5', 'Max failed login attempts before account locking')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), description=VALUES(description);

-- Seed Sample Laboratories
INSERT INTO laboratories (lab_code, lab_name, location, capacity, description, status, camp_id) VALUES
('LAB-01', 'Primary Computing Laboratory', 'Main Block, Ground Floor', 30, 'Equipped with Core i5 desktops, projector, and high-speed network.', 'active', 1),
('LAB-02', 'Advanced Networking Lab', 'Main Block, First Floor', 20, 'Equipped with Cisco routers, switches, and network simulation setups.', 'active', 1),
('LAB-03', 'Hardware & Electronics Lab', 'Technical Hangar Block', 15, 'Equipped with logic design boards, soldering stations, and diagnostic PCs.', 'active', 1)
ON DUPLICATE KEY UPDATE lab_name=VALUES(lab_name), location=VALUES(location), capacity=VALUES(capacity), description=VALUES(description), status=VALUES(status), camp_id=VALUES(camp_id);

-- Seed Sample Instructors (and their corresponding login accounts)
-- Instructor 1: SGT Wijesinghe W.M. (username: sgt.wijesinghe / password: password123)
-- Instructor 2: FG OFF Perera K.A. (username: fg.perera / password: password123)
-- bcrypt hash for password123 is $2y$10$Kdb.f4Z1k7U4xH2J93bEueT4kP6B3S0t7vI1n1V7S0U1l2K8gM12S
INSERT INTO users (id, username, password, role_id, status, camp_id) VALUES
(2, 'sgt.wijesinghe', '$2y$10$A7I71D6jZ2d6rHgcRQKQy.Hi8Ym1MvsZNjJKV9E9QHem01o4p1LU.', 2, 'active', 1),
(3, 'fg.perera', '$2y$10$A7I71D6jZ2d6rHgcRQKQy.Hi8Ym1MvsZNjJKV9E9QHem01o4p1LU.', 2, 'active', 1)
ON DUPLICATE KEY UPDATE username=VALUES(username), password=VALUES(password), role_id=VALUES(role_id), status=VALUES(status), camp_id=VALUES(camp_id);

INSERT INTO instructors (user_id, service_no, `rank`, full_name, trade, contact_no, email, status) VALUES
(2, 'S-12345', 'SGT', 'Wijesinghe W.M.', 'IT Specialist', '0771234567', 'sgt.wijesinghe@slaf.lk', 'active'),
(3, 'S-54321', 'FG OFF', 'Perera K.A.', 'Signals & IT', '0719876543', 'fg.perera@slaf.lk', 'active')
ON DUPLICATE KEY UPDATE service_no=VALUES(service_no), `rank`=VALUES(`rank`), full_name=VALUES(full_name), trade=VALUES(trade), contact_no=VALUES(contact_no), email=VALUES(email), status=VALUES(status);

-- Seed Sample Computers
INSERT INTO computers (asset_no, serial_no, brand, model, processor, ram, storage, os, purchase_date, warranty_status, lab_id, status) VALUES
('SLAF-PC-001', 'SN-9876543210', 'HP', 'ProDesk 600 G6', 'Intel Core i5-10500', '16GB', '512GB NVMe SSD', 'Windows 11 Pro', '2023-01-15', '3 Years Parts & Labor', 1, 'active'),
('SLAF-PC-002', 'SN-9876543211', 'HP', 'ProDesk 600 G6', 'Intel Core i5-10500', '16GB', '512GB NVMe SSD', 'Windows 11 Pro', '2023-01-15', '3 Years Parts & Labor', 1, 'active'),
('SLAF-PC-003', 'SN-9876543212', 'HP', 'ProDesk 600 G6', 'Intel Core i5-10500', '8GB', '512GB NVMe SSD', 'Windows 10 Pro', '2023-01-15', 'Expired', 1, 'faulty'),
('SLAF-PC-004', 'SN-8876543201', 'Dell', 'OptiPlex 5080', 'Intel Core i7-10700', '32GB', '1TB SSD', 'Ubuntu 22.04 LTS', '2022-06-20', '3 Years Parts & Labor', 2, 'active'),
('SLAF-PC-005', 'SN-8876543202', 'Dell', 'OptiPlex 5080', 'Intel Core i7-10700', '16GB', '512GB SSD', 'Ubuntu 22.04 LTS', '2022-06-20', '3 Years Parts & Labor', 2, 'maintenance')
ON DUPLICATE KEY UPDATE serial_no=VALUES(serial_no), brand=VALUES(brand), model=VALUES(model), processor=VALUES(processor), ram=VALUES(ram), storage=VALUES(storage), os=VALUES(os), purchase_date=VALUES(purchase_date), warranty_status=VALUES(warranty_status), lab_id=VALUES(lab_id), status=VALUES(status);

-- Seed Sample Smart Boards
INSERT INTO smart_boards (asset_id, brand, model, installation_date, lab_id, status) VALUES
('SLAF-SB-001', 'Promethean', 'ActivPanel 9', '2023-03-10', 1, 'active'),
('SLAF-SB-002', 'Smart Technologies', 'MX275-V3', '2024-02-18', 2, 'active')
ON DUPLICATE KEY UPDATE brand=VALUES(brand), model=VALUES(model), installation_date=VALUES(installation_date), lab_id=VALUES(lab_id), status=VALUES(status);

-- Seed Sample Lessons
INSERT INTO lessons (lesson_code, lesson_name, trade, duration, description) VALUES
('LES-IT01', 'Introduction to Hardware Maintenance', 'IT Specialist', 180, 'Basic concepts of troubleshooting, assembly, and PC upgrades.'),
('LES-NET02', 'Routing Protocols Configuration', 'Signals & IT', 240, 'In-depth lecture and practical labs on OSPF, EIGRP, and BGP routing.'),
('LES-SEC03', 'Basic Cybersecurity Practices', 'All Trades', 120, 'Military cyber security guidelines, threat classification, and password hygiene.')
ON DUPLICATE KEY UPDATE lesson_name=VALUES(lesson_name), trade=VALUES(trade), duration=VALUES(duration), description=VALUES(description);
