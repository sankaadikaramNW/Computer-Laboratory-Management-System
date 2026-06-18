<?php
/**
 * Allocation Model Class
 * Handles scheduling, lab/instructor conflict checking, and allocations.
 */
class AllocationModel extends Model {
    /**
     * Get all allocations
     */
    public function getAllAllocations($campId = null) {
        $sql = "SELECT a.*, l.lab_name, l.lab_code, c.name as camp_name, i.full_name as instructor_name, i.rank as instructor_rank, les.lesson_name, les.lesson_code 
                FROM allocations a 
                JOIN laboratories l ON a.lab_id = l.id 
                LEFT JOIN camps c ON a.camp_id = c.id
                JOIN instructors i ON a.instructor_id = i.id 
                JOIN lessons les ON a.lesson_id = les.id";
        if ($campId) {
            $sql .= " WHERE a.camp_id = :camp_id";
        }
        $sql .= " ORDER BY a.date DESC, a.start_time ASC";
        
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        return $this->db->resultSet();
    }

    /**
     * Get allocations inside a date range (for FullCalendar)
     */
    public function getAllocationsByDateRange($start, $end, $campId = null) {
        $sql = "SELECT a.*, l.lab_name, l.lab_code, c.name as camp_name, i.full_name as instructor_name, i.rank as instructor_rank, les.lesson_name, les.lesson_code 
                FROM allocations a 
                JOIN laboratories l ON a.lab_id = l.id 
                LEFT JOIN camps c ON a.camp_id = c.id
                JOIN instructors i ON a.instructor_id = i.id 
                JOIN lessons les ON a.lesson_id = les.id 
                WHERE a.date BETWEEN :start AND :end";
        if ($campId) {
            $sql .= " AND a.camp_id = :camp_id";
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
     * Get allocation by ID
     */
    public function getAllocationById($id) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, c.name as camp_name, i.full_name as instructor_name, i.rank as instructor_rank, i.trade as instructor_trade, i.user_id as instructor_user_id, les.lesson_name, les.lesson_code, les.duration as lesson_duration
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          LEFT JOIN camps c ON a.camp_id = c.id
                          JOIN instructors i ON a.instructor_id = i.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check for scheduling conflicts (Overlaps)
     * Returns an array of conflict message strings if conflicts are found.
     */
    public function checkConflicts($date, $startTime, $endTime, $labId, $instructorId, $excludeId = 0) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, i.full_name as instructor_name, i.rank as instructor_rank, les.lesson_name 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN instructors i ON a.instructor_id = i.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.date = :date 
                            AND a.id != :exclude_id 
                            AND (a.lab_id = :lab_id OR a.instructor_id = :instructor_id) 
                            AND (a.start_time < :end_time AND a.end_time > :start_time)");
        
        $this->db->bind(':date', $date);
        $this->db->bind(':exclude_id', $excludeId);
        $this->db->bind(':lab_id', $labId);
        $this->db->bind(':instructor_id', $instructorId);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        $conflicts = $this->db->resultSet();
        $warnings = [];

        foreach ($conflicts as $c) {
            if ((int)$c->lab_id === (int)$labId) {
                $warnings[] = "Laboratory ({$c->lab_code} - {$c->lab_name}) is already booked by {$c->instructor_rank} {$c->instructor_name} for '{$c->lesson_name}' from {$c->start_time} to {$c->end_time}.";
            }
            if ((int)$c->instructor_id === (int)$instructorId) {
                $warnings[] = "Instructor ({$c->instructor_rank} {$c->instructor_name}) is already scheduled in laboratory '{$c->lab_code}' for '{$c->lesson_name}' from {$c->start_time} to {$c->end_time}.";
            }
        }

        return $warnings;
    }

    /**
     * Create an allocation
     */
    public function createAllocation($data) {
        $this->db->query("INSERT INTO allocations (instructor_id, lesson_id, lab_id, date, start_time, end_time, remarks, camp_id) 
                          VALUES (:instructor_id, :lesson_id, :lab_id, :date, :start_time, :end_time, :remarks, :camp_id)");
        
        $this->db->bind(':instructor_id', $data['instructor_id']);
        $this->db->bind(':lesson_id', $data['lesson_id']);
        $this->db->bind(':lab_id', $data['lab_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':remarks', $data['remarks'] ?: null);
        $this->db->bind(':camp_id', $data['camp_id']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update an allocation
     */
    public function updateAllocation($id, $data) {
        $this->db->query("UPDATE allocations 
                          SET instructor_id = :instructor_id, 
                              lesson_id = :lesson_id, 
                              lab_id = :lab_id, 
                              date = :date, 
                              start_time = :start_time, 
                              end_time = :end_time, 
                              remarks = :remarks,
                              camp_id = :camp_id 
                          WHERE id = :id");
        
        $this->db->bind(':instructor_id', $data['instructor_id']);
        $this->db->bind(':lesson_id', $data['lesson_id']);
        $this->db->bind(':lab_id', $data['lab_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':remarks', $data['remarks'] ?: null);
        $this->db->bind(':camp_id', $data['camp_id']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Cancel / Delete an allocation
     */
    public function deleteAllocation($id) {
        $this->db->query("DELETE FROM allocations WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Duplicate an existing allocation to a new date
     */
    public function duplicateAllocation($id, $newDate) {
        $alloc = $this->getAllocationById($id);
        if (!$alloc) return false;

        $data = [
            'instructor_id' => $alloc->instructor_id,
            'lesson_id' => $alloc->lesson_id,
            'lab_id' => $alloc->lab_id,
            'date' => $newDate,
            'start_time' => $alloc->start_time,
            'end_time' => $alloc->end_time,
            'remarks' => $alloc->remarks
        ];

        // Ensure no conflicts on new date
        $conflicts = $this->checkConflicts($newDate, $alloc->start_time, $alloc->end_time, $alloc->lab_id, $alloc->instructor_id);
        if (!empty($conflicts)) {
            return false;
        }

        return $this->createAllocation($data);
    }

    /**
     * Get allocations for a specific instructor
     */
    public function getAllocationsByInstructor($instructorId) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                          ORDER BY a.date DESC, a.start_time ASC");
        $this->db->bind(':instructor_id', $instructorId);
        return $this->db->resultSet();
    }

    /**
     * Get Instructor workload stats for reporting
     */
    public function getInstructorWorkloadStats() {
        $this->db->query("SELECT i.id, i.service_no, i.rank, i.full_name, i.trade, 
                                 COUNT(a.id) as session_count, 
                                 IFNULL(SUM(TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time)) / 60, 0) as total_hours 
                          FROM instructors i 
                          LEFT JOIN allocations a ON i.id = a.instructor_id 
                          GROUP BY i.id 
                          ORDER BY total_hours DESC");
        return $this->db->resultSet();
    }

    /**
     * Get total active allocations count for today
     */
    public function getActiveSessionsToday($campId = null) {
        $sql = "SELECT COUNT(*) as count FROM allocations WHERE date = CURDATE()";
        if ($campId) {
            $sql .= " AND camp_id = :camp_id";
        }
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    /**
     * Get upcoming allocations limit
     */
    public function getUpcomingSessions($limit = 5, $campId = null) {
        $sql = "SELECT a.*, l.lab_name, l.lab_code, i.full_name as instructor_name, i.rank as instructor_rank, les.lesson_name 
                FROM allocations a 
                JOIN laboratories l ON a.lab_id = l.id 
                JOIN instructors i ON a.instructor_id = i.id 
                JOIN lessons les ON a.lesson_id = les.id 
                WHERE a.date >= CURDATE()";
        if ($campId) {
            $sql .= " AND a.camp_id = :camp_id";
        }
        $sql .= " ORDER BY a.date ASC, a.start_time ASC 
                  LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        return $this->db->resultSet();
    }

    /**
     * Get instructor dashboard stats (total, this week, completed)
     */
    public function getInstructorStats($instructorId) {
        $this->db->query("SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN a.date >= CURDATE() - INTERVAL (WEEKDAY(CURDATE())) DAY 
                      AND a.date <= CURDATE() - INTERVAL (WEEKDAY(CURDATE())) DAY + INTERVAL 6 DAY 
                 THEN 1 ELSE 0 END) as this_week,
            SUM(CASE WHEN a.date < CURDATE() THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN a.date >= CURDATE() THEN 1 ELSE 0 END) as upcoming
            FROM allocations a WHERE a.instructor_id = :instructor_id");
        $this->db->bind(':instructor_id', $instructorId);
        return $this->db->single();
    }

    /**
     * Get allocations for a specific instructor within a week range
     */
    public function getWeekAllocations($instructorId, $weekStart, $weekEnd) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                            AND a.date BETWEEN :week_start AND :week_end
                          ORDER BY a.date ASC, a.start_time ASC");
        $this->db->bind(':instructor_id', $instructorId);
        $this->db->bind(':week_start', $weekStart);
        $this->db->bind(':week_end', $weekEnd);
        return $this->db->resultSet();
    }

    /**
     * Get past (completed) allocations for instructor with optional filters
     */
    public function getPastAllocations($instructorId, $month = '', $year = '', $labId = '', $lessonId = '') {
        $sql = "SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                FROM allocations a 
                JOIN laboratories l ON a.lab_id = l.id 
                JOIN lessons les ON a.lesson_id = les.id 
                WHERE a.instructor_id = :instructor_id AND a.date < CURDATE()";

        if (!empty($month)) {
            $sql .= " AND MONTH(a.date) = :month";
        }
        if (!empty($year)) {
            $sql .= " AND YEAR(a.date) = :year";
        }
        if (!empty($labId)) {
            $sql .= " AND a.lab_id = :lab_id";
        }
        if (!empty($lessonId)) {
            $sql .= " AND a.lesson_id = :lesson_id";
        }

        $sql .= " ORDER BY a.date DESC, a.start_time DESC";

        $this->db->query($sql);
        $this->db->bind(':instructor_id', $instructorId);

        if (!empty($month)) {
            $this->db->bind(':month', $month);
        }
        if (!empty($year)) {
            $this->db->bind(':year', $year);
        }
        if (!empty($labId)) {
            $this->db->bind(':lab_id', $labId);
        }
        if (!empty($lessonId)) {
            $this->db->bind(':lesson_id', $lessonId);
        }

        return $this->db->resultSet();
    }

    /**
     * Get filtered future/all allocations for instructor (for My Allocations page)
     */
    public function getFilteredAllocations($instructorId, $dateFrom = '', $dateTo = '', $labId = '', $lessonId = '', $search = '') {
        $sql = "SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                FROM allocations a 
                JOIN laboratories l ON a.lab_id = l.id 
                JOIN lessons les ON a.lesson_id = les.id 
                WHERE a.instructor_id = :instructor_id";

        if (!empty($dateFrom)) {
            $sql .= " AND a.date >= :date_from";
        }
        if (!empty($dateTo)) {
            $sql .= " AND a.date <= :date_to";
        }
        if (!empty($labId)) {
            $sql .= " AND a.lab_id = :lab_id";
        }
        if (!empty($lessonId)) {
            $sql .= " AND a.lesson_id = :lesson_id";
        }
        if (!empty($search)) {
            $sql .= " AND (les.lesson_name LIKE :search OR les.lesson_code LIKE :search OR l.lab_code LIKE :search)";
        }

        $sql .= " ORDER BY a.date DESC, a.start_time ASC";

        $this->db->query($sql);
        $this->db->bind(':instructor_id', $instructorId);

        if (!empty($dateFrom)) {
            $this->db->bind(':date_from', $dateFrom);
        }
        if (!empty($dateTo)) {
            $this->db->bind(':date_to', $dateTo);
        }
        if (!empty($labId)) {
            $this->db->bind(':lab_id', $labId);
        }
        if (!empty($lessonId)) {
            $this->db->bind(':lesson_id', $lessonId);
        }
        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }

        return $this->db->resultSet();
    }

    /**
     * Get calendar events scoped to a single instructor
     */
    public function getInstructorCalendarEvents($instructorId, $start, $end) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                            AND a.date BETWEEN :start AND :end
                          ORDER BY a.date ASC, a.start_time ASC");
        $this->db->bind(':instructor_id', $instructorId);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        return $this->db->resultSet();
    }

    /**
     * Get sessions pending completion for instructor (date is today or in the past, status is Scheduled/In Progress)
     */
    public function getPendingCompletionSessions($instructorId) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                            AND (a.date < CURDATE() OR (a.date = CURDATE() AND a.start_time <= CURTIME())) 
                            AND a.session_status IN ('Scheduled', 'In Progress')
                          ORDER BY a.date DESC, a.start_time DESC");
        $this->db->bind(':instructor_id', $instructorId);
        return $this->db->resultSet();
    }

    /**
     * Get recently completed sessions for instructor
     */
    public function getRecentlyCompletedSessions($instructorId, $limit = 5) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                            AND a.session_status IN ('Completed Successfully', 'Partially Completed') 
                          ORDER BY a.completed_at DESC 
                          LIMIT :limit");
        $this->db->bind(':instructor_id', $instructorId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get upcoming sessions for instructor
     */
    public function getUpcomingSessionsForInstructor($instructorId, $limit = 5) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                            AND (a.date > CURDATE() OR (a.date = CURDATE() AND a.start_time > CURTIME())) 
                            AND a.session_status = 'Scheduled'
                          ORDER BY a.date ASC, a.start_time ASC 
                          LIMIT :limit");
        $this->db->bind(':instructor_id', $instructorId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get instructor's completed / cancelled / rescheduled session history
     */
    public function getInstructorSessionHistory($instructorId) {
        $this->db->query("SELECT a.*, l.lab_name, l.lab_code, les.lesson_name, les.lesson_code 
                          FROM allocations a 
                          JOIN laboratories l ON a.lab_id = l.id 
                          JOIN lessons les ON a.lesson_id = les.id 
                          WHERE a.instructor_id = :instructor_id 
                            AND a.session_status IN ('Completed Successfully', 'Partially Completed', 'Cancelled', 'Rescheduled')
                          ORDER BY a.date DESC, a.start_time DESC");
        $this->db->bind(':instructor_id', $instructorId);
        return $this->db->resultSet();
    }

    /**
     * Save session completion details
     */
    public function completeSession($id, $status, $remarks, $userId) {
        $this->db->query("UPDATE allocations 
                          SET session_status = :status, 
                              instructor_remarks = :remarks, 
                              completed_at = NOW(), 
                              completed_by = :completed_by 
                          WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':remarks', $remarks ?: null);
        $this->db->bind(':completed_by', $userId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get administrator dashboard stats today
     */
    public function getAdminDashboardStats($campId = null) {
        $stats = [];
        
        // Scheduled Sessions Today
        $sql = "SELECT COUNT(*) as count FROM allocations WHERE date = CURDATE() AND session_status = 'Scheduled'";
        if ($campId) {
            $sql .= " AND camp_id = :camp_id";
        }
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        $row = $this->db->single();
        $stats['scheduled_today'] = $row ? (int)$row->count : 0;
        
        // Completed Sessions Today
        $sql = "SELECT COUNT(*) as count FROM allocations WHERE date = CURDATE() AND session_status IN ('Completed Successfully', 'Partially Completed', 'Completed')";
        if ($campId) {
            $sql .= " AND camp_id = :camp_id";
        }
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        $row = $this->db->single();
        $stats['completed_today'] = $row ? (int)$row->count : 0;
        
        // Cancelled Sessions Today or Total
        $sql = "SELECT COUNT(*) as count FROM allocations WHERE session_status = 'Cancelled'";
        if ($campId) {
            $sql .= " AND camp_id = :camp_id";
        }
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        $row = $this->db->single();
        $stats['cancelled_total'] = $row ? (int)$row->count : 0;
        
        // Completion Percentage Today
        $sql = "SELECT COUNT(*) as count FROM allocations WHERE date = CURDATE() AND session_status = 'Cancelled'";
        if ($campId) {
            $sql .= " AND camp_id = :camp_id";
        }
        $this->db->query($sql);
        if ($campId) {
            $this->db->bind(':camp_id', $campId);
        }
        $row = $this->db->single();
        $cancelled_today = $row ? (int)$row->count : 0;
        
        $total_today = $stats['scheduled_today'] + $stats['completed_today'] + $cancelled_today;
        if ($total_today > 0) {
            $stats['completion_percentage'] = round(($stats['completed_today'] / $total_today) * 100);
        } else {
            $stats['completion_percentage'] = 100; // default to 100 if no sessions scheduled
        }
        
        return $stats;
    }
}
