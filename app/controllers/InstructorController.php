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

            $profilePhoto = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $profilePhoto = $this->handlePhotoUpload($_FILES['profile_photo']);
                } catch (Exception $e) {
                    flash('dashboard_error', $e->getMessage(), 'alert alert-danger');
                    redirect('instructor');
                }
            }

            $data = [
                'user_id' => $userId,
                'service_no' => $serviceNo,
                'rank' => $rank,
                'full_name' => $fullName,
                'trade' => $trade,
                'contact_no' => $contactNo,
                'email' => $email,
                'profile_photo' => $profilePhoto,
                'photo_uploaded_at' => $profilePhoto ? date('Y-m-d H:i:s') : null,
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

            // Validate user_id uniqueness: the chosen login must not already be linked to a different instructor
            if ($userId && $this->instructorModel->userIdExistsExcluding($userId, $id)) {
                flash('dashboard_error', "The selected login account is already linked to another instructor. Please choose a different one.", 'alert alert-danger');
                redirect('instructor');
            }

            $instructor = $this->instructorModel->getInstructorById($id);
            $profilePhoto = $instructor->profile_photo;
            $photoUploadedAt = $instructor->photo_uploaded_at;

            // Check if removing photo
            if (isset($_POST['remove_photo']) && $_POST['remove_photo'] == '1') {
                if ($profilePhoto) {
                    $uploadDir = 'uploads/instructors/';
                    $oldPath = $uploadDir . $profilePhoto;
                    $oldThumbPath = $uploadDir . str_replace('.webp', '_thumb.webp', $profilePhoto);
                    if (file_exists($oldPath)) @unlink($oldPath);
                    if (file_exists($oldThumbPath)) @unlink($oldThumbPath);
                    $profilePhoto = null;
                    $photoUploadedAt = null;
                }
            }

            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $profilePhoto = $this->handlePhotoUpload($_FILES['profile_photo'], $instructor->profile_photo);
                    $photoUploadedAt = date('Y-m-d H:i:s');
                } catch (Exception $e) {
                    flash('dashboard_error', $e->getMessage(), 'alert alert-danger');
                    redirect('instructor');
                }
            }

            $data = [
                'user_id' => $userId,
                'service_no' => $serviceNo,
                'rank' => $rank,
                'full_name' => $fullName,
                'trade' => $trade,
                'contact_no' => $contactNo,
                'email' => $email,
                'profile_photo' => $profilePhoto,
                'photo_uploaded_at' => $photoUploadedAt,
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
     * Delete Instructor profile (POST with CSRF)
     */
    public function delete($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('instructor');
        }

        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
            redirect('instructor');
        }

        $instructor = $this->instructorModel->getInstructorById($id);
        if ($instructor) {
            if ($this->instructorModel->deleteInstructor($id)) {
                $this->logActivity('DELETE_INSTRUCTOR', 'INSTRUCTORS', "Deleted instructor record {$instructor->rank} {$instructor->full_name}");
                flash('dashboard_success', 'Instructor profile deleted successfully.', 'alert alert-success');
            } else {
                flash('dashboard_error', 'Failed to delete instructor profile.', 'alert alert-danger');
            }
        } else {
            flash('dashboard_error', 'Instructor not found.', 'alert alert-danger');
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

            $instructor = $this->instructorModel->getInstructorById($instructorId);
            $profilePhoto = $instructor->profile_photo;
            $photoUploadedAt = $instructor->photo_uploaded_at;

            // Check if removing photo
            if (isset($_POST['remove_photo']) && $_POST['remove_photo'] == '1') {
                if ($profilePhoto) {
                    $uploadDir = 'uploads/instructors/';
                    $oldPath = $uploadDir . $profilePhoto;
                    $oldThumbPath = $uploadDir . str_replace('.webp', '_thumb.webp', $profilePhoto);
                    if (file_exists($oldPath)) @unlink($oldPath);
                    if (file_exists($oldThumbPath)) @unlink($oldThumbPath);
                    $profilePhoto = null;
                    $photoUploadedAt = null;
                }
            }

            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $profilePhoto = $this->handlePhotoUpload($_FILES['profile_photo'], $instructor->profile_photo);
                    $photoUploadedAt = date('Y-m-d H:i:s');
                } catch (Exception $e) {
                    flash('dashboard_error', $e->getMessage(), 'alert alert-danger');
                    redirect('instructor/profile');
                }
            }

            if ($this->instructorModel->updateInstructorSelf($instructorId, $contactNo, $email, $profilePhoto, $photoUploadedAt)) {
                $this->logActivity('UPDATE_SELF_PROFILE', 'INSTRUCTORS', "Instructor self-updated contact info & photo (ID: {$instructorId})");
                
                // Update session
                $_SESSION['instructor_email'] = $email;
                $_SESSION['instructor_contact'] = $contactNo;
                $_SESSION['instructor_photo'] = $profilePhoto;

                flash('dashboard_success', 'Contact details and profile photo updated successfully.', 'alert alert-success');
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

    /**
     * Unified Instructor Profile & Account Registration Page
     */
    public function register() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
                flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
                redirect('instructor/register');
            }

            // Extract & sanitize personal info
            $serviceNo = trim($_POST['service_no']);
            $rank = trim($_POST['rank']);
            $fullName = trim($_POST['full_name']);
            $trade = trim($_POST['trade']);
            $contactNo = trim($_POST['contact_no']);
            $email = trim($_POST['email']);

            // Extract system account info
            $createLogin = isset($_POST['create_login']) ? 1 : 0;
            $username = trim($_POST['username'] ?? '');
            $tempPassword = $_POST['temp_password'] ?? '';
            $accountStatus = $_POST['account_status'] ?? 'active';
            $forceChange = isset($_POST['force_change']) ? 1 : 0;

            // Simple validation
            $errors = [];
            if (empty($serviceNo)) $errors[] = "Service Number is required.";
            if (empty($fullName)) $errors[] = "Full Name is required.";
            if (empty($rank)) $errors[] = "Rank is required.";

            // Check duplicate service no
            if ($this->instructorModel->serviceNoExists($serviceNo)) {
                $errors[] = "Instructor with Service Number '{$serviceNo}' is already registered.";
            }

            $profilePhoto = null;
            $photoUploaded = false;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                try {
                    $profilePhoto = $this->handlePhotoUpload($_FILES['profile_photo']);
                    $photoUploaded = true;
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if ($createLogin) {
                if (empty($username)) $errors[] = "Username is required for system account.";
                if (empty($tempPassword)) $errors[] = "Temporary Password is required for system account.";
                if (strlen($tempPassword) < 8) $errors[] = "Temporary Password must be at least 8 characters.";
                
                // Check duplicate username
                if ($this->userModel->findUserByUsername($username)) {
                    $errors[] = "Username '{$username}' already exists.";
                }
            }

            if (!empty($errors)) {
                // If we uploaded a photo but validation failed, clean it up
                if ($photoUploaded && $profilePhoto) {
                    $uploadDir = 'uploads/instructors/';
                    @unlink($uploadDir . $profilePhoto);
                    @unlink($uploadDir . str_replace('.webp', '_thumb.webp', $profilePhoto));
                }
                $data = [
                    'title' => 'Register Instructor',
                    'active_menu' => 'register_instructor',
                    'errors' => $errors,
                    'ranks' => ['LAC', 'CPL', 'SGT', 'FSGT', 'WO', 'PLT OF', 'FG OFF', 'FLT LT', 'SQN LDR', 'WG CDR', 'GP CAPT'],
                    'old' => $_POST
                ];
                $this->view('templates/header', $data);
                $this->view('instructors/register', $data);
                $this->view('templates/footer');
                return;
            }

            // Create login account if selected
            $userId = null;
            if ($createLogin) {
                $userId = $this->userModel->createUser($username, $tempPassword, 2, $accountStatus, $forceChange, 90);
                if (!$userId) {
                    if ($profilePhoto) {
                        $uploadDir = 'uploads/instructors/';
                        @unlink($uploadDir . $profilePhoto);
                        @unlink($uploadDir . str_replace('.webp', '_thumb.webp', $profilePhoto));
                    }
                    flash('dashboard_error', 'Failed to create user account. Registration cancelled.', 'alert alert-danger');
                    redirect('instructor/register');
                }
            }

            // Create instructor profile
            $instructorData = [
                'user_id' => $userId,
                'service_no' => $serviceNo,
                'rank' => $rank,
                'full_name' => $fullName,
                'trade' => $trade,
                'contact_no' => $contactNo,
                'email' => $email,
                'profile_photo' => $profilePhoto,
                'photo_uploaded_at' => $profilePhoto ? date('Y-m-d H:i:s') : null,
                'status' => 'active'
            ];

            if ($this->instructorModel->createInstructor($instructorData)) {
                $this->logActivity('CREATE_INSTRUCTOR', 'INSTRUCTORS', "Registered instructor profile & account for {$rank} {$fullName} ({$serviceNo})");
                flash('dashboard_success', 'Instructor registered successfully.', 'alert alert-success');
                redirect('instructor');
            } else {
                // Rollback user account if created
                if ($userId) {
                    $this->userModel->deleteUser($userId);
                }
                if ($profilePhoto) {
                    $uploadDir = 'uploads/instructors/';
                    @unlink($uploadDir . $profilePhoto);
                    @unlink($uploadDir . str_replace('.webp', '_thumb.webp', $profilePhoto));
                }
                flash('dashboard_error', 'Failed to register instructor profile.', 'alert alert-danger');
                redirect('instructor/register');
            }
        } else {
            // Display form
            $data = [
                'title' => 'Register Instructor',
                'active_menu' => 'register_instructor',
                'ranks' => ['LAC', 'CPL', 'SGT', 'FSGT', 'WO', 'PLT OF', 'FG OFF', 'FLT LT', 'SQN LDR', 'WG CDR', 'GP CAPT'],
                'old' => []
            ];

            $this->view('templates/header', $data);
            $this->view('instructors/register', $data);
            $this->view('templates/footer');
        }
    }

    /**
     * Add Login Credentials to an existing unlinked Instructor
     */
    public function addLogin($id) {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('instructor');
        }

        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            flash('dashboard_error', 'Invalid security token.', 'alert alert-danger');
            redirect('instructor');
        }

        // Fetch the instructor to verify they exist and are unlinked
        $instructor = $this->instructorModel->getInstructorById($id);
        if (!$instructor) {
            flash('dashboard_error', 'Instructor not found.', 'alert alert-danger');
            redirect('instructor');
        }

        if ($instructor->user_id) {
            flash('dashboard_error', "This instructor already has a linked login account ('{$instructor->username}').", 'alert alert-warning');
            redirect('instructor');
        }

        $username      = trim($_POST['username'] ?? '');
        $tempPassword  = $_POST['temp_password'] ?? '';
        $accountStatus = $_POST['account_status'] ?? 'active';
        $forceChange   = isset($_POST['force_change']) ? 1 : 0;

        // Validate
        if (empty($username) || empty($tempPassword)) {
            flash('dashboard_error', 'Username and Password are required.', 'alert alert-danger');
            redirect('instructor');
        }
        if (strlen($tempPassword) < 8) {
            flash('dashboard_error', 'Password must be at least 8 characters.', 'alert alert-danger');
            redirect('instructor');
        }

        // Check username uniqueness
        if ($this->userModel->findUserByUsername($username)) {
            flash('dashboard_error', "Username '{$username}' already exists. Choose a different username.", 'alert alert-danger');
            redirect('instructor');
        }

        // Create the user account
        $userId = $this->userModel->createUser($username, $tempPassword, 2, $accountStatus, $forceChange, 90);
        if (!$userId) {
            flash('dashboard_error', 'Failed to create user account. Please try again.', 'alert alert-danger');
            redirect('instructor');
        }

        // Link the new user account to the instructor record
        $linkData = [
            'user_id'    => $userId,
            'service_no' => $instructor->service_no,
            'rank'       => $instructor->rank,
            'full_name'  => $instructor->full_name,
            'trade'      => $instructor->trade,
            'contact_no' => $instructor->contact_no,
            'email'      => $instructor->email,
            'status'     => $instructor->status,
        ];

        if ($this->instructorModel->updateInstructor($id, $linkData)) {
            $this->logActivity('ADD_LOGIN', 'INSTRUCTORS', "Created and linked login account '{$username}' to instructor {$instructor->rank} {$instructor->full_name} (ID: {$id})");
            flash('dashboard_success', "Login account '{$username}' created and linked to {$instructor->rank} {$instructor->full_name} successfully.", 'alert alert-success');
        } else {
            // Rollback the user account
            $this->userModel->deleteUser($userId);
            flash('dashboard_error', 'Failed to link login account to instructor. Please try again.', 'alert alert-danger');
        }

        redirect('instructor');
    }

    /**
     * AJAX Instant Search for Instructors
     */
    public function searchAjax() {
        requireAdmin();

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $rank = isset($_GET['rank']) ? trim($_GET['rank']) : '';
        $trade = isset($_GET['trade']) ? trim($_GET['trade']) : '';

        $instructors = $this->instructorModel->searchInstructors($search, $rank, $trade);
        
        $response = [];
        foreach ($instructors as $i) {
            $response[] = [
                'id' => $i->id,
                'service_no' => $i->service_no,
                'rank' => $i->rank,
                'full_name' => $i->full_name,
                'trade' => $i->trade,
                'contact_no' => $i->contact_no ?: 'N/A',
                'email' => $i->email ?: 'N/A',
                'status' => $i->status,
                'username' => $i->username ?: 'No Login',
                'profile_photo' => $i->profile_photo ?: ''
            ];
        }

        $this->json($response);
    }

    /**
     * Securely process, validate, resize and compress profile photos to WebP.
     */
    private function handlePhotoUpload($file, $existingPhoto = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return $existingPhoto;
        }

        // Limit size to 5 MB
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("Maximum allowed photo size is 5 MB.");
        }

        // Validate MIME type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $fileMime = mime_content_type($file['tmp_name']);
        if (!in_array($fileMime, $allowedTypes)) {
            throw new Exception("Supported formats are: JPG, JPEG, PNG, and WEBP.");
        }

        // Verify it is a valid image
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception("Uploaded file is not a valid image.");
        }

        // Generate randomized filename
        $randHex = bin2hex(random_bytes(4));
        $uploadDir = 'uploads/instructors/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (extension_loaded('gd')) {
            // Create resource
            switch ($fileMime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $srcImage = @imagecreatefromjpeg($file['tmp_name']);
                    break;
                case 'image/png':
                    $srcImage = @imagecreatefrompng($file['tmp_name']);
                    break;
                case 'image/webp':
                    $srcImage = @imagecreatefromwebp($file['tmp_name']);
                    break;
                default:
                    $srcImage = null;
            }

            if (!$srcImage) {
                throw new Exception("Failed to process image file.");
            }

            $newFilename = "instructor_" . $randHex . ".webp";
            $newThumbFilename = "instructor_" . $randHex . "_thumb.webp";

            $destPath = $uploadDir . $newFilename;
            $thumbPath = $uploadDir . $newThumbFilename;

            // Resize and optimize to WebP
            $this->resizeAndSaveImage($srcImage, $imageInfo[0], $imageInfo[1], 400, 400, $destPath);
            $this->resizeAndSaveImage($srcImage, $imageInfo[0], $imageInfo[1], 100, 100, $thumbPath);

            imagedestroy($srcImage);
        } else {
            // Fallback when GD extension is disabled/not installed: save original file
            $origExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (empty($origExtension)) {
                $extensions = [
                    'image/jpeg' => 'jpg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];
                $origExtension = isset($extensions[$fileMime]) ? $extensions[$fileMime] : 'jpg';
            }
            $origExtension = strtolower($origExtension);

            $newFilename = "instructor_" . $randHex . "." . $origExtension;
            $newThumbFilename = "instructor_" . $randHex . "_thumb." . $origExtension;

            $destPath = $uploadDir . $newFilename;
            $thumbPath = $uploadDir . $newThumbFilename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                throw new Exception("Failed to save uploaded image.");
            }
            // Duplicate original file for thumbnail
            copy($destPath, $thumbPath);
        }

        // Delete old photo and thumbnail if they exist
        if ($existingPhoto) {
            $oldPath = $uploadDir . $existingPhoto;
            $oldThumb = preg_replace('/(\.[a-zA-Z0-9]+)$/', '_thumb$1', $existingPhoto);
            $oldThumbPath = $uploadDir . $oldThumb;
            if (file_exists($oldPath)) @unlink($oldPath);
            if (file_exists($oldThumbPath)) @unlink($oldThumbPath);
        }

        return $newFilename;
    }

    /**
     * Resizes and crops or fits image, saving as WebP
     */
    private function resizeAndSaveImage($srcImage, $srcW, $srcH, $maxW, $maxH, $destPath) {
        $ratio = min($maxW / $srcW, $maxH / $srcH);
        $newW = round($srcW * $ratio);
        $newH = round($srcH * $ratio);

        $destImage = imagecreatetruecolor($newW, $newH);

        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
        imagefilledrectangle($destImage, 0, 0, $newW, $newH, $transparent);

        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

        imagewebp($destImage, $destPath, 80);
        imagedestroy($destImage);
    }
}
