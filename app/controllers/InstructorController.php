<?php
/**
 * Instructor Controller Class
 * Handles registering service profiles, linking logins, status updates, and profile edits.
 */
class InstructorController extends Controller {
    private $instructorModel;
    private $userModel;

    public function __construct() {
        requireLogin();
        $this->instructorModel = $this->model('InstructorModel');
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Admin view list, search, and CRUD panel
     */
    public function index() {
        requireAdmin();

        $searchTerm = filter_input(INPUT_GET, 'search', FILTER_DEFAULT);
        $rankFilter = filter_input(INPUT_GET, 'rank', FILTER_DEFAULT);
        $tradeFilter = filter_input(INPUT_GET, 'trade', FILTER_DEFAULT);

        // Fetch instructors based on filter
        if ($searchTerm || $rankFilter || $tradeFilter) {
            $instructors = $this->instructorModel->searchInstructors($searchTerm, $rankFilter, $tradeFilter);
        } else {
            $instructors = $this->instructorModel->getAllInstructors();
        }

        // Fetch unlinked users to link them as logins (role_id = 2 and not in instructors table)
        // Or we can retrieve all users list to assign. Let's fetch all users so admin can link.
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'Instructor Management',
            'active_menu' => 'instructors',
            'instructors' => $instructors,
            'users' => $users,
            'ranks' => ['LAC', 'CPL', 'SGT', 'FSGT', 'WO', 'PLT OF', 'FG OFF', 'FLT LT', 'SQN LDR', 'WG CDR', 'GP CAPT']
        ];

        $this->view('templates/header', $data);
        $this->view('instructors/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * Create a new Instructor profile
     */
    public function create() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('instructor');
            }

            $serviceNo = trim($_POST['service_no']);
            $rank = trim($_POST['rank']);
            $fullName = trim($_POST['full_name']);
            $trade = trim($_POST['trade']);
            $contactNo = trim($_POST['contact_no']);
            $email = trim($_POST['email']);
            $userId = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
            $status = $_POST['status'] ?? 'active';

            // Check duplicate service no
            if ($this->instructorModel->serviceNoExists($serviceNo)) {
                flash('dashboard_error', "Instructor with Service Number {$serviceNo} already exists.", 'alert alert-danger');
                redirect('instructor');
            }

            $data = [
                'user_id' => $userId,
                'service_no' => $serviceNo,
                'rank' => $rank,
                'full_name' => $fullName,
                'trade' => $trade,
                'contact_no' => $contactNo,
                'email' => $email,
                'status' => $status
            ];

            if ($this->instructorModel->createInstructor($data)) {
                $this->logActivity('CREATE_INSTRUCTOR', 'INSTRUCTORS', "Created instructor record for {$rank} {$fullName} ({$serviceNo})");
                flash('dashboard_success', 'Instructor profile created successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to create instructor profile.', 'alert alert-danger');
            }
            
            redirect('instructor');
        }
    }

    /**
     * Update an Instructor profile
     */
    public function update($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('instructor');
            }

            $serviceNo = trim($_POST['service_no']);
            $rank = trim($_POST['rank']);
            $fullName = trim($_POST['full_name']);
            $trade = trim($_POST['trade']);
            $contactNo = trim($_POST['contact_no']);
            $email = trim($_POST['email']);
            $userId = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
            $status = $_POST['status'] ?? 'active';

            // Validate service number duplication
            if ($this->instructorModel->serviceNoExistsExcluding($serviceNo, $id)) {
                flash('dashboard_error', "Another instructor is already registered with Service Number {$serviceNo}.", 'alert alert-danger');
                redirect('instructor');
            }

            $data = [
                'user_id' => $userId,
                'service_no' => $serviceNo,
                'rank' => $rank,
                'full_name' => $fullName,
                'trade' => $trade,
                'contact_no' => $contactNo,
                'email' => $email,
                'status' => $status
            ];

            if ($this->instructorModel->updateInstructor($id, $data)) {
                $this->logActivity('UPDATE_INSTRUCTOR', 'INSTRUCTORS', "Updated instructor record for {$rank} {$fullName} (ID: {$id})");
                flash('dashboard_success', 'Instructor profile updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update instructor profile.', 'alert alert-danger');
            }
            redirect('instructor');
        }
    }

    /**
     * Delete Instructor profile
     */
    public function delete($id) {
        requireAdmin();

        $instructor = $this->instructorModel->getInstructorById($id);
        if ($instructor) {
            if ($this->instructorModel->deleteInstructor($id)) {
                $this->logActivity('DELETE_INSTRUCTOR', 'INSTRUCTORS', "Deleted instructor record {$instructor->rank} {$instructor->full_name}");
                flash('dashboard_success', 'Instructor profile deleted successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to delete instructor profile.', 'alert alert-danger');
            }
        }
        redirect('instructor');
    }

    /**
     * Self service contact updates for Instructor role
     */
    public function profile() {
        requireInstructor();

        $instructorId = $_SESSION['instructor_id'] ?? null;
        if (!$instructorId) {
            flash('dashboard_error', 'No instructor record associated with your user account.', 'alert alert-danger');
            redirect('dashboard/instructor');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('instructor/profile');
            }

            $contactNo = trim($_POST['contact_no']);
            $email = trim($_POST['email']);

            if ($this->instructorModel->updateInstructorSelf($instructorId, $contactNo, $email)) {
                $this->logActivity('UPDATE_SELF_PROFILE', 'INSTRUCTORS', "Instructor self-updated contact info (ID: {$instructorId})");
                
                // Update session
                $_SESSION['instructor_email'] = $email;
                $_SESSION['instructor_contact'] = $contactNo;

                flash('dashboard_success', 'Contact details updated successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to update contact details.', 'alert alert-danger');
            }
            redirect('instructor/profile');
        } else {
            // GET request - Display profile form
            $instructor = $this->instructorModel->getInstructorById($instructorId);
            
            $data = [
                'title' => 'Update Contact Info',
                'active_menu' => 'profile',
                'instructor' => $instructor
            ];

            $this->view('templates/header', $data);
            $this->view('instructors/profile', $data);
            $this->view('templates/footer');
        }
    }
}
