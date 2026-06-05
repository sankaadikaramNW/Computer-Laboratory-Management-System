<?php
/**
 * Notice Model Class
 * Manages notice board announcements posted by administrators.
 */
class NoticeModel extends Model {
    /**
     * Get all notices (latest first)
     */
    public function getAllNotices() {
        $this->db->query("SELECT n.*, u.username as publisher_name 
                          FROM notices n 
                          JOIN users u ON n.published_by = u.id 
                          ORDER BY n.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Get active notices for dashboard viewing
     */
    public function getActiveNotices() {
        $this->db->query("SELECT n.*, u.username as publisher_name 
                          FROM notices n 
                          JOIN users u ON n.published_by = u.id 
                          WHERE n.status = 'active' 
                          ORDER BY n.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Get specific notice by ID
     */
    public function getNoticeById($id) {
        $this->db->query("SELECT * FROM notices WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Create a notice and return its new ID (or false on failure)
     */
    public function createNotice($data) {
        $this->db->query("INSERT INTO notices (title, content, published_by, status) 
                          VALUES (:title, :content, :published_by, :status)");
        
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':published_by', $data['published_by']);
        $this->db->bind(':status', $data['status']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId(); // Returns new notice ID
        }
        return false;
    }

    /**
     * Check if a notification for this notice has already been sent to a user
     * (prevents duplicate notifications when notice is updated/re-published)
     */
    public function noticeAlreadyNotified($noticeId, $userId) {
        // We check the notifications table via raw PDO through a second model call
        // This is done externally in NotificationModel
        return false; // Placeholder — actual check is in NotificationModel
    }

    /**
     * Update a notice
     */
    public function updateNotice($id, $data) {
        $this->db->query("UPDATE notices 
                          SET title = :title, 
                              content = :content, 
                              status = :status 
                          WHERE id = :id");
        
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Delete a notice
     */
    public function deleteNotice($id) {
        $this->db->query("DELETE FROM notices WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
