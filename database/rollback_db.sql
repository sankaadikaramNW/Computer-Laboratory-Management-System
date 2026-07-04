-- ============================================================
-- SQL ROLLBACK SCRIPT (ALTERNATIVE METHOD)
-- ============================================================
-- If you used the table-renaming SQL script to rename the database,
-- you can revert the tables back using the following SQL:
-- ============================================================

CREATE DATABASE IF NOT EXISTS slaf_clms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

RENAME TABLE itwekala_slaf_clms.roles TO slaf_clms.roles;
RENAME TABLE itwekala_slaf_clms.permissions TO slaf_clms.permissions;
RENAME TABLE itwekala_slaf_clms.role_permissions TO slaf_clms.role_permissions;
RENAME TABLE itwekala_slaf_clms.users TO slaf_clms.users;
RENAME TABLE itwekala_slaf_clms.login_attempts TO slaf_clms.login_attempts;
RENAME TABLE itwekala_slaf_clms.instructors TO slaf_clms.instructors;
RENAME TABLE itwekala_slaf_clms.laboratories TO slaf_clms.laboratories;
RENAME TABLE itwekala_slaf_clms.computers TO slaf_clms.computers;
RENAME TABLE itwekala_slaf_clms.smart_boards TO slaf_clms.smart_boards;
RENAME TABLE itwekala_slaf_clms.lessons TO slaf_clms.lessons;
RENAME TABLE itwekala_slaf_clms.allocations TO slaf_clms.allocations;
RENAME TABLE itwekala_slaf_clms.allocation_requests TO slaf_clms.allocation_requests;
RENAME TABLE itwekala_slaf_clms.fault_reports TO slaf_clms.fault_reports;
RENAME TABLE itwekala_slaf_clms.maintenance_records TO slaf_clms.maintenance_records;
RENAME TABLE itwekala_slaf_clms.notifications TO slaf_clms.notifications;
RENAME TABLE itwekala_slaf_clms.notices TO slaf_clms.notices;
RENAME TABLE itwekala_slaf_clms.audit_logs TO slaf_clms.audit_logs;
RENAME TABLE itwekala_slaf_clms.system_settings TO slaf_clms.system_settings;
RENAME TABLE itwekala_slaf_clms.password_history TO slaf_clms.password_history;
