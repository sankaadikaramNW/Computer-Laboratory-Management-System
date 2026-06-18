<?php
/**
 * Smart Board Model Class
 * Handles smart board digital devices, installation dates, and lab allocations.
 */
class SmartBoardModel extends Model {
    /**
     * Get all smart boards
     */
    public function getAllSmartBoards($campId = null) {
        if ($campId) {
            $this->db->query("SELECT s.*, l.lab_name, l.lab_code 
                              FROM smart_boards s 
                              LEFT JOIN laboratories l ON s.lab_id = l.id 
                              WHERE l.camp_id = :camp_id
                              ORDER BY s.asset_id ASC");
            $this->db->bind(':camp_id', $campId);
        } else {
            $this->db->query("SELECT s.*, l.lab_name, l.lab_code 
                              FROM smart_boards s 
                              LEFT JOIN laboratories l ON s.lab_id = l.id 
                              ORDER BY s.asset_id ASC");
        }
        return $this->db->resultSet();
    }

    /**
     * Get smart board by ID
     */
    public function getSmartBoardById($id) {
        $this->db->query("SELECT s.*, l.lab_name, l.lab_code 
                          FROM smart_boards s 
                          LEFT JOIN laboratories l ON s.lab_id = l.id 
                          WHERE s.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check if asset ID exists
     */
    public function checkAssetIdExists($assetId, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM smart_boards WHERE asset_id = :asset_id AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM smart_boards WHERE asset_id = :asset_id");
        }
        $this->db->bind(':asset_id', $assetId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Create smart board record
     */
    public function createSmartBoard($data) {
        $this->db->query("INSERT INTO smart_boards (asset_id, brand, model, installation_date, lab_id, status) 
                          VALUES (:asset_id, :brand, :model, :installation_date, :lab_id, :status)");
        
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':brand', $data['brand']);
        $this->db->bind(':model', $data['model']);
        $this->db->bind(':installation_date', $data['installation_date'] ?: null);
        $this->db->bind(':lab_id', $data['lab_id'] ?: null);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }

    /**
     * Update smart board details
     */
    public function updateSmartBoard($id, $data) {
        $this->db->query("UPDATE smart_boards 
                          SET asset_id = :asset_id, 
                              brand = :brand, 
                              model = :model, 
                              installation_date = :installation_date, 
                              lab_id = :lab_id, 
                              status = :status 
                          WHERE id = :id");
        
        $this->db->bind(':asset_id', $data['asset_id']);
        $this->db->bind(':brand', $data['brand']);
        $this->db->bind(':model', $data['model']);
        $this->db->bind(':installation_date', $data['installation_date'] ?: null);
        $this->db->bind(':lab_id', $data['lab_id'] ?: null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Transfer smart board to a lab
     */
    public function assignToLab($id, $labId) {
        $this->db->query("UPDATE smart_boards SET lab_id = :lab_id WHERE id = :id");
        $this->db->bind(':lab_id', $labId ?: null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Update status
     */
    public function updateStatus($id, $status) {
        $this->db->query("UPDATE smart_boards SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete smart board record
     */
    public function deleteSmartBoard($id) {
        $this->db->query("DELETE FROM smart_boards WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
