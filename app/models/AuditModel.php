<?php
/**
 * Audit Model Class
 * Inserts actions and changes made by users to the database for accountability.
 */
class AuditModel extends Model {
    /**
     * Log a user activity
     */
    public function log($userId, $action, $module, $ipAddress, $details = '') {
        $this->db->query("INSERT INTO audit_logs (user_id, action, module, ip_address, details) 
                          VALUES (:user_id, :action, :module, :ip_address, :details)");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':action', $action);
        $this->db->bind(':module', $module);
        $this->db->bind(':ip_address', $ipAddress);
        $this->db->bind(':details', $details);
        
        return $this->db->execute();
    }

    /**
     * Retrieve login history for a specific user
     */
    public function getUserLoginLogs($userId, $username, $limit = 100) {
        $this->db->query("SELECT * FROM audit_logs 
                          WHERE (user_id = :user_id AND (action = 'LOGIN' OR action = 'LOGOUT' OR action = 'PASSWORD_CHANGED')) 
                             OR (details LIKE :username_lock AND action = 'ACCOUNT_LOCKED')
                          ORDER BY created_at DESC 
                          LIMIT :limit");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':username_lock', '%' . $username . '%');
        $this->db->bind(':limit', (int)$limit);
        return $this->db->resultSet();
    }

    /**
     * Retrieve audit history (latest first)
     */
    public function getAllLogs() {
        $this->db->query("SELECT a.*, u.username, r.name as role_name 
                          FROM audit_logs a 
                          LEFT JOIN users u ON a.user_id = u.id 
                          LEFT JOIN roles r ON u.role_id = r.id 
                          ORDER BY a.created_at DESC 
                          LIMIT 1000");
        return $this->db->resultSet();
    }

    /**
     * Clear logs older than a specific date (optional maintenance feature)
     */
    public function clearOldLogs($days) {
        $this->db->query("DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        return $this->db->execute();
    }
}
