<?php
/**
 * User Model Class
 * Handles credentials verification, user roles, lockouts, and user record management.
 */
class UserModel extends Model {
    /**
     * Find user by username
     */
    public function findUserByUsername($username) {
        $this->db->query("SELECT u.*, r.name as role_name 
                          FROM users u 
                          JOIN roles r ON u.role_id = r.id 
                          WHERE u.username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    /**
     * Get user details by ID
     */
    public function getUserById($id) {
        $this->db->query("SELECT u.*, r.name as role_name 
                          FROM users u 
                          JOIN roles r ON u.role_id = r.id 
                          WHERE u.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Authenticate user with hashed password
     */
    public function authenticate($username, $password) {
        $user = $this->findUserByUsername($username);
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    /**
     * Update user last login time
     */
    public function updateLastLogin($userId) {
        $this->db->query("UPDATE users SET last_login = NOW() WHERE id = :id");
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * Record a failed login attempt
     */
    public function trackLoginAttempt($ip, $username) {
        $this->db->query("INSERT INTO login_attempts (ip_address, username) VALUES (:ip, :username)");
        $this->db->bind(':ip', $ip);
        $this->db->bind(':username', $username);
        return $this->db->execute();
    }

    /**
     * Count failed login attempts within the last 10 minutes
     */
    public function getLoginAttemptsCount($ip, $username) {
        $this->db->query("SELECT COUNT(*) as count 
                          FROM login_attempts 
                          WHERE (ip_address = :ip OR username = :username) 
                            AND attempt_time > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
        $this->db->bind(':ip', $ip);
        $this->db->bind(':username', $username);
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Clear failed login attempts for a user
     */
    public function clearLoginAttempts($ip, $username) {
        $this->db->query("DELETE FROM login_attempts WHERE ip_address = :ip OR username = :username");
        $this->db->bind(':ip', $ip);
        $this->db->bind(':username', $username);
        return $this->db->execute();
    }

    /**
     * Lock a user account due to too many failed attempts
     */
    public function lockAccount($username) {
        $this->db->query("UPDATE users SET status = 'locked' WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->execute();
    }

    /**
     * Unlock a user account
     */
    public function unlockAccount($id) {
        $this->db->query("UPDATE users SET status = 'active' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Create a new user login account
     */
    public function createUser($username, $password, $roleId, $status = 'active') {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $this->db->query("INSERT INTO users (username, password, role_id, status) 
                          VALUES (:username, :password, :role_id, :status)");
        
        $this->db->bind(':username', $username);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':role_id', $roleId);
        $this->db->bind(':status', $status);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update user details (Role and Status)
     */
    public function updateUser($id, $roleId, $status) {
        $this->db->query("UPDATE users SET role_id = :role_id, status = :status WHERE id = :id");
        $this->db->bind(':role_id', $roleId);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Reset user password
     */
    public function resetPassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->db->query("UPDATE users SET password = :password WHERE id = :id");
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get all users with role information
     */
    public function getAllUsers() {
        $this->db->query("SELECT u.id, u.username, u.status, u.last_login, u.created_at, r.name as role_name, r.id as role_id 
                          FROM users u 
                          JOIN roles r ON u.role_id = r.id 
                          ORDER BY u.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        // Can't delete admin #1
        if ($id == 1) return false;
        
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
