-- ============================================================
-- SQL RENAME SCRIPT (ALTERNATIVE METHOD)
-- ============================================================
-- Note: MySQL/MariaDB does not support "RENAME DATABASE".
-- The standard method is using mysqldump and mysql import (refer to rename_db.bat).
--
-- If you want to rename tables individually, execute the following SQL:
-- ============================================================

CREATE DATABASE IF NOT EXISTS itwekala_slaf_clms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

RENAME TABLE slaf_clms.roles TO itwekala_slaf_clms.roles;
RENAME TABLE slaf_clms.permissions TO itwekala_slaf_clms.permissions;
RENAME TABLE slaf_clms.role_permissions TO itwekala_slaf_clms.role_permissions;
RENAME TABLE slaf_clms.users TO itwekala_slaf_clms.users;
RENAME TABLE slaf_clms.login_attempts TO itwekala_slaf_clms.login_attempts;
RENAME TABLE slaf_clms.instructors TO itwekala_slaf_clms.instructors;
RENAME TABLE slaf_clms.laboratories TO itwekala_slaf_clms.laboratories;
RENAME TABLE slaf_clms.computers TO itwekala_slaf_clms.computers;
RENAME TABLE slaf_clms.smart_boards TO itwekala_slaf_clms.smart_boards;
RENAME TABLE slaf_clms.lessons TO itwekala_slaf_clms.lessons;
RENAME TABLE slaf_clms.allocations TO itwekala_slaf_clms.allocations;
RENAME TABLE slaf_clms.allocation_requests TO itwekala_slaf_clms.allocation_requests;
RENAME TABLE slaf_clms.fault_reports TO itwekala_slaf_clms.fault_reports;
RENAME TABLE slaf_clms.maintenance_records TO itwekala_slaf_clms.maintenance_records;
RENAME TABLE slaf_clms.notifications TO itwekala_slaf_clms.notifications;
RENAME TABLE slaf_clms.notices TO itwekala_slaf_clms.notices;
RENAME TABLE slaf_clms.audit_logs TO itwekala_slaf_clms.audit_logs;
RENAME TABLE slaf_clms.system_settings TO itwekala_slaf_clms.system_settings;
RENAME TABLE slaf_clms.password_history TO itwekala_slaf_clms.password_history;
