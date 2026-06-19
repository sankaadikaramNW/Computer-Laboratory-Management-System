<?php
/**
 * Maintenance Model Class
 * Handles preventative maintenance records and scheduling repairs for hardware assets.
 */
class MaintenanceModel extends Model {
    /**
     * Get all maintenance logs
     */
    public function getAllRecords($campId = null) {
        $sql = "SELECT m.*, 
                       c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                       s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model,
                       COALESCE(lc.camp_id, ls.camp_id) as camp_id
                FROM maintenance_records m
                LEFT JOIN computers c ON m.equipment_type = 'computer' AND m.equipment_id = c.id
                LEFT JOIN smart_boards s ON m.equipment_type = 'smart_board' AND m.equipment_id = s.id
                LEFT JOIN laboratories lc ON c.lab_id = lc.id
                LEFT JOIN laboratories ls ON s.lab_id = ls.id";
        
        if ($campId) {
            $sql .= " WHERE COALESCE(lc.camp_id, ls.camp_id) = :camp_id";
        }
        
        $sql .= " ORDER BY m.repair_date DESC, m.created_at DESC";
        
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        return $this->db->resultSet();
    }

    /**
     * Get record by ID
     */
    public function getRecordById($id) {
        $this->db->query("SELECT m.*, 
                                 c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                                 s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model,
                                 COALESCE(lc.camp_id, ls.camp_id) as camp_id
                          FROM maintenance_records m
                          LEFT JOIN computers c ON m.equipment_type = 'computer' AND m.equipment_id = c.id
                          LEFT JOIN smart_boards s ON m.equipment_type = 'smart_board' AND m.equipment_id = s.id
                          LEFT JOIN laboratories lc ON c.lab_id = lc.id
                          LEFT JOIN laboratories ls ON s.lab_id = ls.id
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
    public function getPendingMaintenanceCount($campId = null) {
        if ($campId) {
            $this->db->query("SELECT COUNT(*) as count FROM maintenance_records m
                              LEFT JOIN computers c ON m.equipment_type = 'computer' AND m.equipment_id = c.id
                              LEFT JOIN smart_boards s ON m.equipment_type = 'smart_board' AND m.equipment_id = s.id
                              LEFT JOIN laboratories l ON (c.lab_id = l.id OR s.lab_id = l.id)
                              WHERE m.status IN ('scheduled', 'in_progress') AND l.camp_id = :camp_id");
            $this->db->bind(':camp_id', $campId);
        } else {
            $this->db->query("SELECT COUNT(*) as count FROM maintenance_records WHERE status IN ('scheduled', 'in_progress')");
        }
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Get maintenance records in a date range, filtered by camp
     */
    public function getMaintenanceByDateRange($start, $end, $campId = null) {
        $sql = "SELECT m.*, 
                       c.asset_no as computer_asset_no, c.brand as computer_brand, c.model as computer_model,
                       s.asset_id as smartboard_asset_id, s.brand as smartboard_brand, s.model as smartboard_model,
                       cl.camp_id as computer_camp_id, sl.camp_id as smartboard_camp_id
                FROM maintenance_records m
                LEFT JOIN computers c ON m.equipment_type = 'computer' AND m.equipment_id = c.id
                LEFT JOIN laboratories cl ON c.lab_id = cl.id
                LEFT JOIN smart_boards s ON m.equipment_type = 'smart_board' AND m.equipment_id = s.id
                LEFT JOIN laboratories sl ON s.lab_id = sl.id
                WHERE m.repair_date BETWEEN :start AND :end";
        if ($campId) {
            $sql .= " AND (cl.camp_id = :camp_id OR sl.camp_id = :camp_id)";
        }
        $this->db->query($sql);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        return $this->db->resultSet();
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
