<?php
/**
 * Lesson Controller Class
 * Handles CRUD operations for curriculum syllabus lessons.
 */
class LessonController extends Controller {
    private $lessonModel;

    public function __construct() {
        requireLogin();
        $this->lessonModel = $this->model('LessonModel');
    }

    /**
     * Display Lessons CRUD Registry
     */
    public function index() {
        requireAdmin();

        $lessons = $this->lessonModel->getAllLessons();
        
        $data = [
            'title' => 'Syllabus Lessons',
            'active_menu' => 'lessons',
            'lessons' => $lessons
        ];

        $this->view('templates/header', $data);
        $this->view('lessons/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Create Lesson
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('lesson');
            }

            $lessonCode = strtoupper(trim($_POST['lesson_code']));
            $lessonName = trim($_POST['lesson_name']);
            $trade = trim($_POST['trade']);
            $duration = (int)$_POST['duration'];
            $description = trim($_POST['description']);

            // Validate code duplicate
            if ($this->lessonModel->checkLessonCodeExists($lessonCode)) {
                flash('dashboard_error', "Lesson Code '{$lessonCode}' already exists.", 'alert alert-danger');
                redirect('lesson');
            }

            $data = [
                'lesson_code' => $lessonCode,
                'lesson_name' => $lessonName,
                'trade' => $trade,
                'duration' => $duration,
                'description' => $description
            ];

            if ($this->lessonModel->createLesson($data)) {
                $this->logActivity('CREATE_LESSON', 'LESSONS', "Created new syllabus lesson: {$lessonCode} - {$lessonName}");
                flash('dashboard_success', 'Syllabus lesson registered successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to register syllabus lesson.', 'alert alert-danger');
            }
            redirect('lesson');
        }
    }

    /**
     * Update Lesson Details
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('lesson');
            }

            $lessonCode = strtoupper(trim($_POST['lesson_code']));
            $lessonName = trim($_POST['lesson_name']);
            $trade = trim($_POST['trade']);
            $duration = (int)$_POST['duration'];
            $description = trim($_POST['description']);

            // Validate duplicate excluding current lesson
            if ($this->lessonModel->checkLessonCodeExists($lessonCode, $id)) {
                flash('dashboard_error', "Another lesson is using code '{$lessonCode}'.", 'alert alert-danger');
                redirect('lesson');
            }

            $data = [
                'lesson_code' => $lessonCode,
                'lesson_name' => $lessonName,
                'trade' => $trade,
                'duration' => $duration,
                'description' => $description
            ];

            if ($this->lessonModel->updateLesson($id, $data)) {
                $this->logActivity('UPDATE_LESSON', 'LESSONS', "Updated syllabus lesson: {$lessonCode} (ID: {$id})");
                flash('dashboard_success', 'Syllabus lesson details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update syllabus lesson.', 'alert alert-danger');
            }
            redirect('lesson');
        }
    }

    /**
     * Delete Lesson
     */
    public function delete($id) {
        requireAdmin();

        $lesson = $this->lessonModel->getLessonById($id);
        if ($lesson) {
            if ($this->lessonModel->deleteLesson($id)) {
                $this->logActivity('DELETE_LESSON', 'LESSONS', "Deleted syllabus lesson '{$lesson->lesson_code}'");
                flash('dashboard_success', 'Syllabus lesson deleted successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to delete syllabus lesson (check if referenced by schedules).', 'alert alert-danger');
            }
        }
        redirect('lesson');
    }
}
