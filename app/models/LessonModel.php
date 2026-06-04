<?php
/**
 * Lesson Model Class
 * Handles course lessons, codes, and syllabus durations.
 */
class LessonModel extends Model {
    /**
     * Get all lessons
     */
    public function getAllLessons() {
        $this->db->query("SELECT * FROM lessons ORDER BY lesson_code ASC");
        return $this->db->resultSet();
    }

    /**
     * Get lesson by ID
     */
    public function getLessonById($id) {
        $this->db->query("SELECT * FROM lessons WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check if lesson code exists
     */
    public function checkLessonCodeExists($lessonCode, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM lessons WHERE lesson_code = :lesson_code AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM lessons WHERE lesson_code = :lesson_code");
        }
        $this->db->bind(':lesson_code', $lessonCode);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Create lesson record
     */
    public function createLesson($data) {
        $this->db->query("INSERT INTO lessons (lesson_code, lesson_name, trade, duration, description) 
                          VALUES (:lesson_code, :lesson_name, :trade, :duration, :description)");
        
        $this->db->bind(':lesson_code', $data['lesson_code']);
        $this->db->bind(':lesson_name', $data['lesson_name']);
        $this->db->bind(':trade', $data['trade']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':description', $data['description']);
        
        return $this->db->execute();
    }

    /**
     * Update lesson details
     */
    public function updateLesson($id, $data) {
        $this->db->query("UPDATE lessons 
                          SET lesson_code = :lesson_code, 
                              lesson_name = :lesson_name, 
                              trade = :trade, 
                              duration = :duration, 
                              description = :description 
                          WHERE id = :id");
        
        $this->db->bind(':lesson_code', $data['lesson_code']);
        $this->db->bind(':lesson_name', $data['lesson_name']);
        $this->db->bind(':trade', $data['trade']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Delete a lesson record
     */
    public function deleteLesson($id) {
        $this->db->query("DELETE FROM lessons WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
