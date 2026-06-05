<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? e($data['title']) . ' - ' . e(SITENAME) : e(SYSTEM_TITLE); ?></title>
    
    <!-- CSRF Token Meta Tag for AJAX -->
    <meta name="csrf-token" content="<?php echo generateCsrfToken(); ?>">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/css/bootstrap.min.css" rel="stylesheet" onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- FullCalendar CSS CDN (required for calendar view) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>css/style.css">
    
    <!-- Render Theme Config Inline to Prevent FOUC -->
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    </script>
</head>
<body>

<div class="app-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="<?php echo URLROOT; ?>images/Picture1.png"
                 alt="SLAF Crest"
                 style="width:38px;height:38px;object-fit:contain;flex-shrink:0;filter:drop-shadow(0 1px 4px rgba(0,0,0,0.3));"
                 onerror="this.outerHTML='<i class=\'bi bi-shield-fill-check text-white me-2 fs-3\'></i>';">
            <div class="sidebar-brand-text">
                <span class="d-block fs-6 fw-bold"><?php echo e(MILITARY_BRANCH); ?></span>
                <span class="d-block text-muted" style="font-size: 0.7rem; font-weight: 500; letter-spacing: 0px; text-transform: uppercase;">CLMS Ekala</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-menu-header">Main Menu</li>
            
            <?php if (isAdmin()): ?>
                <!-- Administrator Sidebar Options -->
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>dashboard/admin" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                
                <li class="sidebar-menu-header">Instructor Management</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'instructors') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>instructor" class="sidebar-link">
                        <i class="bi bi-people-fill"></i> View Instructors
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'register_instructor') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>instructor/register" class="sidebar-link">
                        <i class="bi bi-person-plus-fill"></i> Register Instructor
                    </a>
                </li>

                <li class="sidebar-menu-header">Syllabus</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'lessons') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>lesson" class="sidebar-link">
                        <i class="bi bi-book-half"></i> Syllabus Lessons
                    </a>
                </li>

                <li class="sidebar-menu-header">Resources</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'laboratories') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>laboratory" class="sidebar-link">
                        <i class="bi bi-door-closed-fill"></i> Laboratories
                    </a>
                </li>
                
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'computers') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>equipment/computers" class="sidebar-link">
                        <i class="bi bi-pc-display"></i> Computers Inventory
                    </a>
                </li>
                
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'smartboards') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>equipment/smartboards" class="sidebar-link">
                        <i class="bi bi-easel-fill"></i> Smart Boards
                    </a>
                </li>

                <li class="sidebar-menu-header">Operational Schedules</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'schedule') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>allocation/schedule" class="sidebar-link">
                        <i class="bi bi-calendar-event"></i> Allocate Labs
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'calendar') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>allocation/calendar" class="sidebar-link">
                        <i class="bi bi-calendar3"></i> Interactive Calendar
                    </a>
                </li>

                <li class="sidebar-menu-header">Support Tickets</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'requests') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>request" class="sidebar-link">
                        <i class="bi bi-arrow-left-right"></i> Change Requests
                        <?php if (isset($_SESSION['pending_requests_count']) && $_SESSION['pending_requests_count'] > 0): ?>
                            <span class="badge bg-danger ms-auto rounded-pill"><?php echo $_SESSION['pending_requests_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'faults') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>fault" class="sidebar-link">
                        <i class="bi bi-exclamation-triangle-fill"></i> Fault Reports
                        <?php if (isset($_SESSION['pending_faults_count']) && $_SESSION['pending_faults_count'] > 0): ?>
                            <span class="badge bg-danger ms-auto rounded-pill"><?php echo $_SESSION['pending_faults_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'maintenance') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>maintenance" class="sidebar-link">
                        <i class="bi bi-wrench-adjustable"></i> Maintenance
                    </a>
                </li>
                <li class="sidebar-menu-header">Operations</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'inq_instructor') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/instructorActivity" class="sidebar-link">
                        <i class="bi bi-people-fill"></i> Instructor Activity
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'inq_lab_sessions') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/labSessions" class="sidebar-link">
                        <i class="bi bi-door-closed-fill"></i> Lab Session Inquiry
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'inq_lecture') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/lectureHours" class="sidebar-link">
                        <i class="bi bi-clock-history"></i> Lecture Hours Analysis
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'inq_lab_util') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/labUtilization" class="sidebar-link">
                        <i class="bi bi-pie-chart-fill"></i> Lab Utilization
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'inq_session_hist') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/sessionHistory" class="sidebar-link">
                        <i class="bi bi-journal-text"></i> Session History
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'inq_equipment') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/equipmentUsage" class="sidebar-link">
                        <i class="bi bi-cpu-fill"></i> Equipment Usage
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'session_completion_records') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>inquiry/sessionCompletionRecords" class="sidebar-link">
                        <i class="bi bi-journal-check"></i> Session Completion Records
                    </a>
                </li>
                <li class="sidebar-menu-header">Administration</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'user_management') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>user" class="sidebar-link">
                        <i class="bi bi-people-fill"></i> User Management
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'my_password') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>auth/myPassword" class="sidebar-link">
                        <i class="bi bi-key-fill"></i> Change My Password
                    </a>
                </li>

                <li class="sidebar-menu-header">System Administration</li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'notices') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>notice" class="sidebar-link">
                        <i class="bi bi-card-heading"></i> Publish Notices
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'reports') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>report" class="sidebar-link">
                        <i class="bi bi-file-earmark-bar-graph"></i> Workload & Analytics
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'audit') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>audit" class="sidebar-link">
                        <i class="bi bi-journal-text"></i> System Audit Logs
                    </a>
                </li>

            <?php elseif (isInstructor()): ?>
                <!-- Instructor Sidebar Options -->
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>dashboard/instructor" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'my_schedule') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>allocation/mySchedule" class="sidebar-link">
                        <i class="bi bi-calendar-check-fill"></i> My Schedule
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'calendar') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>allocation/calendar" class="sidebar-link">
                        <i class="bi bi-calendar3"></i> School Calendar
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'my_history') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>allocation/myHistory" class="sidebar-link">
                        <i class="bi bi-clock-history"></i> My Session History
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'requests') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>request/instructor" class="sidebar-link">
                        <i class="bi bi-arrow-left-right"></i> Change Requests
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'faults') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>fault/instructor" class="sidebar-link">
                        <i class="bi bi-exclamation-triangle-fill"></i> Fault Reporting
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'profile') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>instructor/profile" class="sidebar-link">
                        <i class="bi bi-person-gear"></i> Update Contact
                    </a>
                </li>
                <li class="sidebar-item <?php echo (isset($data['active_menu']) && $data['active_menu'] === 'my_password') ? 'active' : ''; ?>">
                    <a href="<?php echo URLROOT; ?>auth/myPassword" class="sidebar-link">
                        <i class="bi bi-key-fill"></i> Change My Password
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <div class="sidebar-footer">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-lock-fill text-muted me-2"></i>
                <span class="small text-muted">SLAF CLMS &middot; v1.0</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <button class="sidebar-toggle-btn" id="sidebar-toggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold" style="font-size:0.95rem;color:var(--text-h);">
                        <?php echo e(SYSTEM_TITLE); ?>
                    </h5>
                    <span style="font-size:0.75rem;color:var(--text-muted);"><?php echo e(SYSTEM_SUBTITLE); ?></span>
                </div>
            </div>

            <div class="navbar-right">
                <!-- Theme Switcher -->
                <button class="theme-switch" id="theme-toggle" title="Toggle Light/Dark Theme">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary border-0 position-relative p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <?php 
                        // Fetch unread notification counts dynamically
                        $notifModel = new NotificationModel();
                        $unreadCount = $notifModel->getUnreadCount($_SESSION['user_id']);
                        if ($unreadCount > 0): 
                        ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                <?php echo $unreadCount; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-0" style="width:320px;border-radius:12px;overflow:hidden;background:var(--bg-card);border:1px solid var(--border);box-shadow:0 8px 32px rgba(0,0,0,0.12);">
                        <li style="background:var(--primary-grad);color:#fff;" class="p-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-bell-fill me-2"></i> Notifications</h6>
                            <?php if ($unreadCount > 0): ?>
                                <a href="<?php echo URLROOT; ?>dashboard/clearNotifications" class="text-white small text-decoration-underline" style="font-size: 0.75rem;">Mark all read</a>
                            <?php endif; ?>
                        </li>
                        <div style="max-height: 250px; overflow-y: auto;">
                            <?php 
                            $notifications = $notifModel->getNotificationsByUser($_SESSION['user_id'], 5);
                            if (!empty($notifications)):
                                foreach ($notifications as $n):
                            ?>
                                <li class="p-3 border-bottom" style="font-size:0.85rem;background:<?php echo $n->is_read ? 'var(--bg-card)' : 'var(--primary-light)'; ?>;border-color:var(--border)!important;">
                                    <span class="text-secondary small d-block mb-1"><i class="bi bi-clock me-1"></i><?php echo date('d M Y H:i', strtotime($n->created_at)); ?></span>
                                    <p class="mb-0 text-wrap text-break" style="color:var(--text-body);"><?php echo e($n->message); ?></p>
                                </li>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <li class="p-4 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-4 d-block mb-2 text-secondary"></i>
                                    <span class="small">No notifications found</span>
                                </li>
                            <?php endif; ?>
                        </div>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle profile-dropdown d-flex align-items-center gap-2 p-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if(isInstructor() && !empty($_SESSION['instructor_photo'])): 
                            $isWebp = (pathinfo($_SESSION['instructor_photo'], PATHINFO_EXTENSION) === 'webp');
                            $thumb = $isWebp ? preg_replace('/(\.[a-zA-Z0-9]+)$/', '_thumb$1', $_SESSION['instructor_photo']) : $_SESSION['instructor_photo'];
                        ?>
                            <img src="<?php echo URLROOT; ?>uploads/instructors/<?php echo $thumb; ?>?v=<?php echo time(); ?>" class="rounded-circle border border-primary" style="width:32px; height:32px; object-fit:cover;">
                        <?php else: ?>
                            <i class="bi bi-person-circle fs-4 text-secondary"></i>
                        <?php endif; ?>
                        <span class="d-none d-lg-inline small fw-semibold text-capitalize text-wrap text-start" style="color: var(--text-primary); max-width: 120px;">
                            <?php 
                            if (isInstructor() && isset($_SESSION['instructor_name'])) {
                                echo e($_SESSION['instructor_rank'] . ' ' . $_SESSION['instructor_name']);
                            } else {
                                echo e($_SESSION['username']);
                            }
                            ?>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px;background:var(--bg-card);border:1px solid var(--border);box-shadow:0 8px 32px rgba(0,0,0,0.12);">
                        <li class="dropdown-header text-uppercase pb-1" style="font-size: 0.7rem; font-weight: 700;">Role</li>
                        <li><span class="dropdown-item-text fw-bold text-primary small pt-0"><?php echo e($_SESSION['user_role_name']); ?></span></li>
                        <li><hr class="dropdown-divider border-color"></li>
                        <?php if (isInstructor()): ?>
                            <li><a class="dropdown-item small" href="<?php echo URLROOT; ?>instructor/profile"><i class="bi bi-person-gear me-2 text-secondary"></i> Edit Contact Info</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item small" href="<?php echo URLROOT; ?>auth/myPassword"><i class="bi bi-key-fill me-2 text-secondary"></i> Change My Password</a></li>
                        <li><hr class="dropdown-divider border-color"></li>
                        <li><a class="dropdown-item small text-danger" href="<?php echo URLROOT; ?>auth/logout"><i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <div class="content-body">
            <!-- Flash Message Banner -->
            <?php flash('dashboard_error'); ?>
            <?php flash('dashboard_success'); ?>
