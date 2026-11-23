<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Class Timetable & Schedule Confirmation - SLAF CLMS</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- FullCalendar CSS & JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>css/style.css?v=<?php echo time(); ?>">
    
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    </script>

    <style>
        .public-nav {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: var(--shadow-sm);
        }
        .hero-banner {
            background: linear-gradient(135deg, rgba(45, 108, 223, 0.08) 0%, rgba(79, 70, 229, 0.05) 100%);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem 2.2rem;
            margin-bottom: 2rem;
        }
        .portal-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            background: rgba(45, 108, 223, 0.12);
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nav-tabs-custom {
            border-bottom: 2px solid var(--border);
            gap: 8px;
        }
        .nav-tabs-custom .nav-link {
            border: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px 8px 0 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .nav-tabs-custom .nav-link:hover {
            color: var(--primary);
            background: rgba(45, 108, 223, 0.05);
        }
        .nav-tabs-custom .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: transparent;
        }
        .confirmation-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .confirmation-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }
        .card-stripe {
            height: 4px;
            width: 100%;
            background: var(--primary-grad);
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 700;
            color: var(--text-h);
        }
        .fc .fc-button-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: var(--border) !important;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
            font-weight: 500;
        }
        @media print {
            .no-print, .public-nav, .nav-tabs-custom, .hero-banner {
                display: none !important;
            }
            .modal-content {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body style="background-color: var(--bg-page); min-height: 100vh;">

<!-- Public Top Navigation -->
<header class="public-nav">
    <div class="container-xl d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <img src="<?php echo URLROOT; ?>images/Picture1.png" alt="SLAF Crest" style="width:42px;height:42px;object-fit:contain;" onerror="this.outerHTML='<i class=\'bi bi-shield-fill-check fs-2 text-primary\'></i>';">
            <div>
                <span class="d-block fw-bold text-dark lh-1" style="font-size: 1rem; color: var(--text-h) !important;">Sri Lanka Air Force &middot; TTS Ekala</span>
                <span class="small text-muted" style="font-size: 0.75rem;">Computer Laboratory Management System (CLMS)</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Theme Toggle Button -->
            <button class="btn btn-sm btn-outline-secondary border-0 px-2" id="theme-toggle" title="Toggle Light/Dark Theme">
                <i class="bi bi-moon-stars-fill"></i>
            </button>

            <!-- Sign In Portal Button -->
            <a href="<?php echo URLROOT; ?>auth/login" class="btn btn-primary btn-sm px-3 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Staff Login
            </a>
        </div>
    </div>
</header>

<!-- Main Container -->
<main class="container-xl py-4">

    <!-- Hero / Title Section -->
    <div class="hero-banner">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="portal-pill mb-2">
                    <i class="bi bi-broadcast"></i> Public Guest Portal &middot; Live Official Roster
                </div>
                <h2 class="fw-bold mb-2" style="color: var(--text-h); font-size: 1.75rem;">
                    Class Timetable Forecast Calendar &amp; Schedule Confirmation
                </h2>
                <p class="text-muted mb-0" style="font-size: 0.95rem; max-width: 680px;">
                    Welcome to the SLAF Computer Laboratory official public schedule portal. Trainees, instructors, and service personnel can view published timetables, forecast laboratory room allocations, and confirm scheduled class sessions in real-time without signing in.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex flex-column align-items-lg-end gap-1 p-3 bg-white bg-opacity-75 rounded border border-color">
                    <span class="small text-muted fw-semibold"><i class="bi bi-clock-history me-1"></i> Current Date &amp; Time</span>
                    <span class="fw-bold fs-6 text-primary" id="liveClock"><?php echo date('l, d F Y'); ?></span>
                    <span class="badge bg-success-subtle text-success mt-1"><i class="bi bi-check-circle-fill me-1"></i> Official Published Roster</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mode Navigation Tabs -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="rosterTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="forecast-tab" data-bs-toggle="tab" data-bs-target="#forecast-pane" type="button" role="tab">
                <i class="bi bi-calendar3-range fs-5"></i> Forecast Calendar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="confirmation-tab" data-bs-toggle="tab" data-bs-target="#confirmation-pane" type="button" role="tab">
                <i class="bi bi-patch-check-fill fs-5 text-primary"></i> Schedule Confirmation &amp; Verification
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="rosterTabsContent">

        <!-- TAB 1: FORECAST CALENDAR -->
        <div class="tab-pane fade show active" id="forecast-pane" role="tabpanel">
            <div class="card-clms p-4 mb-4">
                
                <!-- Filter Toolbar -->
                <div class="row g-3 align-items-center mb-4 pb-3 border-bottom border-color">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-geo-alt-fill text-primary me-1"></i> Camp Location</label>
                        <select id="filter_camp" class="form-select form-control-clms">
                            <option value="">-- All Camps / Stations --</option>
                            <?php foreach ($data['camps'] as $c): ?>
                                <option value="<?php echo $c->id; ?>"><?php echo e($c->name); ?> (<?php echo e($c->code); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-door-closed-fill text-secondary me-1"></i> Laboratory Room</label>
                        <select id="filter_lab" class="form-select form-control-clms">
                            <option value="">-- All Computer Laboratories --</option>
                            <?php foreach ($data['labs'] as $l): ?>
                                <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> &mdash; <?php echo e($l->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="button" id="btn_apply_filters" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-funnel-fill me-1"></i> Apply Filters
                        </button>
                        <button type="button" id="btn_reset_filters" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>

                <!-- Color legend guide -->
                <div class="d-flex flex-wrap gap-3 align-items-center mb-3 p-2 bg-light rounded border border-color small">
                    <span class="fw-bold text-muted"><i class="bi bi-palette-fill me-1"></i> Legend:</span>
                    <div class="d-flex align-items-center gap-1">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#1d3557;"></span>
                        <span>Lab 01 (Cisco/Network)</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#2b6cb0;"></span>
                        <span>Lab 02 (Software/Web)</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#0d9488;"></span>
                        <span>Lab 03 (Hardware/Tech)</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#059669;"></span>
                        <span>Completed Session</span>
                    </div>
                    <span class="ms-auto text-muted fst-italic">Click any timetable block to inspect full reservation confirmation.</span>
                </div>

                <!-- Calendar Mounting Container -->
                <div id="publicCalendar" style="min-height: 600px;"></div>
            </div>
        </div>

        <!-- TAB 2: SCHEDULE CONFIRMATION & VERIFICATION -->
        <div class="tab-pane fade" id="confirmation-pane" role="tabpanel">
            <div class="card-clms p-4 mb-4">
                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-search me-1 text-primary"></i> Search by Service No, Instructor Name, Batch or Lesson
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-color"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="confirm_search_input" class="form-control form-control-clms" placeholder="e.g. 24589, Weerasooriya, Batch 45, or LES-IT01...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-calendar-event me-1 text-primary"></i> Scheduled Date</label>
                        <input type="date" id="confirm_date_input" class="form-control form-control-clms">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="btn_search_confirm" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Verify Schedule
                        </button>
                    </div>
                </div>

                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark" style="color:var(--text-h) !important;">
                        <i class="bi bi-list-check me-1 text-primary"></i> Verified Schedule Allocations (<span id="results_count"><?php echo count($data['initial_roster']); ?></span>)
                    </h6>
                    <small class="text-muted">Showing officially published timetable allocations.</small>
                </div>

                <!-- Cards Grid Container -->
                <div id="confirm_results_grid" class="row g-3">
                    <?php if (!empty($data['initial_roster'])): ?>
                        <?php foreach ($data['initial_roster'] as $item): ?>
                            <?php 
                                $batchName = !empty($item->batch) ? $item->batch : (!empty($item->remarks) ? $item->remarks : 'General Batch');
                                $statusBadge = 'bg-primary-subtle text-primary';
                                if ($item->session_status === 'Completed' || $item->session_status === 'Completed Successfully') {
                                    $statusBadge = 'bg-success-subtle text-success';
                                } elseif ($item->session_status === 'Cancelled') {
                                    $statusBadge = 'bg-danger-subtle text-danger';
                                }
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="confirmation-card h-100 p-3 d-flex flex-column justify-content-between">
                                    <div class="card-stripe mb-3"></div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-secondary-subtle text-secondary small fw-semibold">
                                                #AL-<?php echo sprintf('%05d', $item->id); ?>
                                            </span>
                                            <span class="badge <?php echo $statusBadge; ?> small">
                                                <?php echo e($item->session_status); ?>
                                            </span>
                                        </div>

                                        <!-- Batch Banner -->
                                        <div class="mb-2">
                                            <span class="badge bg-primary-subtle text-primary fs-7 fw-bold">
                                                <i class="bi bi-people-fill me-1"></i> <?php echo e($batchName); ?>
                                            </span>
                                        </div>

                                        <!-- Lesson Title -->
                                        <h6 class="fw-bold mb-1" style="color:var(--text-h); font-size:0.95rem;">
                                            <?php echo e($item->lesson_code); ?> &mdash; <?php echo e($item->lesson_name); ?>
                                        </h6>

                                        <!-- Instructor -->
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-person-badge-fill me-1 text-secondary"></i>
                                            <span class="fw-semibold text-dark"><?php echo e($item->instructor_rank); ?> <?php echo e($item->instructor_name); ?></span>
                                            <?php if (!empty($item->instructor_service_no)): ?>
                                                <span class="badge bg-light text-muted border border-color ms-1"><?php echo e($item->instructor_service_no); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Lab & Location -->
                                        <div class="small text-muted mb-3">
                                            <i class="bi bi-door-closed me-1"></i> <?php echo e($item->lab_code); ?> (<?php echo e($item->lab_name); ?>)
                                            <div class="text-secondary small mt-1"><i class="bi bi-geo-alt me-1"></i> <?php echo e($item->camp_name ?: 'Main Station'); ?></div>
                                        </div>
                                    </div>

                                    <!-- Date & Time Box -->
                                    <div class="pt-2 border-top border-color">
                                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                                            <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> <?php echo date('D, d M Y', strtotime($item->date)); ?></span>
                                            <span class="fw-bold text-primary"><i class="bi bi-clock me-1"></i> <?php echo date('H:i', strtotime($item->start_time)) . ' - ' . date('H:i', strtotime($item->end_time)); ?></span>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-semibold btn-view-confirm" data-id="<?php echo $item->id; ?>">
                                            <i class="bi bi-card-checklist me-1"></i> View Schedule Confirmation Slip
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold">No published class session allocations found.</h6>
                            <p class="small">Try adjusting your search criteria or date filter above.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

</main>

<!-- CONFIRMATION & ALLOCATION SLIP MODAL -->
<div class="modal fade" id="rosterDetailModal" tabindex="-1" aria-labelledby="rosterDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-lg);">
            <div class="modal-header border-color">
                <div class="d-flex align-items-center gap-2">
                    <img src="<?php echo URLROOT; ?>images/Picture1.png" alt="SLAF Crest" style="width:30px;height:30px;object-fit:contain;" onerror="this.style.display='none';">
                    <h5 class="modal-title fw-bold" id="rosterDetailModalLabel" style="font-size:1.05rem;color:var(--text-h);">
                        Official Schedule Confirmation Slip
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" id="printableSlipArea">
                <!-- Watermark Header in Slip -->
                <div class="text-center pb-3 border-bottom border-color mb-3">
                    <span class="d-block fw-bold text-primary fs-6">SRI LANKA AIR FORCE</span>
                    <span class="d-block text-muted small fw-semibold">Trade Training School Ekala &middot; Computer Laboratory Management</span>
                    <div class="badge bg-success-subtle text-success mt-2 py-1 px-3">
                        <i class="bi bi-shield-fill-check me-1"></i> VERIFIED OFFICIAL TIMETABLE RESERVATION
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted mb-0">Class / Course Batch</label>
                    <div class="fw-bold text-primary fs-5" id="modal_batch"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted mb-0">Course Lesson / Module</label>
                    <div class="fw-semibold text-dark" id="modal_lesson"></div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-0">Assigned Instructor</label>
                        <div class="fw-semibold text-dark" id="modal_instructor"></div>
                        <small class="text-muted" id="modal_service_no"></small>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-0">Laboratory Room</label>
                        <div class="fw-semibold text-primary" id="modal_lab"></div>
                        <small class="text-muted" id="modal_camp"></small>
                    </div>
                </div>

                <div class="row g-2 mb-3 p-2 bg-light rounded border border-color">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-0">Scheduled Date</label>
                        <div class="fw-bold text-dark" id="modal_date"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted mb-0">Reserved Time Block</label>
                        <div class="fw-bold text-primary" id="modal_time"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 text-muted small">
                    <span>Reference ID: <strong id="modal_ref" class="text-dark"></strong></span>
                    <span>Status: <span id="modal_status" class="badge"></span></span>
                </div>
            </div>

            <div class="modal-footer border-color no-print d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print();">
                    <i class="bi bi-printer-fill me-1"></i> Print Confirmation
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="mt-5 py-4 border-top border-color bg-white small text-center text-muted no-print">
    <div class="container-xl">
        <p class="mb-1 fw-semibold">Sri Lanka Air Force &middot; Computer Laboratory Management System</p>
        <p class="mb-0">Class Timetable Forecast Calendar &amp; Schedule Confirmation &middot; All Rights Reserved &copy; <?php echo date('Y'); ?></p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Theme toggle handler
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        });
    }

    // 2. Initialize Public FullCalendar
    const calendarEl = document.getElementById('publicCalendar');
    let calendar;

    function initCalendar() {
        if (!calendarEl) return;
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            height: 'auto',
            navLinks: true,
            editable: false,
            droppable: false,
            timeZone: 'local',
            events: function(fetchInfo, successCallback, failureCallback) {
                const campId = document.getElementById('filter_camp').value;
                const labId = document.getElementById('filter_lab').value;
                
                let url = '<?php echo URLROOT; ?>roster/getCalendarEvents?start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr);
                if (campId) url += '&camp_id=' + encodeURIComponent(campId);
                if (labId) url += '&lab_id=' + encodeURIComponent(labId);
                
                fetch(url)
                    .then(response => response.json())
                    .then(events => successCallback(events))
                    .catch(err => failureCallback(err));
            },
            eventClick: function(info) {
                const props = info.event.extendedProps;
                openModalFromProps(props, info.event.start, info.event.end);
            }
        });

        calendar.render();
    }

    initCalendar();

    // Re-render calendar when tab switches
    const forecastTab = document.getElementById('forecast-tab');
    if (forecastTab) {
        forecastTab.addEventListener('shown.bs.tab', function () {
            if (calendar) calendar.render();
        });
    }

    // 3. Filter Application
    document.getElementById('btn_apply_filters').addEventListener('click', function() {
        if (calendar) calendar.refetchEvents();
    });

    document.getElementById('btn_reset_filters').addEventListener('click', function() {
        document.getElementById('filter_camp').value = '';
        document.getElementById('filter_lab').value = '';
        if (calendar) calendar.refetchEvents();
    });

    // 4. Modal Population
    const modalEl = document.getElementById('rosterDetailModal');
    const bsModal = new bootstrap.Modal(modalEl);

    function openModalFromProps(props, start, end) {
        document.getElementById('modal_batch').innerText = props.batch || 'General Batch';
        document.getElementById('modal_lesson').innerText = props.lessonCode + ' - ' + props.lessonName;
        document.getElementById('modal_instructor').innerText = props.instructorRank + ' ' + props.instructorName;
        document.getElementById('modal_service_no').innerText = props.instructorServiceNo ? 'Service No: ' + props.instructorServiceNo : '';
        document.getElementById('modal_lab').innerText = props.labCode + ' (' + props.labName + ')';
        document.getElementById('modal_camp').innerText = props.campName || 'Main Station';
        
        let dateStr = props.dateFormatted || '';
        let timeStr = props.timeFormatted || '';
        if (!dateStr && start) {
            const pad = n => (n < 10 ? '0' : '') + n;
            dateStr = start.getFullYear() + '-' + pad(start.getMonth() + 1) + '-' + pad(start.getDate());
            timeStr = pad(start.getHours()) + ':' + pad(start.getMinutes());
            if (end) timeStr += ' - ' + pad(end.getHours()) + ':' + pad(end.getMinutes());
        }

        document.getElementById('modal_date').innerText = dateStr;
        document.getElementById('modal_time').innerText = timeStr;
        document.getElementById('modal_ref').innerText = props.allocationId ? ('#AL-' + String(props.allocationId).padStart(5, '0')) : 'N/A';
        
        const statusEl = document.getElementById('modal_status');
        const status = props.sessionStatus || 'Scheduled';
        statusEl.innerText = status;
        statusEl.className = 'badge ' + (status === 'Completed' || status === 'Completed Successfully' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary');

        bsModal.show();
    }

    // 5. Schedule Confirmation Lookup Search
    function performRosterSearch() {
        const query = document.getElementById('confirm_search_input').value.trim();
        const date = document.getElementById('confirm_date_input').value;
        const grid = document.getElementById('confirm_results_grid');
        const countSpan = document.getElementById('results_count');

        grid.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="small text-muted mt-2">Verifying official timetable records...</p></div>';

        let url = '<?php echo URLROOT; ?>roster/search?q=' + encodeURIComponent(query);
        if (date) url += '&date=' + encodeURIComponent(date);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                countSpan.innerText = data.count || 0;
                grid.innerHTML = '';

                if (!data.results || data.results.length === 0) {
                    grid.innerHTML = `
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No matching allocation records verified.</h6>
                            <p class="small">Check your instructor service number, batch name, or date selection.</p>
                        </div>
                    `;
                    return;
                }

                data.results.forEach(item => {
                    const statusClass = item.session_status === 'Completed' || item.session_status === 'Completed Successfully' 
                        ? 'bg-success-subtle text-success' 
                        : (item.session_status === 'Cancelled' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary');

                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4';
                    col.innerHTML = `
                        <div class="confirmation-card h-100 p-3 d-flex flex-column justify-content-between">
                            <div class="card-stripe mb-3"></div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary-subtle text-secondary small fw-semibold">#${item.ref_no}</span>
                                    <span class="badge ${statusClass} small">${item.session_status}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-primary-subtle text-primary fs-7 fw-bold">
                                        <i class="bi bi-people-fill me-1"></i> ${item.batch}
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1" style="color:var(--text-h); font-size:0.95rem;">
                                    ${item.lesson_code} &mdash; ${item.lesson_name}
                                </h6>
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-person-badge-fill me-1 text-secondary"></i>
                                    <span class="fw-semibold text-dark">${item.instructor_rank} ${item.instructor_name}</span>
                                    ${item.instructor_service_no ? `<span class="badge bg-light text-muted border border-color ms-1">${item.instructor_service_no}</span>` : ''}
                                </div>
                                <div class="small text-muted mb-3">
                                    <i class="bi bi-door-closed me-1"></i> ${item.lab_code} (${item.lab_name})
                                    <div class="text-secondary small mt-1"><i class="bi bi-geo-alt me-1"></i> ${item.camp_name}</div>
                                </div>
                            </div>
                            <div class="pt-2 border-top border-color">
                                <div class="d-flex justify-content-between align-items-center mb-2 small">
                                    <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> ${item.date_formatted}</span>
                                    <span class="fw-bold text-primary"><i class="bi bi-clock me-1"></i> ${item.start_time} - ${item.end_time}</span>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-semibold btn-view-confirm" data-id="${item.id}">
                                    <i class="bi bi-card-checklist me-1"></i> View Schedule Confirmation Slip
                                </button>
                            </div>
                        </div>
                    `;
                    grid.appendChild(col);
                });
            })
            .catch(err => {
                grid.innerHTML = '<div class="col-12 text-center text-danger py-4">Error loading schedule confirmation data. Please try again.</div>';
            });
    }

    document.getElementById('btn_search_confirm').addEventListener('click', performRosterSearch);
    document.getElementById('confirm_search_input').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') performRosterSearch();
    });

    // 6. View Details Slip Button Event Delegation
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-view-confirm');
        if (!btn) return;

        const id = btn.getAttribute('data-id');
        if (!id) return;

        fetch('<?php echo URLROOT; ?>roster/detail/' + id)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                openModalFromProps({
                    allocationId: data.id,
                    batch: data.batch,
                    lessonCode: data.lesson_code,
                    lessonName: data.lesson_name,
                    instructorRank: data.instructor_rank,
                    instructorName: data.instructor_name,
                    instructorServiceNo: data.instructor_service_no,
                    labCode: data.lab_code,
                    labName: data.lab_name,
                    campName: data.camp_name,
                    dateFormatted: data.date_formatted,
                    timeFormatted: data.start_time + ' - ' + data.end_time,
                    sessionStatus: data.session_status
                });
            })
            .catch(err => alert('Failed to fetch allocation details.'));
    });
});
</script>

</body>
</html>
