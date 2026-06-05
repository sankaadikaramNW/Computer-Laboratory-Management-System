<?php
/**
 * Inquiry Controller
 * Routes all operational inquiry/dashboard pages.
 * URL: /inquiry/<action>
 */
class InquiryController extends Controller {
    private $inquiryModel;

    public function __construct() {
        requireLogin();
        requireAdmin();
        $this->inquiryModel = $this->model('InquiryModel');
    }

    // ─── Shared filter helper ─────────────────────────────────────────────────
    private function filters() {
        return [
            'service_no' => trim($_GET['service_no'] ?? ''),
            'name'       => trim($_GET['name']       ?? ''),
            'rank'       => trim($_GET['rank']        ?? ''),
            'trade'      => trim($_GET['trade']       ?? ''),
            'lab_id'     => (int)($_GET['lab_id']    ?? 0) ?: '',
            'lesson_id'  => (int)($_GET['lesson_id'] ?? 0) ?: '',
            'status'     => trim($_GET['status']      ?? ''),
            'date_from'  => trim($_GET['date_from']   ?? ''),
            'date_to'    => trim($_GET['date_to']     ?? ''),
            'month'      => trim($_GET['month']        ?? ''),
            'year'       => trim($_GET['year']         ?? ''),
            'asset_no'   => trim($_GET['asset_no']    ?? ''),
            'type'       => trim($_GET['type']        ?? ''),
        ];
    }

    private function refs() {
        return [
            'labs'   => $this->inquiryModel->getAllLabs(),
            'lessons'=> $this->inquiryModel->getAllLessons(),
            'trades' => $this->inquiryModel->getAllTrades(),
            'ranks'  => $this->inquiryModel->getAllRanks(),
        ];
    }

