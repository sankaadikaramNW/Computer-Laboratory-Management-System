<?php
/**
 * Instructor Model Class
 * Handles instructor service record profiles and their login linkage.
 */
class InstructorModel extends Model {
    /**
     * Get instructor by associated user ID
     */
    public function getInstructorByUserId($userId) {
        $this->db->query("SELECT * FROM instructors WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }

    /**
     * Get instructor by ID
     */
    public function getInstructorById($id) {
        $this->db->query("SELECT i.*, u.username, u.status as user_status 
                          FROM instructors i 
                          LEFT JOIN users u ON i.user_id = u.id 
                          WHERE i.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get all instructors
     */
    public function getAllInstructors() {
        $this->db->query("SELECT i.*, u.username 
                          FROM instructors i 
                          LEFT JOIN users u ON i.user_id = u.id 
                          ORDER BY i.rank, i.full_name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get only active instructors (for dropdowns and allocation forms)
     */
    public function getActiveInstructors() {
        $this->db->query("SELECT i.*, u.username 
                          FROM instructors i 
                          LEFT JOIN users u ON i.user_id = u.id 
                          WHERE i.status = 'active'
                          ORDER BY i.rank, i.full_name ASC");
        return $this->db->resultSet();
    }

    /**
     * Create instructor profile
     */
    public function createInstructor($data) {
        $this->db->query("INSERT INTO instructors (user_id, service_no, rank, full_name, trade, contact_no, email, profile_photo, photo_uploaded_at, status) 
                          VALUES (:user_id, :service_no, :rank, :full_name, :trade, :contact_no, :email, :profile_photo, :photo_uploaded_at, :status)");
        
        $this->db->bind(':user_id', $data['user_id'] ?: null);
        $this->db->bind(':service_no', $data['service_no']);
        $this->db->bind(':rank', $data['rank']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':trade', $data['trade']);
        $this->db->bind(':contact_no', $data['contact_no']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':profile_photo', $data['profile_photo'] ?? null);
        $this->db->bind(':photo_uploaded_at', $data['photo_uploaded_at'] ?? null);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }

    /**
     * Update instructor profile (Admin access)
     */
    public function updateInstructor($id, $data) {
        $this->db->query("UPDATE instructors 
                          SET user_id = :user_id, 
                              service_no = :service_no, 
                              rank = :rank, 
                              full_name = :full_name, 
                              trade = :trade, 
                              contact_no = :contact_no, 
                              email = :email, 
                              profile_photo = :profile_photo,
                              photo_uploaded_at = :photo_uploaded_at,
                              status = :status 
                          WHERE id = :id");
        
        $this->db->bind(':user_id', $data['user_id'] ?: null);
        $this->db->bind(':service_no', $data['service_no']);
        $this->db->bind(':rank', $data['rank']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':trade', $data['trade']);
        $this->db->bind(':contact_no', $data['contact_no']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':profile_photo', $data['profile_photo'] ?? null);
        $this->db->bind(':photo_uploaded_at', $data['photo_uploaded_at'] ?? null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Instructor self-profile update (restricted fields only)
     */
    public function updateInstructorSelf($id, $contactNo, $email, $photoPath = null, $uploadedAt = null) {
        if ($photoPath !== null) {
            $this->db->query("UPDATE instructors SET contact_no = :contact_no, email = :email, profile_photo = :profile_photo, photo_uploaded_at = :photo_uploaded_at WHERE id = :id");
            $this->db->bind(':profile_photo', $photoPath);
            $this->db->bind(':photo_uploaded_at', $uploadedAt);
        } else {
            $this->db->query("UPDATE instructors SET contact_no = :contact_no, email = :email WHERE id = :id");
        }
        $this->db->bind(':contact_no', $contactNo);
        $this->db->bind(':email', $email);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Search and Filter Instructors
     */
    public function searchInstructors($term = '', $rank = '', $trade = '') {
        $sql = "SELECT i.*, u.username FROM instructors i LEFT JOIN users u ON i.user_id = u.id WHERE 1=1";
        
        if (!empty($term)) {
            $sql .= " AND (i.service_no LIKE :term 
                        OR i.full_name LIKE :term 
                        OR i.rank LIKE :term 
                        OR i.trade LIKE :term 
                        OR u.username LIKE :term 
                        OR i.email LIKE :term)";
        }
        if (!empty($rank)) {
            $sql .= " AND i.rank = :rank";
        }
        if (!empty($trade)) {
            $sql .= " AND i.trade LIKE :trade";
        }
        
        $sql .= " ORDER BY i.rank, i.full_name ASC";
        
        $this->db->query($sql);
        
        if (!empty($term)) {
            $this->db->bind(':term', '%' . $term . '%');
        }
        if (!empty($rank)) {
            $this->db->bind(':rank', $rank);
        }
        if (!empty($trade)) {
            $this->db->bind(':trade', '%' . $trade . '%');
        }
        
        return $this->db->resultSet();
    }

    /**
     * Check if a service number already exists (for create duplicate check)
     */
    public function serviceNoExists($serviceNo) {
        $this->db->query("SELECT id FROM instructors WHERE service_no = :service_no");
        $this->db->bind(':service_no', $serviceNo);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if a service number is used by another instructor (for update duplicate check)
     */
    public function serviceNoExistsExcluding($serviceNo, $excludeId) {
        $this->db->query("SELECT id FROM instructors WHERE service_no = :service_no AND id != :id");
        $this->db->bind(':service_no', $serviceNo);
        $this->db->bind(':id', $excludeId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if a user_id is already linked to a DIFFERENT instructor (for update duplicate check)
     */
    public function userIdExistsExcluding($userId, $excludeId) {
        if (!$userId) return false; // NULL is allowed (unlinked)
        $this->db->query("SELECT id FROM instructors WHERE user_id = :user_id AND id != :id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':id', $excludeId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Delete an instructor record
     */
    public function deleteInstructor($id) {
        $this->db->query("DELETE FROM instructors WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
