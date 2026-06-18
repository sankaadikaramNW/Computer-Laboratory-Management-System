<?php
/**
 * Computer Model Class
 * Handles computer inventory items, specs, and lab allocations.
 */
class ComputerModel extends Model {
    /**
     * Get all computers with assigned laboratory code
     */
    public function getAllComputers($campId = null) {
        if ($campId) {
            $this->db->query("SELECT c.*, l.lab_name, l.lab_code 
                              FROM computers c 
                              LEFT JOIN laboratories l ON c.lab_id = l.id 
                              WHERE l.camp_id = :camp_id
                              ORDER BY c.asset_no ASC");
            $this->db->bind(':camp_id', $campId);
        } else {
            $this->db->query("SELECT c.*, l.lab_name, l.lab_code 
                              FROM computers c 
                              LEFT JOIN laboratories l ON c.lab_id = l.id 
                              ORDER BY c.asset_no ASC");
        }
        return $this->db->resultSet();
    }

    /**
     * Get computer by ID
     */
    public function getComputerById($id) {
        $this->db->query("SELECT c.*, l.lab_name, l.lab_code 
                          FROM computers c 
                          LEFT JOIN laboratories l ON c.lab_id = l.id 
                          WHERE c.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check if asset number exists
     */
    public function checkAssetNoExists($assetNo, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM computers WHERE asset_no = :asset_no AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM computers WHERE asset_no = :asset_no");
        }
        $this->db->bind(':asset_no', $assetNo);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Create a computer record
     */
    public function createComputer($data) {
        $this->db->query("INSERT INTO computers (asset_no, serial_no, brand, model, processor, ram, storage, os, purchase_date, warranty_status, lab_id, status) 
                          VALUES (:asset_no, :serial_no, :brand, :model, :processor, :ram, :storage, :os, :purchase_date, :warranty_status, :lab_id, :status)");
        
        $this->db->bind(':asset_no', $data['asset_no']);
        $this->db->bind(':serial_no', $data['serial_no']);
        $this->db->bind(':brand', $data['brand']);
        $this->db->bind(':model', $data['model']);
        $this->db->bind(':processor', $data['processor']);
        $this->db->bind(':ram', $data['ram']);
        $this->db->bind(':storage', $data['storage']);
        $this->db->bind(':os', $data['os']);
        $this->db->bind(':purchase_date', $data['purchase_date'] ?: null);
        $this->db->bind(':warranty_status', $data['warranty_status']);
        $this->db->bind(':lab_id', $data['lab_id'] ?: null);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }

    /**
     * Update computer specifications or assignment
     */
    public function updateComputer($id, $data) {
        $this->db->query("UPDATE computers 
                          SET asset_no = :asset_no, 
                              serial_no = :serial_no, 
                              brand = :brand, 
                              model = :model, 
                              processor = :processor, 
                              ram = :ram, 
                              storage = :storage, 
                              os = :os, 
                              purchase_date = :purchase_date, 
                              warranty_status = :warranty_status, 
                              lab_id = :lab_id, 
                              status = :status 
                          WHERE id = :id");
        
        $this->db->bind(':asset_no', $data['asset_no']);
        $this->db->bind(':serial_no', $data['serial_no']);
        $this->db->bind(':brand', $data['brand']);
        $this->db->bind(':model', $data['model']);
        $this->db->bind(':processor', $data['processor']);
        $this->db->bind(':ram', $data['ram']);
        $this->db->bind(':storage', $data['storage']);
        $this->db->bind(':os', $data['os']);
        $this->db->bind(':purchase_date', $data['purchase_date'] ?: null);
        $this->db->bind(':warranty_status', $data['warranty_status']);
        $this->db->bind(':lab_id', $data['lab_id'] ?: null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Transfer or assign computer to a laboratory
     */
    public function assignToLab($id, $labId) {
        $this->db->query("UPDATE computers SET lab_id = :lab_id WHERE id = :id");
        $this->db->bind(':lab_id', $labId ?: null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Update computer hardware status
     */
    public function updateStatus($id, $status) {
        $this->db->query("UPDATE computers SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete computer record
     */
    public function deleteComputer($id) {
        $this->db->query("DELETE FROM computers WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get computers assigned to a specific laboratory
     */
    public function getComputersByLab($labId) {
        $this->db->query("SELECT * FROM computers WHERE lab_id = :lab_id ORDER BY asset_no ASC");
        $this->db->bind(':lab_id', $labId);
        return $this->db->resultSet();
    }

    /**
     * Get summary status counts
     */
    public function getStatusCounts() {
        $this->db->query("SELECT status, COUNT(*) as count FROM computers GROUP BY status");
        return $this->db->resultSet();
    }
}
