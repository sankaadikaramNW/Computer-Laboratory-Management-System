<?php
/**
 * Maintenance Model Class
 * Handles preventative maintenance records and scheduling repairs for hardware assets.
 */
class MaintenanceModel extends Model {
    /**
     * Get all maintenance logs
     */
    public function getAllRecords() {
        $this->db->query("SELECT m.*, 
                                 c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                                 s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model
                          FROM maintenance_records m
                          LEFT JOIN computers c ON m.equipment_type = 'computer' AND m.equipment_id = c.id
                          LEFT JOIN smart_boards s ON m.equipment_type = 'smart_board' AND m.equipment_id = s.id
                          ORDER BY m.repair_date DESC, m.created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Get record by ID
     */
    public function getRecordById($id) {
        $this->db->query("SELECT m.*, 
                                 c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                                 s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model
                          FROM maintenance_records m
                          LEFT JOIN computers c ON m.equipment_type = 'computer' AND m.equipment_id = c.id
                          LEFT JOIN smart_boards s ON m.equipment_type = 'smart_board' AND m.equipment_id = s.id
                          WHERE m.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Create a maintenance record
     */
    public function createRecord($data) {
        $this->db->query("INSERT INTO maintenance_records (equipment_type, equipment_id, issue_type, assigned_technician, repair_date, status, notes) 
                          VALUES (:equipment_type, :equipment_id, :issue_type, :assigned_technician, :repair_date, :status, :notes)");
        
        $this->db->bind(':equipment_type', $data['equipment_type']);
        $this->db->bind(':equipment_id', $data['equipment_id'] ?: null);
        $this->db->bind(':issue_type', $data['issue_type']);
        $this->db->bind(':assigned_technician', $data['assigned_technician']);
        $this->db->bind(':repair_date', $data['repair_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        
        return $this->db->execute();
    }

    /**
     * Update a maintenance record
     */
    public function updateRecord($id, $data) {
        $this->db->query("UPDATE maintenance_records 
                          SET equipment_type = :equipment_type, 
                              equipment_id = :equipment_id, 
                              issue_type = :issue_type, 
                              assigned_technician = :assigned_technician, 
                              repair_date = :repair_date, 
                              status = :status, 
                              notes = :notes 
                          WHERE id = :id");
        
        $this->db->bind(':equipment_type', $data['equipment_type']);
        $this->db->bind(':equipment_id', $data['equipment_id'] ?: null);
        $this->db->bind(':issue_type', $data['issue_type']);
        $this->db->bind(':assigned_technician', $data['assigned_technician']);
        $this->db->bind(':repair_date', $data['repair_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Get count of active maintenance items
     */
    public function getPendingMaintenanceCount() {
        $this->db->query("SELECT COUNT(*) as count FROM maintenance_records WHERE status IN ('scheduled', 'in_progress')");
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Delete maintenance record
     */
    public function deleteRecord($id) {
        $this->db->query("DELETE FROM maintenance_records WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
