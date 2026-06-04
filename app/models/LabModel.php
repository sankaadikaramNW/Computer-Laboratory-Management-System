<?php
/**
 * Laboratory Model Class
 * Manages laboratory room details, capacities, locations, and status.
 */
class LabModel extends Model {
    /**
     * Get all laboratories
     */
    public function getAllLabs() {
        $this->db->query("SELECT * FROM laboratories ORDER BY lab_code ASC");
        return $this->db->resultSet();
    }

    /**
     * Get active laboratories
     */
    public function getActiveLabs() {
        $this->db->query("SELECT * FROM laboratories WHERE status = 'active' ORDER BY lab_code ASC");
        return $this->db->resultSet();
    }

    /**
     * Get lab details by ID
     */
    public function getLabById($id) {
        $this->db->query("SELECT * FROM laboratories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check if lab code exists (for duplicates validation)
     */
    public function checkLabCodeExists($labCode, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM laboratories WHERE lab_code = :lab_code AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM laboratories WHERE lab_code = :lab_code");
        }
        $this->db->bind(':lab_code', $labCode);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Create a laboratory
     */
    public function createLab($data) {
        $this->db->query("INSERT INTO laboratories (lab_code, lab_name, location, capacity, description, status) 
                          VALUES (:lab_code, :lab_name, :location, :capacity, :description, :status)");
        
        $this->db->bind(':lab_code', $data['lab_code']);
        $this->db->bind(':lab_name', $data['lab_name']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':capacity', $data['capacity']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }

    /**
     * Update laboratory details
     */
    public function updateLab($id, $data) {
        $this->db->query("UPDATE laboratories 
                          SET lab_code = :lab_code, 
                              lab_name = :lab_name, 
                              location = :location, 
                              capacity = :capacity, 
                              description = :description, 
                              status = :status 
                          WHERE id = :id");
        
        $this->db->bind(':lab_code', $data['lab_code']);
        $this->db->bind(':lab_name', $data['lab_name']);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':capacity', $data['capacity']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Delete/Remove a laboratory
     */
    public function deleteLab($id) {
        $this->db->query("DELETE FROM laboratories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get lab utilization statistics (total sessions, active time)
     */
    public function getLabUtilizationStats() {
        $this->db->query("SELECT l.lab_name, l.lab_code, COUNT(a.id) as total_sessions, 
                                 IFNULL(SUM(TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time)) / 60, 0) as total_hours
                          FROM laboratories l 
                          LEFT JOIN allocations a ON l.id = a.lab_id 
                          GROUP BY l.id");
        return $this->db->resultSet();
    }
}
