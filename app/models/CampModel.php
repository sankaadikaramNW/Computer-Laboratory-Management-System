<?php
/**
 * Camp Model Class
 * Handles SLAF base/station locations database actions.
 */
class CampModel extends Model {
    /**
     * Get all camps
     */
    public function getAllCamps() {
        $this->db->query("SELECT * FROM camps ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get active camps only
     */
    public function getActiveCamps() {
        $this->db->query("SELECT * FROM camps WHERE status = 'active' ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get camp by ID
     */
    public function getCampById($id) {
        $this->db->query("SELECT * FROM camps WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check if camp code already exists
     */
    public function checkCampCodeExists($code, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM camps WHERE code = :code AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM camps WHERE code = :code");
        }
        $this->db->bind(':code', $code);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if camp name already exists
     */
    public function checkCampNameExists($name, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM camps WHERE name = :name AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM camps WHERE name = :name");
        }
        $this->db->bind(':name', $name);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Create camp record
     */
    public function createCamp($data) {
        $this->db->query("INSERT INTO camps (name, code, address, status) VALUES (:name, :code, :address, :status)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        return $this->db->execute();
    }

    /**
     * Update camp details
     */
    public function updateCamp($id, $data) {
        $this->db->query("UPDATE camps SET name = :name, code = :code, address = :address, status = :status WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Toggle camp status
     */
    public function updateCampStatus($id, $status) {
        $this->db->query("UPDATE camps SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
