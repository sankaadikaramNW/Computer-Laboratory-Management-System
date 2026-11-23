<?php
/**
 * Roster Controller Class
 * Provides public guest views for published class timetables, forecast calendars,
 * and schedule confirmation verification without requiring user authentication.
 */
class RosterController extends Controller {
    private $allocModel;
    private $labModel;
    private $campModel;

    public function __construct() {
        // Publicly accessible - No requireLogin()!
        $this->allocModel = $this->model('AllocationModel');
        $this->labModel = $this->model('LabModel');
        $this->campModel = $this->model('CampModel');
    }

    /**
     * Default Guest View: Timetable Forecast Calendar & Schedule Confirmation
     */
    public function index() {
        $camps = $this->campModel->getActiveCamps();
        $labs = $this->labModel->getActiveLabs();

        // Get initial upcoming roster (next 7 days from today)
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $upcomingRoster = $this->allocModel->searchPublicRoster('', '', null, null);

        $data = [
            'title' => 'Official Class Timetable & Schedule Confirmation',
            'camps' => $camps,
            'labs' => $labs,
            'initial_roster' => $upcomingRoster,
            'today' => $today
        ];

        $this->view('roster/index', $data);
    }

    /**
     * API: Get Calendar Events for Public FullCalendar
     */
    public function getCalendarEvents() {
        $start = filter_input(INPUT_GET, 'start', FILTER_DEFAULT);
        $end = filter_input(INPUT_GET, 'end', FILTER_DEFAULT);

        if (!$start || !$end) {
            $this->json(['error' => 'Missing date range.'], 400);
            return;
        }

        $startDate = date('Y-m-d', strtotime($start));
        $endDate = date('Y-m-d', strtotime($end));
        $campId = filter_input(INPUT_GET, 'camp_id', FILTER_VALIDATE_INT) ?: null;
        $labId = filter_input(INPUT_GET, 'lab_id', FILTER_VALIDATE_INT) ?: null;

        $allocations = $this->allocModel->getAllocationsByDateRange($startDate, $endDate, $campId);

        $events = [];
        $colors = [
            '1' => '#1d3557', // Navy blue
            '2' => '#2b6cb0', // Steel blue
            '3' => '#0d9488', // Teal
            '4' => '#475569', // Slate
            '5' => '#b91c1c', // Crimson
            '6' => '#d97706'  // Amber
        ];

        foreach ($allocations as $a) {
            // Filter by lab if requested
            if ($labId && (int)$a->lab_id !== (int)$labId) {
                continue;
            }

            // Exclude cancelled sessions from public forecast unless specified
            if ($a->session_status === 'Cancelled') {
                continue;
            }

            $batchDisplay = !empty($a->batch) ? $a->batch : (!empty($a->remarks) ? $a->remarks : 'General Batch');

            $color = $colors[$a->lab_id] ?? '#1d3557';
            if ($a->session_status === 'Completed' || $a->session_status === 'Completed Successfully') {
                $color = '#059669'; // Green
            }

            $events[] = [
                'id' => $a->id,
                'title' => "{$batchDisplay} - {$a->lesson_code} ({$a->instructor_rank} {$a->instructor_name})",
                'start' => "{$a->date}T{$a->start_time}",
                'end' => "{$a->date}T{$a->end_time}",
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'allocationId' => $a->id,
                    'batch' => $batchDisplay,
                    'labCode' => $a->lab_code,
                    'labName' => $a->lab_name,
                    'campName' => $a->camp_name ?: 'Main Station',
                    'instructorRank' => $a->instructor_rank,
                    'instructorName' => $a->instructor_name,
                    'instructorServiceNo' => $a->instructor_service_no ?? '',
                    'lessonCode' => $a->lesson_code,
                    'lessonName' => $a->lesson_name,
                    'sessionStatus' => $a->session_status,
                    'dateFormatted' => date('l, d M Y', strtotime($a->date)),
                    'timeFormatted' => date('H:i', strtotime($a->start_time)) . ' - ' . date('H:i', strtotime($a->end_time))
                ]
            ];
        }

