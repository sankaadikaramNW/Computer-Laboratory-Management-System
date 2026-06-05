<?php
/**
 * Inquiry Model Class
 * Powers all Advanced Filtering & Operational Inquiry dashboards.
 */
class InquiryModel extends Model {

    // ─── SHARED HELPERS ───────────────────────────────────────────────────────

    private function buildDateWhere(&$sql, &$params, $dateFrom, $dateTo, $col = 'a.date') {
        if (!empty($dateFrom)) { $sql .= " AND {$col} >= :date_from"; $params[':date_from'] = $dateFrom; }
        if (!empty($dateTo))   { $sql .= " AND {$col} <= :date_to";   $params[':date_to']   = $dateTo;   }
    }

    // ─── 1. INSTRUCTOR ACTIVITY INQUIRY ───────────────────────────────────────

    public function getInstructorActivity($filters = []) {
        $sql = "SELECT a.id, a.date, a.start_time, a.end_time, a.remarks,
                       TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time) / 60.0 as hours,
                       i.id as instructor_id, i.service_no, i.rank, i.full_name, i.trade,
                       l.lab_code, l.lab_name,
                       les.lesson_code, les.lesson_name
                FROM allocations a
                JOIN instructors i  ON a.instructor_id = i.id
                JOIN laboratories l ON a.lab_id = l.id
                JOIN lessons les    ON a.lesson_id = les.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['service_no'])) { $sql .= " AND i.service_no LIKE :service_no"; $params[':service_no'] = '%'.$filters['service_no'].'%'; }
        if (!empty($filters['name']))        { $sql .= " AND i.full_name  LIKE :name";       $params[':name']       = '%'.$filters['name'].'%'; }
        if (!empty($filters['rank']))        { $sql .= " AND i.rank = :rank";                $params[':rank']       = $filters['rank']; }
        if (!empty($filters['trade']))       { $sql .= " AND i.trade LIKE :trade";           $params[':trade']      = '%'.$filters['trade'].'%'; }
        if (!empty($filters['lab_id']))      { $sql .= " AND a.lab_id = :lab_id";            $params[':lab_id']     = $filters['lab_id']; }
        if (!empty($filters['lesson_id']))   { $sql .= " AND a.lesson_id = :lesson_id";      $params[':lesson_id']  = $filters['lesson_id']; }
        if (!empty($filters['month']))       { $sql .= " AND MONTH(a.date) = :month";        $params[':month']      = $filters['month']; }
        if (!empty($filters['year']))        { $sql .= " AND YEAR(a.date) = :year";          $params[':year']       = $filters['year']; }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $sql .= " ORDER BY a.date DESC, a.start_time ASC";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    public function getInstructorActivitySummary($filters = []) {
        $sql = "SELECT i.id, i.service_no, i.rank, i.full_name, i.trade,
                       COUNT(a.id) as total_sessions,
                       ROUND(IFNULL(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,0),2) as total_hours,
                       SUM(CASE WHEN a.date < CURDATE() THEN 1 ELSE 0 END) as completed,
                       SUM(CASE WHEN a.date >= CURDATE() THEN 1 ELSE 0 END) as upcoming
                FROM instructors i
                LEFT JOIN allocations a ON i.id = a.instructor_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['service_no'])) { $sql .= " AND i.service_no LIKE :service_no"; $params[':service_no'] = '%'.$filters['service_no'].'%'; }
        if (!empty($filters['name']))        { $sql .= " AND i.full_name  LIKE :name";       $params[':name']       = '%'.$filters['name'].'%'; }
        if (!empty($filters['rank']))        { $sql .= " AND i.rank = :rank";                $params[':rank']       = $filters['rank']; }
        if (!empty($filters['trade']))       { $sql .= " AND i.trade LIKE :trade";           $params[':trade']      = '%'.$filters['trade'].'%'; }
        if (!empty($filters['month']))       { $sql .= " AND MONTH(a.date) = :month";        $params[':month']      = $filters['month']; }
        if (!empty($filters['year']))        { $sql .= " AND YEAR(a.date) = :year";          $params[':year']       = $filters['year']; }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $sql .= " GROUP BY i.id ORDER BY total_hours DESC";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    // ─── 2. LABORATORY SESSION INQUIRY ────────────────────────────────────────

    public function getLabSessions($filters = []) {
        $sql = "SELECT a.id, a.date, a.start_time, a.end_time, a.remarks,
                       TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time) as duration_min,
                       ROUND(TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time)/60.0, 2) as hours,
                       i.rank as instructor_rank, i.full_name as instructor_name, i.service_no, i.trade,
                       l.lab_code, l.lab_name,
                       les.lesson_code, les.lesson_name
                FROM allocations a
                JOIN instructors i  ON a.instructor_id = i.id
                JOIN laboratories l ON a.lab_id = l.id
                JOIN lessons les    ON a.lesson_id = les.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['lab_id']))    { $sql .= " AND a.lab_id = :lab_id";         $params[':lab_id']    = $filters['lab_id']; }
        if (!empty($filters['trade']))     { $sql .= " AND i.trade LIKE :trade";         $params[':trade']     = '%'.$filters['trade'].'%'; }
        if (!empty($filters['lesson_id'])) { $sql .= " AND a.lesson_id = :lesson_id";   $params[':lesson_id'] = $filters['lesson_id']; }
        if (!empty($filters['name']))      { $sql .= " AND i.full_name LIKE :name";      $params[':name']      = '%'.$filters['name'].'%'; }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $sql .= " ORDER BY a.date DESC, a.start_time ASC";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    public function getLabSessionStats($filters = []) {
        $sql = "SELECT COUNT(a.id) as total_sessions,
                       ROUND(IFNULL(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,0),2) as total_hours,
                       COUNT(DISTINCT a.lab_id) as labs_used,
                       COUNT(DISTINCT a.instructor_id) as instructors
                FROM allocations a
                JOIN instructors i ON a.instructor_id = i.id
                JOIN laboratories l ON a.lab_id = l.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['lab_id']))  { $sql .= " AND a.lab_id = :lab_id";    $params[':lab_id']  = $filters['lab_id']; }
        if (!empty($filters['trade']))   { $sql .= " AND i.trade LIKE :trade";    $params[':trade']   = '%'.$filters['trade'].'%'; }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->single();
    }

    // ─── 3. LECTURE HOURS ANALYSIS ────────────────────────────────────────────

    public function getLectureHoursAnalysis($filters = []) {
        $sql = "SELECT i.id, i.service_no, i.rank, i.full_name, i.trade,
                       COUNT(a.id) as session_count,
                       ROUND(IFNULL(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,0),2) as total_hours
                FROM instructors i
                LEFT JOIN allocations a ON i.id = a.instructor_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['service_no'])) { $sql .= " AND i.service_no LIKE :service_no"; $params[':service_no'] = '%'.$filters['service_no'].'%'; }
        if (!empty($filters['name']))        { $sql .= " AND i.full_name  LIKE :name";       $params[':name']       = '%'.$filters['name'].'%'; }
        if (!empty($filters['trade']))       { $sql .= " AND i.trade LIKE :trade";           $params[':trade']      = '%'.$filters['trade'].'%'; }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $sql .= " GROUP BY i.id ORDER BY total_hours DESC";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    public function getTop10Instructors() {
        $this->db->query("SELECT i.rank, i.full_name, i.trade,
                                 COUNT(a.id) as sessions,
                                 ROUND(IFNULL(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,0),2) as total_hours
                          FROM instructors i
                          LEFT JOIN allocations a ON i.id = a.instructor_id
                          GROUP BY i.id
                          ORDER BY total_hours DESC
                          LIMIT 10");
        return $this->db->resultSet();
    }

    public function getMonthlyHoursTrend() {
        $this->db->query("SELECT YEAR(a.date) as yr, MONTH(a.date) as mo,
                                 DATE_FORMAT(a.date,'%b %Y') as label,
                                 ROUND(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,2) as hours,
                                 COUNT(a.id) as sessions
                          FROM allocations a
                          WHERE a.date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                          GROUP BY yr, mo
                          ORDER BY yr ASC, mo ASC");
        return $this->db->resultSet();
    }

    // ─── 4. LABORATORY UTILIZATION ────────────────────────────────────────────

    public function getLabUtilization($filters = []) {
        $sql = "SELECT l.id, l.lab_code, l.lab_name, l.capacity,
                       COUNT(a.id) as total_sessions,
                       ROUND(IFNULL(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,0),2) as total_hours,
                       COUNT(DISTINCT a.date) as active_days
                FROM laboratories l
                LEFT JOIN allocations a ON l.id = a.lab_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['lab_id'])) { $sql .= " AND l.id = :lab_id"; $params[':lab_id'] = $filters['lab_id']; }
        if (!empty($filters['month']))  { $sql .= " AND MONTH(a.date) = :month"; $params[':month'] = $filters['month']; }
        if (!empty($filters['year']))   { $sql .= " AND YEAR(a.date) = :year";   $params[':year']  = $filters['year']; }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $sql .= " GROUP BY l.id ORDER BY total_hours DESC";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    public function getDailyLabUsage($labId, $dateFrom, $dateTo) {
        $this->db->query("SELECT a.date,
                                 COUNT(a.id) as sessions,
                                 ROUND(SUM(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time))/60,2) as hours
                          FROM allocations a
                          WHERE a.lab_id = :lab_id
                            AND a.date BETWEEN :date_from AND :date_to
                          GROUP BY a.date
                          ORDER BY a.date ASC");
        $this->db->bind(':lab_id',    $labId);
        $this->db->bind(':date_from', $dateFrom);
        $this->db->bind(':date_to',   $dateTo);
        return $this->db->resultSet();
    }

    // ─── 5. SESSION HISTORY ───────────────────────────────────────────────────

    public function getSessionHistory($filters = []) {
        $sql = "SELECT a.id, a.date, a.start_time, a.end_time, a.remarks,
                       ROUND(TIMESTAMPDIFF(MINUTE,a.start_time,a.end_time)/60.0,2) as hours,
                       CASE WHEN a.date < CURDATE() THEN 'Completed'
                            WHEN a.date = CURDATE() THEN 'Today'
                            ELSE 'Scheduled' END as status,
                       i.service_no, i.rank, i.full_name as instructor_name, i.trade,
                       l.lab_code, l.lab_name,
                       les.lesson_code, les.lesson_name
                FROM allocations a
                JOIN instructors i  ON a.instructor_id = i.id
                JOIN laboratories l ON a.lab_id = l.id
                JOIN lessons les    ON a.lesson_id = les.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['service_no'])) { $sql .= " AND i.service_no LIKE :service_no"; $params[':service_no'] = '%'.$filters['service_no'].'%'; }
        if (!empty($filters['name']))        { $sql .= " AND i.full_name  LIKE :name";       $params[':name']       = '%'.$filters['name'].'%'; }
        if (!empty($filters['trade']))       { $sql .= " AND i.trade LIKE :trade";           $params[':trade']      = '%'.$filters['trade'].'%'; }
        if (!empty($filters['lab_id']))      { $sql .= " AND a.lab_id = :lab_id";            $params[':lab_id']     = $filters['lab_id']; }
        if (!empty($filters['lesson_id']))   { $sql .= " AND a.lesson_id = :lesson_id";      $params[':lesson_id']  = $filters['lesson_id']; }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'Completed')  { $sql .= " AND a.date < CURDATE()"; }
            elseif ($filters['status'] === 'Today')  { $sql .= " AND a.date = CURDATE()"; }
            elseif ($filters['status'] === 'Scheduled') { $sql .= " AND a.date > CURDATE()"; }
        }
        $this->buildDateWhere($sql, $params, $filters['date_from'] ?? '', $filters['date_to'] ?? '');

        $sql .= " ORDER BY a.date DESC, a.start_time ASC";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    // ─── 6. EQUIPMENT USAGE ───────────────────────────────────────────────────

    public function getEquipmentUsage($filters = []) {
        // Computers
        $sql = "SELECT c.id, c.asset_no, c.brand, c.model, c.status,
                       'Computer' as equipment_type,
                       l.lab_code, l.lab_name,
                       (SELECT COUNT(*) FROM allocations a WHERE a.lab_id = c.lab_id) as usage_sessions
                FROM computers c
                LEFT JOIN laboratories l ON c.lab_id = l.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['lab_id']))    { $sql .= " AND c.lab_id = :lab_id";         $params[':lab_id']    = $filters['lab_id']; }
        if (!empty($filters['asset_no']))  { $sql .= " AND c.asset_no LIKE :asset_no";  $params[':asset_no']  = '%'.$filters['asset_no'].'%'; }
        if (!empty($filters['type']) && $filters['type'] !== 'smartboard') {
            // only computers
        }

        $sql .= " ORDER BY usage_sessions DESC";
        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    public function getMaintenanceHistory($equipmentType = '', $equipmentId = 0) {
        $sql = "SELECT * FROM maintenance_records WHERE 1=1";
        $params = [];
        if (!empty($equipmentType)) { $sql .= " AND equipment_type = :type"; $params[':type'] = $equipmentType; }
        if ($equipmentId > 0)       { $sql .= " AND equipment_id = :eid";    $params[':eid']  = $equipmentId; }
        $sql .= " ORDER BY repair_date DESC LIMIT 20";
        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    // ─── GLOBAL SEARCH ────────────────────────────────────────────────────────

    public function globalSearch($term) {
        $like = '%' . $term . '%';
        $results = [];

        // Instructors
        $this->db->query("SELECT 'Instructor' as module, CONCAT(rank,' ',full_name) as label,
                                  service_no as sub, 'instructor' as link_base, id
                           FROM instructors WHERE service_no LIKE :t OR full_name LIKE :t2 LIMIT 5");
        $this->db->bind(':t',  $like);
        $this->db->bind(':t2', $like);
        $results['instructors'] = $this->db->resultSet();

        // Laboratories
        $this->db->query("SELECT 'Laboratory' as module, lab_name as label,
                                  lab_code as sub, 'laboratory' as link_base, id
                           FROM laboratories WHERE lab_name LIKE :t OR lab_code LIKE :t2 LIMIT 5");
        $this->db->bind(':t',  $like);
        $this->db->bind(':t2', $like);
        $results['labs'] = $this->db->resultSet();

        // Lessons
        $this->db->query("SELECT 'Lesson' as module, lesson_name as label,
                                  lesson_code as sub, 'lesson' as link_base, id
                           FROM lessons WHERE lesson_name LIKE :t OR lesson_code LIKE :t2 LIMIT 5");
        $this->db->bind(':t',  $like);
        $this->db->bind(':t2', $like);
        $results['lessons'] = $this->db->resultSet();

        // Computers (asset no)
        $this->db->query("SELECT 'Computer' as module, CONCAT(brand,' ',model) as label,
                                  asset_no as sub, 'equipment/computers' as link_base, id
                           FROM computers WHERE asset_no LIKE :t OR brand LIKE :t2 LIMIT 5");
        $this->db->bind(':t',  $like);
        $this->db->bind(':t2', $like);
        $results['computers'] = $this->db->resultSet();

        return $results;
    }

    // ─── REFERENCE DATA ───────────────────────────────────────────────────────

    public function getAllLabs() {
        $this->db->query("SELECT id, lab_code, lab_name FROM laboratories WHERE status='active' ORDER BY lab_name");
        return $this->db->resultSet();
    }

    public function getAllLessons() {
        $this->db->query("SELECT id, lesson_code, lesson_name, trade FROM lessons ORDER BY lesson_name");
        return $this->db->resultSet();
    }

    public function getAllTrades() {
        $this->db->query("SELECT DISTINCT trade FROM instructors WHERE trade IS NOT NULL AND trade != '' ORDER BY trade");
        return $this->db->resultSet();
    }

    public function getAllRanks() {
        return ['LAC','CPL','SGT','FSGT','WO','PLT OF','FG OFF','FLT LT','SQN LDR','WG CDR','GP CAPT'];
    }
}
