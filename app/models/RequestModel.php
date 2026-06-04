<?php
/**
 * Request Model Class
 * Handles rescheduling, cancellation, and lab transfer requests from instructors.
 */
class RequestModel extends Model {
    /**
     * Get all change requests
     */
    public function getAllRequests() {
        $this->db->query("SELECT r.*, u.username as requester_name, inst.full_name as instructor_name, inst.rank as instructor_rank,
                                 a.date as old_date, a.start_time as old_start_time, a.end_time as old_end_time, 
                                 l.lab_code as old_lab_code, l.lab_name as old_lab_name,
                                 nl.lab_code as new_lab_code, nl.lab_name as new_lab_name,
                                 les.lesson_name
                          FROM allocation_requests r 
                          JOIN users u ON r.requester_id = u.id 
                          LEFT JOIN instructors inst ON u.id = inst.user_id
                          LEFT JOIN allocations a ON r.allocation_id = a.id 
                          LEFT JOIN lessons les ON a.lesson_id = les.id
                          LEFT JOIN laboratories l ON a.lab_id = l.id 
                          LEFT JOIN laboratories nl ON r.new_lab_id = nl.id 
                          ORDER BY r.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Get requests submitted by a specific instructor user ID
     */
    public function getRequestsByRequester($userId) {
        $this->db->query("SELECT r.*, 
                                 a.date as old_date, a.start_time as old_start_time, a.end_time as old_end_time, 
                                 l.lab_code as old_lab_code, l.lab_name as old_lab_name,
                                 nl.lab_code as new_lab_code, nl.lab_name as new_lab_name,
                                 les.lesson_name
                          FROM allocation_requests r 
                          LEFT JOIN allocations a ON r.allocation_id = a.id 
                          LEFT JOIN lessons les ON a.lesson_id = les.id
                          LEFT JOIN laboratories l ON a.lab_id = l.id 
                          LEFT JOIN laboratories nl ON r.new_lab_id = nl.id 
                          WHERE r.requester_id = :requester_id 
                          ORDER BY r.created_at DESC");
        $this->db->bind(':requester_id', $userId);
        return $this->db->resultSet();
    }

    /**
     * Get request details by ID
     */
    public function getRequestById($id) {
        $this->db->query("SELECT r.*, u.username as requester_name, inst.full_name as instructor_name, inst.rank as instructor_rank, inst.id as instructor_id,
                                 a.date as old_date, a.start_time as old_start_time, a.end_time as old_end_time, a.instructor_id as old_instructor_id, a.lesson_id as old_lesson_id, a.lab_id as old_lab_id,
                                 l.lab_code as old_lab_code, l.lab_name as old_lab_name,
                                 nl.lab_code as new_lab_code, nl.lab_name as new_lab_name,
                                 les.lesson_name
                          FROM allocation_requests r 
                          JOIN users u ON r.requester_id = u.id 
                          LEFT JOIN instructors inst ON u.id = inst.user_id
                          LEFT JOIN allocations a ON r.allocation_id = a.id 
                          LEFT JOIN lessons les ON a.lesson_id = les.id
                          LEFT JOIN laboratories l ON a.lab_id = l.id 
                          LEFT JOIN laboratories nl ON r.new_lab_id = nl.id 
                          WHERE r.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Create a new change request
     */
    public function createRequest($data) {
        $this->db->query("INSERT INTO allocation_requests (allocation_id, requester_id, type, new_date, new_start_time, new_end_time, new_lab_id, reason, status) 
                          VALUES (:allocation_id, :requester_id, :type, :new_date, :new_start_time, :new_end_time, :new_lab_id, :reason, 'pending')");
        
        $this->db->bind(':allocation_id', $data['allocation_id']);
        $this->db->bind(':requester_id', $data['requester_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':new_date', $data['new_date'] ?: null);
        $this->db->bind(':new_start_time', $data['new_start_time'] ?: null);
        $this->db->bind(':new_end_time', $data['new_end_time'] ?: null);
        $this->db->bind(':new_lab_id', $data['new_lab_id'] ?: null);
        $this->db->bind(':reason', $data['reason']);
        
        return $this->db->execute();
    }

    /**
     * Update request review status
     */
    public function updateRequestStatus($id, $status, $reviewerRemarks) {
        $this->db->query("UPDATE allocation_requests 
                          SET status = :status, reviewer_remarks = :reviewer_remarks 
                          WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':reviewer_remarks', $reviewerRemarks);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Get count of pending requests
     */
    public function getPendingRequestsCount() {
        $this->db->query("SELECT COUNT(*) as count FROM allocation_requests WHERE status = 'pending'");
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }
}
