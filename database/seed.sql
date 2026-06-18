-- Database Seeding for SLAF Computer Laboratory Management System
USE itwekala_slaf_clms;

-- Seed Roles
INSERT INTO roles (id, name, description) VALUES
(1, 'Administrator', 'Full system access and control'),
(2, 'Instructor', 'Access to own lessons, allocations, notice board, and fault reporting');

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
(14, 'view_audit_logs', 'Ability to monitor system audit records');

-- Seed Role Permissions
-- Administrator permissions (all permissions)
INSERT INTO role_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions;

-- Instructor permissions
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, 7),  -- view_allocations
(2, 9),  -- submit_requests
(2, 10); -- report_faults

-- Seed Default Admin User
-- Username: admin
-- Password: admin123 (hashed using bcrypt)
INSERT INTO users (id, username, password, role_id, status) VALUES
(1, 'admin', '$2y$10$5d7u2n2edWUnxvi2PN0dW.ZLa0F/BkHMC9zgHO7ZFoPba/6J0ykgm', 1, 'active');

-- Seed System Settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('system_name', 'SLAF CLMS', 'System Name Displayed on Title and Branding'),
('org_name', 'Trade Training School Ekala', 'Parent Organization Name'),
('session_timeout', '1800', 'Session Timeout duration in seconds'),
('max_login_attempts', '5', 'Max failed login attempts before account locking');

-- Seed Sample Laboratories
INSERT INTO laboratories (lab_code, lab_name, location, capacity, description, status) VALUES
('LAB-01', 'Primary Computing Laboratory', 'Main Block, Ground Floor', 30, 'Equipped with Core i5 desktops, projector, and high-speed network.', 'active'),
('LAB-02', 'Advanced Networking Lab', 'Main Block, First Floor', 20, 'Equipped with Cisco routers, switches, and network simulation setups.', 'active'),
('LAB-03', 'Hardware & Electronics Lab', 'Technical Hangar Block', 15, 'Equipped with logic design boards, soldering stations, and diagnostic PCs.', 'active');

-- Seed Sample Instructors (and their corresponding login accounts)
-- Instructor 1: SGT Wijesinghe W.M. (username: sgt.wijesinghe / password: password123)
-- Instructor 2: FG OFF Perera K.A. (username: fg.perera / password: password123)
-- bcrypt hash for password123 is $2y$10$Kdb.f4Z1k7U4xH2J93bEueT4kP6B3S0t7vI1n1V7S0U1l2K8gM12S

INSERT INTO users (id, username, password, role_id, status) VALUES
(2, 'sgt.wijesinghe', '$2y$10$A7I71D6jZ2d6rHgcRQKQy.Hi8Ym1MvsZNjJKV9E9QHem01o4p1LU.', 2, 'active'),
(3, 'fg.perera', '$2y$10$A7I71D6jZ2d6rHgcRQKQy.Hi8Ym1MvsZNjJKV9E9QHem01o4p1LU.', 2, 'active');

INSERT INTO instructors (user_id, service_no, rank, full_name, trade, contact_no, email, status) VALUES
(2, 'S-12345', 'SGT', 'Wijesinghe W.M.', 'IT Specialist', '0771234567', 'sgt.wijesinghe@slaf.lk', 'active'),
(3, 'S-54321', 'FG OFF', 'Perera K.A.', 'Signals & IT', '0719876543', 'fg.perera@slaf.lk', 'active');

-- Seed Sample Computers
INSERT INTO computers (asset_no, serial_no, brand, model, processor, ram, storage, os, purchase_date, warranty_status, lab_id, status) VALUES
('SLAF-PC-001', 'SN-9876543210', 'HP', 'ProDesk 600 G6', 'Intel Core i5-10500', '16GB', '512GB NVMe SSD', 'Windows 11 Pro', '2023-01-15', '3 Years Parts & Labor', 1, 'active'),
('SLAF-PC-002', 'SN-9876543211', 'HP', 'ProDesk 600 G6', 'Intel Core i5-10500', '16GB', '512GB NVMe SSD', 'Windows 11 Pro', '2023-01-15', '3 Years Parts & Labor', 1, 'active'),
('SLAF-PC-003', 'SN-9876543212', 'HP', 'ProDesk 600 G6', 'Intel Core i5-10500', '8GB', '512GB NVMe SSD', 'Windows 10 Pro', '2023-01-15', 'Expired', 1, 'faulty'),
('SLAF-PC-004', 'SN-8876543201', 'Dell', 'OptiPlex 5080', 'Intel Core i7-10700', '32GB', '1TB SSD', 'Ubuntu 22.04 LTS', '2022-06-20', '3 Years Parts & Labor', 2, 'active'),
('SLAF-PC-005', 'SN-8876543202', 'Dell', 'OptiPlex 5080', 'Intel Core i7-10700', '16GB', '512GB SSD', 'Ubuntu 22.04 LTS', '2022-06-20', '3 Years Parts & Labor', 2, 'maintenance');

-- Seed Sample Smart Boards
INSERT INTO smart_boards (asset_id, brand, model, installation_date, lab_id, status) VALUES
('SLAF-SB-001', 'Promethean', 'ActivPanel 9', '2023-03-10', 1, 'active'),
('SLAF-SB-002', 'Smart Technologies', 'MX275-V3', '2024-02-18', 2, 'active');

-- Seed Sample Lessons
INSERT INTO lessons (lesson_code, lesson_name, trade, duration, description) VALUES
('LES-IT01', 'Introduction to Hardware Maintenance', 'IT Specialist', 180, 'Basic concepts of troubleshooting, assembly, and PC upgrades.'),
('LES-NET02', 'Routing Protocols Configuration', 'Signals & IT', 240, 'In-depth lecture and practical labs on OSPF, EIGRP, and BGP routing.'),
('LES-SEC03', 'Basic Cybersecurity Practices', 'All Trades', 120, 'Military cyber security guidelines, threat classification, and password hygiene.');
