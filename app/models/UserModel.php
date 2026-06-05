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
        $this->db->query("UPDATE users SET status = 'active', failed_attempts = 0 WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Lock a user account
     */
    public function lockUserAccount($id) {
        $this->db->query("UPDATE users SET status = 'locked' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Deactivate a user account
     */
    public function deactivateUserAccount($id) {
        $this->db->query("UPDATE users SET status = 'inactive' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Activate a user account
     */
    public function activateUserAccount($id) {
        $this->db->query("UPDATE users SET status = 'active' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Create a new user login account
     */
    public function createUser($username, $password, $roleId, $status = 'active', $forcePasswordChange = 0, $passwordExpiryDays = 90) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $this->db->query("INSERT INTO users (username, password, role_id, status, force_password_change, password_expiry_days, last_password_change) 
                          VALUES (:username, :password, :role_id, :status, :force_password_change, :password_expiry_days, NOW())");
        
        $this->db->bind(':username', $username);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':role_id', $roleId);
        $this->db->bind(':status', $status);
        $this->db->bind(':force_password_change', $forcePasswordChange);
        $this->db->bind(':password_expiry_days', $passwordExpiryDays);
        
        if ($this->db->execute()) {
            $userId = $this->db->lastInsertId();
            $this->addPasswordHistory($userId, $hashedPassword);
            return $userId;
        }
        return false;
    }

    /**
     * Update user details (Role, Status, Force PW, PW Expiry)
     */
    public function updateUser($id, $roleId, $status, $forcePasswordChange = 0, $passwordExpiryDays = 90) {
        $this->db->query("UPDATE users SET role_id = :role_id, status = :status, force_password_change = :force_password_change, password_expiry_days = :password_expiry_days WHERE id = :id");
        $this->db->bind(':role_id', $roleId);
        $this->db->bind(':status', $status);
        $this->db->bind(':force_password_change', $forcePasswordChange);
        $this->db->bind(':password_expiry_days', $passwordExpiryDays);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Reset user password
     */
    public function resetPassword($id, $newPassword, $forcePasswordChange = 0) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->db->query("UPDATE users SET password = :password, last_password_change = NOW(), force_password_change = :force_password_change, failed_attempts = 0 WHERE id = :id");
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':force_password_change', $forcePasswordChange);
        $this->db->bind(':id', $id);
        
        if ($this->db->execute()) {
            $this->addPasswordHistory($id, $hashedPassword);
            return true;
        }
        return false;
    }

    /**
     * Add password hash to history
     */
    public function addPasswordHistory($userId, $passwordHash) {
        $this->db->query("INSERT INTO password_history (user_id, password_hash) VALUES (:user_id, :password_hash)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':password_hash', $passwordHash);
        return $this->db->execute();
    }

    /**
     * Retrieve password history for a user
     */
    public function getPasswordHistory($userId) {
        $this->db->query("SELECT * FROM password_history WHERE user_id = :user_id ORDER BY changed_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    /**
     * Increment failed attempts
     */
    public function incrementFailedAttempts($username) {
        $this->db->query("UPDATE users SET failed_attempts = failed_attempts + 1 WHERE username = :username");
        $this->db->bind(':username', $username);
        $this->db->execute();

        $user = $this->findUserByUsername($username);
        if ($user && $user->failed_attempts >= 5) {
            $this->lockAccount($username);
            return true; // Locked
        }
        return false;
    }

    /**
     * Reset failed attempts count
     */
    public function resetFailedAttempts($username) {
        $this->db->query("UPDATE users SET failed_attempts = 0 WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->execute();
    }

    /**
     * Get all users with role information
     */
    public function getAllUsers() {
        $this->db->query("SELECT u.id, u.username, u.status, u.last_login, u.created_at, u.last_password_change, u.failed_attempts, u.force_password_change, u.password_expiry_days, r.name as role_name, r.id as role_id 
                          FROM users u 
                          JOIN roles r ON u.role_id = r.id 
                          ORDER BY u.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Get all active instructor-role users (role_id = 2)
     * Used to fan out notice notifications.
     */
    public function getAllInstructorUsers() {
        $this->db->query("SELECT id FROM users WHERE role_id = 2 AND status = 'active'");
        return $this->db->resultSet();
    }

    /**
     * Search users by username, role, or status
     */
    public function searchUsers($query, $roleId = null) {
        $sql = "SELECT u.id, u.username, u.status, u.last_login, u.created_at, u.last_password_change, u.failed_attempts, u.force_password_change, u.password_expiry_days, r.name as role_name, r.id as role_id 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username LIKE :query";
        
        if ($roleId) {
            $sql .= " AND u.role_id = :role_id";
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':query', '%' . $query . '%');
        if ($roleId) {
            $this->db->bind(':role_id', $roleId);
        }
        
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

