<?php
/**
 * Notification Model Class
 * Inserts alerts for users and updates read states.
 */
class NotificationModel extends Model {
    /**
     * Create a notification entry
     */
    public function createNotification($userId, $message, $type, $relatedId = null) {
        $this->db->query("INSERT INTO notifications (user_id, message, type, related_id, is_read) 
                          VALUES (:user_id, :message, :type, :related_id, 0)");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':message', $message);
        $this->db->bind(':type', $type);
        $this->db->bind(':related_id', $relatedId ?: null);
        
        return $this->db->execute();
    }

    /**
     * Fetch user notifications (latest first)
     */
    public function getNotificationsByUser($userId, $limit = 50) {
        $this->db->query("SELECT * FROM notifications 
                          WHERE user_id = :user_id 
                          ORDER BY created_at DESC 
                          LIMIT :limit");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    /**
     * Get count of unread notifications
     */
    public function getUnreadCount($userId) {
        $this->db->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $this->db->bind(':user_id', $userId);
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id, $userId) {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId) {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }

    /**
     * Check if a NOTICE notification was already sent to a specific user for a given notice ID.
     * Prevents duplicate fanout when a notice is updated.
     */
    public function noticeAlreadyNotified($noticeId, $userId) {
        $this->db->query("SELECT id FROM notifications 
                          WHERE user_id = :user_id 
                            AND type = 'NOTICE' 
                            AND related_id = :notice_id 
                          LIMIT 1");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':notice_id', $noticeId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}
