<?php
/**
 * Fault Model Class
 * Handles reporting, updates, and resolution logs of system faults (computer, smart board, network, other).
 */
class FaultModel extends Model {
    /**
     * Get all fault reports
     */
    public function getAllFaults() {
        $this->db->query("SELECT f.*, u.username as reported_by_name, inst.full_name as instructor_name, inst.rank as instructor_rank,
                                 c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                                 s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model
                          FROM fault_reports f
                          JOIN users u ON f.reported_by = u.id
                          LEFT JOIN instructors inst ON u.id = inst.user_id
                          LEFT JOIN computers c ON f.equipment_type = 'computer' AND f.equipment_id = c.id
                          LEFT JOIN smart_boards s ON f.equipment_type = 'smart_board' AND f.equipment_id = s.id
                          ORDER BY f.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Get fault reports by specific reporter user ID
     */
    public function getFaultsByReporter($userId) {
        $this->db->query("SELECT f.*, 
                                 c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                                 s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model
                          FROM fault_reports f
                          LEFT JOIN computers c ON f.equipment_type = 'computer' AND f.equipment_id = c.id
                          LEFT JOIN smart_boards s ON f.equipment_type = 'smart_board' AND f.equipment_id = s.id
                          WHERE f.reported_by = :reported_by
                          ORDER BY f.created_at DESC");
        $this->db->bind(':reported_by', $userId);
        return $this->db->resultSet();
    }

    /**
     * Get fault details by ID
     */
    public function getFaultById($id) {
        $this->db->query("SELECT f.*, u.username as reported_by_name, inst.full_name as instructor_name, inst.rank as instructor_rank,
                                 c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                                 s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model
                          FROM fault_reports f
                          JOIN users u ON f.reported_by = u.id
                          LEFT JOIN instructors inst ON u.id = inst.user_id
                          LEFT JOIN computers c ON f.equipment_type = 'computer' AND f.equipment_id = c.id
                          LEFT JOIN smart_boards s ON f.equipment_type = 'smart_board' AND f.equipment_id = s.id
                          WHERE f.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Report a new fault ticket
     */
    public function createFault($data) {
        $this->db->query("INSERT INTO fault_reports (reported_by, equipment_type, equipment_id, description, status) 
                          VALUES (:reported_by, :equipment_type, :equipment_id, :description, 'reported')");
        
        $this->db->bind(':reported_by', $data['reported_by']);
        $this->db->bind(':equipment_type', $data['equipment_type']);
        $this->db->bind(':equipment_id', $data['equipment_id'] ?: null);
        $this->db->bind(':description', $data['description']);
        
        return $this->db->execute();
    }

    /**
     * Update status and resolution notes of a fault ticket
     */
    public function updateStatus($id, $status, $resolutionNotes = '') {
        $this->db->query("UPDATE fault_reports 
                          SET status = :status, resolution_notes = :resolution_notes 
                          WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':resolution_notes', $resolutionNotes);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get count of pending (reported/in-progress) faults
     */
    public function getPendingFaultsCount($campId = null) {
        if ($campId) {
            $this->db->query("SELECT COUNT(*) as count FROM fault_reports f
                              LEFT JOIN computers c ON f.equipment_type = 'computer' AND f.equipment_id = c.id
                              LEFT JOIN smart_boards s ON f.equipment_type = 'smart_board' AND f.equipment_id = s.id
                              LEFT JOIN laboratories l ON (c.lab_id = l.id OR s.lab_id = l.id)
                              WHERE f.status IN ('reported', 'in_progress') AND l.camp_id = :camp_id");
            $this->db->bind(':camp_id', $campId);
        } else {
            $this->db->query("SELECT COUNT(*) as count FROM fault_reports WHERE status IN ('reported', 'in_progress')");
        }
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }
}