        $this->json($events);
    }

    /**
     * API: Live Schedule Confirmation & Roster Search
     */
    public function search() {
        $q = trim(filter_input(INPUT_GET, 'q', FILTER_DEFAULT) ?? '');
        $date = trim(filter_input(INPUT_GET, 'date', FILTER_DEFAULT) ?? '');
        $campId = filter_input(INPUT_GET, 'camp_id', FILTER_VALIDATE_INT) ?: null;
        $labId = filter_input(INPUT_GET, 'lab_id', FILTER_VALIDATE_INT) ?: null;

        $results = $this->allocModel->searchPublicRoster($q, $date ?: null, $campId, $labId);

        // Sanitize & format for JSON output
        $formatted = [];
        foreach ($results as $r) {
            $formatted[] = [
                'id' => $r->id,
                'ref_no' => sprintf('AL-%05d', $r->id),
                'batch' => !empty($r->batch) ? $r->batch : (!empty($r->remarks) ? $r->remarks : 'General Batch'),
                'lesson_code' => $r->lesson_code,
                'lesson_name' => $r->lesson_name,
                'lesson_trade' => $r->lesson_trade ?? 'General',
                'lesson_duration' => $r->lesson_duration ?? 60,
                'instructor_name' => $r->instructor_name,
                'instructor_rank' => $r->instructor_rank,
                'instructor_service_no' => $r->instructor_service_no ?? '',
                'instructor_trade' => $r->instructor_trade ?? '',
                'lab_code' => $r->lab_code,
                'lab_name' => $r->lab_name,
                'camp_name' => $r->camp_name ?: 'Main Station',
                'date' => $r->date,
                'date_formatted' => date('l, d M Y', strtotime($r->date)),
                'start_time' => date('H:i', strtotime($r->start_time)),
                'end_time' => date('H:i', strtotime($r->end_time)),
                'session_status' => $r->session_status,
                'remarks' => $r->remarks ?: ''
            ];
        }

        $this->json([
            'count' => count($formatted),
            'results' => $formatted
        ]);
    }

    /**
     * API: Get single schedule confirmation detail by ID
     */
    public function detail($id = 0) {
        $id = (int)$id;
        if (!$id) {
            $this->json(['error' => 'Invalid allocation ID.'], 400);
            return;
        }

        $alloc = $this->allocModel->getPublicAllocationById($id);
        if (!$alloc) {
            $this->json(['error' => 'Roster allocation not found.'], 404);
            return;
        }

        $this->json([
            'id' => $alloc->id,
            'ref_no' => sprintf('AL-%05d', $alloc->id),
            'batch' => !empty($alloc->batch) ? $alloc->batch : (!empty($alloc->remarks) ? $alloc->remarks : 'General Batch'),
            'lesson_code' => $alloc->lesson_code,
            'lesson_name' => $alloc->lesson_name,
            'lesson_trade' => $alloc->lesson_trade ?? '',
            'lesson_duration' => $alloc->lesson_duration ?? '',
            'instructor_name' => $alloc->instructor_name,
            'instructor_rank' => $alloc->instructor_rank,
            'instructor_service_no' => $alloc->instructor_service_no ?? '',
            'instructor_trade' => $alloc->instructor_trade ?? '',
            'lab_code' => $alloc->lab_code,
            'lab_name' => $alloc->lab_name,
            'lab_location' => $alloc->lab_location ?? '',
            'lab_capacity' => $alloc->lab_capacity ?? '',
            'camp_name' => $alloc->camp_name ?: 'Main Station',
            'date' => $alloc->date,
            'date_formatted' => date('l, d M Y', strtotime($alloc->date)),
            'start_time' => date('H:i', strtotime($alloc->start_time)),
            'end_time' => date('H:i', strtotime($alloc->end_time)),
            'session_status' => $alloc->session_status,
            'verified_at' => date('Y-m-d H:i:s')
        ]);
    }
}