    // ─── 1. Instructor Activity Inquiry ───────────────────────────────────────
    public function instructorActivity() {
        $f = $this->filters();
        $sessions = $this->inquiryModel->getInstructorActivity($f);
        $summary  = $this->inquiryModel->getInstructorActivitySummary($f);

        // Aggregate workload totals
        $totals = ['sessions' => 0, 'hours' => 0, 'instructors' => count($summary)];
        foreach ($summary as $r) {
            $totals['sessions'] += $r->total_sessions;
            $totals['hours']    += $r->total_hours;
        }

        $data = array_merge($this->refs(), [
            'title'       => 'Instructor Activity Inquiry',
            'active_menu' => 'inq_instructor',
            'filters'     => $f,
            'sessions'    => $sessions,
            'summary'     => $summary,
            'totals'      => $totals,
        ]);

        if ($this->isAjax()) { $this->json(['sessions' => $sessions, 'summary' => $summary, 'totals' => $totals]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/instructor_activity', $data);
        $this->view('templates/footer');
    }

    // ─── 2. Laboratory Session Inquiry ────────────────────────────────────────
    public function labSessions() {
        $f       = $this->filters();
        $sessions = $this->inquiryModel->getLabSessions($f);
        $stats    = $this->inquiryModel->getLabSessionStats($f);

        $data = array_merge($this->refs(), [
            'title'       => 'Laboratory Session Inquiry',
            'active_menu' => 'inq_lab_sessions',
            'filters'     => $f,
            'sessions'    => $sessions,
            'stats'       => $stats,
        ]);

        if ($this->isAjax()) { $this->json(['sessions' => $sessions, 'stats' => $stats]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/lab_sessions', $data);
        $this->view('templates/footer');
    }

    // ─── 3. Lecture Hours Analysis ────────────────────────────────────────────
    public function lectureHours() {
        $f    = $this->filters();
        $rows = $this->inquiryModel->getLectureHoursAnalysis($f);
        $top  = $this->inquiryModel->getTop10Instructors();
        $trend= $this->inquiryModel->getMonthlyHoursTrend();

        $totalHours = array_reduce($rows, fn($c,$r) => $c + $r->total_hours, 0);
        $totalSessions = array_reduce($rows, fn($c,$r) => $c + $r->session_count, 0);

        $data = array_merge($this->refs(), [
            'title'          => 'Lecture Hours Analysis',
            'active_menu'    => 'inq_lecture',
            'filters'        => $f,
            'rows'           => $rows,
            'top10'          => $top,
            'trend'          => $trend,
            'total_hours'    => round($totalHours, 2),
            'total_sessions' => $totalSessions,
        ]);

        if ($this->isAjax()) { $this->json(['rows' => $rows, 'total_hours' => $totalHours, 'total_sessions' => $totalSessions]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/lecture_hours', $data);
        $this->view('templates/footer');
    }

    // ─── 4. Laboratory Utilization ────────────────────────────────────────────
    public function labUtilization() {
        $f    = $this->filters();
        $rows = $this->inquiryModel->getLabUtilization($f);

        // For chart: daily usage of the selected/first lab
        $labId    = !empty($f['lab_id']) ? $f['lab_id'] : ($rows[0]->id ?? 0);
        $dateFrom = !empty($f['date_from']) ? $f['date_from'] : date('Y-m-01');
        $dateTo   = !empty($f['date_to'])   ? $f['date_to']   : date('Y-m-t');
        $daily    = $labId ? $this->inquiryModel->getDailyLabUsage($labId, $dateFrom, $dateTo) : [];

        // Compute working hours in range (8h/day Mon-Fri)
        $workingDays = 0;
        if ($dateFrom && $dateTo) {
            $d = new DateTime($dateFrom);
            $end = new DateTime($dateTo);
            while ($d <= $end) {
                $dow = (int)$d->format('N');
                if ($dow <= 5) $workingDays++;
                $d->modify('+1 day');
            }
        }
        $availableHours = $workingDays * 8;

        $data = array_merge($this->refs(), [
            'title'           => 'Laboratory Utilization',
            'active_menu'     => 'inq_lab_util',
            'filters'         => $f,
            'rows'            => $rows,
            'daily'           => $daily,
            'available_hours' => $availableHours,
        ]);

        if ($this->isAjax()) { $this->json(['rows' => $rows, 'daily' => $daily]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/lab_utilization', $data);
        $this->view('templates/footer');
    }

    // ─── 5. Session History ───────────────────────────────────────────────────
    public function sessionHistory() {
        $f        = $this->filters();
        $sessions = $this->inquiryModel->getSessionHistory($f);

        $counts = ['Completed' => 0, 'Scheduled' => 0, 'Today' => 0];
        $totalHours = 0;
        foreach ($sessions as $s) {
            $counts[$s->status] = ($counts[$s->status] ?? 0) + 1;
            $totalHours += $s->hours;
        }

        $data = array_merge($this->refs(), [
            'title'       => 'Session History',
            'active_menu' => 'inq_session_hist',
            'filters'     => $f,
            'sessions'    => $sessions,
            'counts'      => $counts,
            'total_hours' => round($totalHours, 2),
        ]);

        if ($this->isAjax()) { $this->json(['sessions' => $sessions, 'counts' => $counts]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/session_history', $data);
        $this->view('templates/footer');
    }

    // ─── 6. Equipment Usage ───────────────────────────────────────────────────
    public function equipmentUsage() {
        $f    = $this->filters();
        $rows = $this->inquiryModel->getEquipmentUsage($f);

        $data = array_merge($this->refs(), [
            'title'       => 'Equipment Usage Inquiry',
            'active_menu' => 'inq_equipment',
            'filters'     => $f,
            'rows'        => $rows,
        ]);

        if ($this->isAjax()) { $this->json(['rows' => $rows]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/equipment_usage', $data);
        $this->view('templates/footer');
    }

    // ─── 7. Session Completion Records (Admin View) ──────────────────────────
    public function sessionCompletionRecords() {
        $f = $this->filters();
        $records = $this->inquiryModel->getSessionCompletionRecords($f);

        $counts = ['Completed Successfully' => 0, 'Partially Completed' => 0, 'Cancelled' => 0, 'Total' => 0];
        foreach ($records as $r) {
            $status = $r->session_status;
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
            $counts['Total']++;
        }

        $data = array_merge($this->refs(), [
            'title'       => 'Session Completion Records',
            'active_menu' => 'session_completion_records',
            'filters'     => $f,
            'records'     => $records,
            'counts'      => $counts
        ]);

        if ($this->isAjax()) { $this->json(['records' => $records, 'counts' => $counts]); return; }

        $this->view('templates/header', $data);
        $this->view('inquiry/session_completion_records', $data);
        $this->view('templates/footer');
    }

    // ─── Global Search (AJAX only) ────────────────────────────────────────────
    public function globalSearch() {
        $term = trim($_GET['q'] ?? '');
        if (strlen($term) < 2) { $this->json([]); return; }
        $this->json($this->inquiryModel->globalSearch($term));
    }

    // ─── Helper ───────────────────────────────────────────────────────────────
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // ─── Get Maintenance History (AJAX) ───────────────────────────────────────
    public function maintenanceHistory($type, $id) {
        $history = $this->inquiryModel->getMaintenanceHistory($type, (int)$id);
        $this->json($history);
    }
}
