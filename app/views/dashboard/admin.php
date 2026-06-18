<!-- Admin Dashboard — Enhanced SLAF Lab Management UI -->

<!-- Custom styles for FullCalendar and dashboard tiles -->
<style>
    .fc {
        --fc-border-color: var(--card-border);
        --fc-daygrid-event-dot-color: var(--primary);
        --fc-today-bg-color: rgba(69, 123, 157, 0.1);
        color: var(--text-body);
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: var(--card-border);
    }
    .fc .fc-toolbar-title {
        font-family: var(--font-heading);
        font-weight: 700;
        color: var(--text-h);
        font-size: 1.25rem;
    }
    .fc .fc-button-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .fc .fc-button-primary:hover, .fc .fc-button-primary:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: none;
    }
    .fc .fc-button-primary:disabled {
        background-color: var(--card-border);
        border-color: var(--card-border);
    }
    .fc .fc-button-active {
        background-color: var(--primary-dark) !important;
        border-color: var(--primary-dark) !important;
    }
    .fc-daygrid-event {
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.78rem;
        font-weight: 600;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
    }
    /* Hover effects for KPI tiles */
    .kpi-tile {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    }
</style>

<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="mb-1" style="font-family:var(--font-heading);color:var(--text-h);">
            <i class="bi bi-speedometer2 me-2" style="color:var(--primary);"></i>SLAF Laboratory Command Dashboard
        </h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:0.85rem;">
            <?php echo date('l, d F Y'); ?> &mdash; Operations Control Center
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Camp Filter / Badge -->
        <?php if (isSuperAdmin()): ?>
            <form action="" method="GET" class="d-flex align-items-center gap-2 me-2">
                <label for="campFilter" class="form-label mb-0 text-nowrap small fw-bold text-muted"><i class="bi bi-geo-alt-fill text-primary me-1"></i>Camp Scope:</label>
                <select name="camp_id" id="campFilter" class="form-select form-select-sm form-control-clms" style="min-width: 180px;" onchange="this.form.submit()">
                    <option value="">All SLAF Camps (Global)</option>
                    <?php foreach ($data['camps'] as $camp): ?>
                        <option value="<?php echo $camp->id; ?>" <?php echo ($data['current_camp_id'] == $camp->id) ? 'selected' : ''; ?>>
                            <?php echo e($camp->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php elseif (isCampAdmin()): ?>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-7 me-2">
                <i class="bi bi-geo-alt-fill me-1"></i><?php echo e($_SESSION['camp_name'] ?? 'Assigned Camp'); ?>
            </span>
        <?php endif; ?>

        <a href="<?php echo URLROOT; ?>allocation/schedule" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i>New Allocation
        </a>
    </div>
</div>

<!-- Global Search Bar Section -->
<div class="card-clms mb-4 position-relative">
    <div class="card-body p-3">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-primary"><i class="bi bi-search fs-6"></i></span>
            <input type="text" id="globalSearchInput" class="form-control bg-transparent border-start-0 ps-0 form-control-clms" style="box-shadow:none;" placeholder="Global Search: Type Service No, Instructor, Lab Name, Lesson, or Asset Number..." autocomplete="off">
        </div>
        
        <!-- Live Autocomplete dropdown -->
        <div id="globalSearchResults" class="list-group shadow-lg position-absolute w-100 start-0 px-3 d-none" style="z-index: 1050; top: 100%; max-height: 400px; overflow-y: auto; background-color: var(--card-bg); border: 1px solid var(--card-border); border-radius: 0 0 8px 8px;">
            <!-- Category and item list will populate here -->
        </div>
    </div>
</div>

<!-- Interactive Calendar Row (Top Center) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card-clms p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 pb-3 border-bottom border-color gap-2">
                <div>
                    <h6 class="fw-bold mb-1"><i class="bi bi-calendar3 text-primary me-2"></i>Operational Laboratory Schedule</h6>
                    <p class="text-muted small mb-0">Monthly overview of lab allocations, bookings, maintenance, and training programs.</p>
                </div>
                <!-- Color legends -->
                <div class="d-flex flex-wrap gap-3 mt-1">
                    <span class="small fw-semibold d-flex align-items-center"><span class="legend-dot" style="background:#1d3557;"></span>Sessions</span>
                    <span class="small fw-semibold d-flex align-items-center"><span class="legend-dot" style="background:#2a9d8f;"></span>Completed Bookings</span>
                    <span class="small fw-semibold d-flex align-items-center"><span class="legend-dot" style="background:#7209b7;"></span>Special Events</span>
                    <span class="small fw-semibold d-flex align-items-center"><span class="legend-dot" style="background:#f4a261;"></span>Maintenance</span>
                    <span class="small fw-semibold d-flex align-items-center"><span class="legend-dot" style="background:#e63946;"></span>Cancelled</span>
                </div>
            </div>
            <!-- FullCalendar Container -->
            <div id="calendar" style="height: 450px;"></div>
        </div>
    </div>
</div>

<!-- Dashboard Summary Statistics Tiles (Below Calendar) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-3 mb-4">
    <!-- Total Labs -->
    <div class="col">
        <div class="stats-card kpi-tile accent-blue h-100">
            <div>
                <p class="stats-label mb-1">Total Laboratories</p>
                <h3 class="stats-count m-0"><?php echo $data['total_labs']; ?></h3>
                <small style="color:var(--text-muted);font-size:0.75rem;">Registered rooms</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-door-closed-fill"></i>
            </div>
        </div>
    </div>
    
    <!-- Active Labs -->
    <div class="col">
        <div class="stats-card kpi-tile accent-green h-100">
            <div>
                <p class="stats-label mb-1">Active Labs</p>
                <h3 class="stats-count m-0 text-success"><?php echo $data['active_labs'] ?? $data['total_labs']; ?></h3>
                <small style="color:var(--text-muted);font-size:0.75rem;">In active status</small>
            </div>
            <div class="stats-icon-wrapper icon-green">
                <i class="bi bi-door-open-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Scheduled Today -->
    <div class="col">
        <div class="stats-card kpi-tile accent-blue h-100">
            <div>
                <p class="stats-label mb-1">Scheduled Today</p>
                <h3 class="stats-count m-0 text-primary"><?php echo $data['stats_today']['scheduled_today']; ?></h3>
                <small style="color:var(--text-muted);font-size:0.75rem;">Awaiting execution</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Completed Today -->
    <div class="col">
        <div class="stats-card kpi-tile accent-green h-100">
            <div>
                <p class="stats-label mb-1">Completed Today</p>
                <h3 class="stats-count m-0 text-success"><?php echo $data['stats_today']['completed_today']; ?></h3>
                <small style="color:var(--text-muted);font-size:0.75rem;">Logged successfully</small>
            </div>
            <div class="stats-icon-wrapper icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Cancelled Sessions -->
    <div class="col">
        <div class="stats-card kpi-tile accent-red h-100">
            <div>
                <p class="stats-label mb-1">Cancelled Today</p>
                <h3 class="stats-count m-0 text-danger"><?php echo $data['stats_today']['cancelled_total']; ?></h3>
                <small style="color:var(--text-muted);font-size:0.75rem;">Total cancelled overall</small>
            </div>
            <div class="stats-icon-wrapper icon-red">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>
    </div>

    <!-- Session Completion Percentage -->
    <div class="col">
        <div class="stats-card kpi-tile accent-blue h-100">
            <div>
                <p class="stats-label mb-1">Completion Rate</p>
                <h3 class="stats-count m-0" style="color:var(--primary);"><?php echo $data['stats_today']['completion_percentage']; ?>%</h3>
                <small style="color:var(--text-muted);font-size:0.75rem;">Today's target metrics</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-percent"></i>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Metrics Card Row (Faults, Maintenance, Requests) -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="<?php echo URLROOT; ?>request" class="text-decoration-none">
            <div class="stats-card kpi-tile accent-yellow">
                <div>
                    <p class="stats-label mb-1">Pending Change Requests</p>
                    <h4 class="stats-count m-0 text-warning"><?php echo $data['pending_requests']; ?></h4>
                    <small style="color:var(--text-muted);font-size:0.75rem;">Allocations awaiting approval</small>
                </div>
                <div class="stats-icon-wrapper icon-yellow">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-4">
        <a href="<?php echo URLROOT; ?>fault" class="text-decoration-none">
            <div class="stats-card kpi-tile accent-red">
                <div>
                    <p class="stats-label mb-1">Active Fault Reports</p>
                    <h4 class="stats-count m-0 text-danger"><?php echo $data['pending_faults']; ?></h4>
                    <small style="color:var(--text-muted);font-size:0.75rem;">Open hardware/network tickets</small>
                </div>
                <div class="stats-icon-wrapper icon-red">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo URLROOT; ?>maintenance" class="text-decoration-none">
            <div class="stats-card kpi-tile accent-yellow">
                <div>
                    <p class="stats-label mb-1">Scheduled Maintenance Tasks</p>
                    <h4 class="stats-count m-0 text-warning"><?php echo $data['pending_maintenance']; ?></h4>
                    <small style="color:var(--text-muted);font-size:0.75rem;">Preventative repairs in queue</small>
                </div>
                <div class="stats-icon-wrapper icon-yellow">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="card-clms mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="width:32px;height:32px;background:var(--primary-light);border-radius:8px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-lightning-charge-fill" style="color:var(--primary);font-size:0.9rem;"></i>
        </div>
        <h6 class="mb-0 fw-700" style="font-family:var(--font-heading);color:var(--text-h);">Quick Actions Panel</h6>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>allocation/schedule" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i>New Lab Allocation
        </a>
        <?php if (isSuperAdmin()): ?>
            <a href="<?php echo URLROOT; ?>instructor" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-person-plus-fill me-1"></i>Register Instructor
            </a>
            <a href="<?php echo URLROOT; ?>laboratory" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-door-closed me-1"></i>Configure Labs
            </a>
            <a href="<?php echo URLROOT; ?>camp" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-geo-alt-fill me-1"></i>Manage SLAF Camps
            </a>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>notice" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-card-heading me-1"></i>Publish Notice
        </a>
        <a href="<?php echo URLROOT; ?>report" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-bar-graph me-1"></i>Generate Report
        </a>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4 mb-4">
    <!-- Upcoming Sessions Table -->
    <div class="col-lg-8">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-range-fill" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Active &amp; Upcoming Allocations</h6>
                </div>
            </div>
            <div class="table-responsive mt-2">
                <table class="table table-hover table-clms align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Instructor</th>
                            <th>Lab</th>
                            <th>Lesson</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['upcoming_sessions'])): ?>
                            <?php foreach($data['upcoming_sessions'] as $s): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:30px;height:30px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="bi bi-person-fill" style="color:var(--primary);font-size:0.8rem;"></i>
                                            </div>
                                            <span class="fw-600" style="font-size:0.88rem;"><?php echo e($s->instructor_rank) . ' ' . e($s->instructor_name); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-active text-nowrap" style="padding:3px 10px;border-radius:99px;font-size:0.75rem;font-weight:600;background:var(--primary-light);color:var(--primary);">
                                            <?php echo e($s->lab_code); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width:180px;font-size:0.88rem;" title="<?php echo e($s->lesson_name); ?>">
                                            <?php echo e($s->lesson_name); ?>
                                        </div>
                                    </td>
                                    <td style="font-size:0.88rem;color:var(--text-muted);"><?php echo date('d M Y', strtotime($s->date)); ?></td>
                                    <td>
                                        <span class="fw-600" style="font-size:0.82rem;color:var(--primary);">
                                            <?php echo date('H:i', strtotime($s->start_time)) . ' – ' . date('H:i', strtotime($s->end_time)); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-calendar-x d-block mb-2" style="font-size:2rem;color:var(--text-muted);"></i>
                                    <span style="color:var(--text-muted);font-size:0.9rem;">No sessions scheduled.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lab Utilization -->
    <div class="col-lg-4">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Lab Utilization</h6>
                </div>
                <span style="font-size:0.75rem;color:var(--text-muted);">This month</span>
            </div>
            <div class="d-flex flex-column gap-3 mt-3">
                <?php if(!empty($data['utilization'])): ?>
                    <?php foreach($data['utilization'] as $u): ?>
                        <?php $pct = min(($u->total_hours / 40) * 100, 100); ?>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:0.82rem;font-weight:600;color:var(--text-h);">
                                    <?php echo e($u->lab_code) . ' — ' . e($u->lab_name); ?>
                                </span>
                                <span style="font-size:0.78rem;color:var(--text-muted);font-weight:600;">
                                    <?php echo round($u->total_hours, 1); ?>h
                                </span>
                            </div>
                            <div class="progress" style="height:7px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width:<?php echo $pct; ?>%;background:<?php echo $pct > 75 ? 'var(--success)' : ($pct > 40 ? 'var(--primary)' : 'var(--warning)'); ?>;"
                                    aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-bar-chart d-block mb-2" style="font-size:2rem;color:var(--text-muted);"></i>
                        <span style="color:var(--text-muted);font-size:0.9rem;">No utilization data.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row — Notices + Audit Logs -->
<div class="row g-4">
    <!-- Notice Board -->
    <div class="col-md-6">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone-fill" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Notice Board</h6>
                </div>
                <a href="<?php echo URLROOT; ?>notice" class="btn btn-outline-secondary btn-sm">Manage</a>
            </div>
            <div class="mt-2" style="max-height:360px;overflow-y:auto;">
                <?php if(!empty($data['recent_notices'])): ?>
                    <?php foreach($data['recent_notices'] as $n): ?>
                        <div class="notice-item p-2 mb-2 rounded border border-color">
                            <h6 class="fw-700 mb-1" style="font-size:0.88rem;color:var(--text-h);"><?php echo e($n->title); ?></h6>
                            <p class="mb-2" style="font-size:0.82rem;color:var(--text-muted);"><?php echo e($n->content); ?></p>
                            <div class="notice-meta small text-muted">
                                <i class="bi bi-person me-1"></i><?php echo e($n->publisher_name); ?>
                                &nbsp;&middot;&nbsp;
                                <i class="bi bi-calendar me-1"></i><?php echo date('d M Y', strtotime($n->created_at)); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-megaphone d-block mb-2" style="font-size:2rem;color:var(--text-muted);"></i>
                        <span style="color:var(--text-muted);font-size:0.9rem;">No announcements published.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Audit Log Feed -->
    <div class="col-md-6">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Security Audit Trail</h6>
                </div>
                <?php if (isSuperAdmin()): ?>
                    <a href="<?php echo URLROOT; ?>audit" class="btn btn-outline-secondary btn-sm">All Logs</a>
                <?php endif; ?>
            </div>
            <div class="mt-2" style="max-height:360px;overflow-y:auto;">
                <?php if(!empty($data['recent_logs'])): ?>
                    <?php foreach($data['recent_logs'] as $log): ?>
                        <?php
                        $dotColor = 'var(--info)';
                        if (strpos($log->action,'DELETE')!==false || strpos($log->action,'LOCK')!==false) $dotColor='var(--danger)';
                        elseif (strpos($log->action,'UPDATE')!==false || strpos($log->action,'REJECT')!==false) $dotColor='var(--warning)';
                        ?>
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-color">
                            <div style="flex-shrink:0;margin-top:3px;">
                                <div style="width:10px;height:10px;border-radius:50%;background:<?php echo $dotColor; ?>;box-shadow:0 0 0 3px color-mix(in srgb,<?php echo $dotColor; ?> 20%,transparent);"></div>
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size:0.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:0.5px;"><?php echo e($log->action); ?></span>
                                    <span style="font-size:0.7rem;color:var(--text-muted);flex-shrink:0;"><?php echo date('d M H:i', strtotime($log->created_at)); ?></span>
                                </div>
                                <p class="mb-1 text-truncate" style="font-size:0.82rem;color:var(--text-body);max-width:100%;" title="<?php echo e($log->details); ?>"><?php echo e($log->details); ?></p>
                                <span style="font-size:0.72rem;color:var(--text-muted);">
                                    <i class="bi bi-person-fill me-1"></i><?php echo e($log->username ?: 'System'); ?>
                                    &nbsp;&middot;&nbsp; <?php echo e($log->ip_address); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-shield-slash d-block mb-2" style="font-size:2rem;color:var(--text-muted);"></i>
                        <span style="color:var(--text-muted);font-size:0.9rem;">No recent security events.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- DASHBOARD EVENT DETAILS MODAL -->
<div class="modal fade" id="dashboardEventModal" tabindex="-1" aria-labelledby="dashboardEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="modal_title"><i class="bi bi-info-circle-fill text-primary me-2"></i>Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Session/Allocation Details -->
                <div id="session_detail_fields">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Laboratory</label>
                        <div class="fw-semibold text-primary fs-6" id="det_lab"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Course Lesson</label>
                        <div class="fw-bold text-light-emphasis" id="det_lesson"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Assigned Instructor</label>
                        <div class="fw-semibold" id="det_instructor"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Scheduled Date</label>
                            <div class="fw-semibold" id="det_date"></div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Time Block</label>
                            <div class="fw-semibold text-warning" id="det_time"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Session Status</label>
                        <div>
                            <span class="badge" id="det_status"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Remarks / Notes</label>
                        <p class="p-2 bg-light-subtle rounded border border-color small" id="det_remarks"></p>
                    </div>
                </div>
                
                <!-- Maintenance Details -->
                <div id="maintenance_detail_fields" class="d-none">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Equipment / Asset</label>
                        <div class="fw-semibold text-primary fs-6" id="maint_equip"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Issue Type</label>
                        <div class="fw-bold text-light-emphasis" id="maint_issue"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Assigned Technician</label>
                        <div class="fw-semibold" id="maint_tech"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <div>
                            <span class="badge bg-warning text-dark" id="maint_status"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Technician Notes</label>
                        <p class="p-2 bg-light-subtle rounded border border-color small" id="maint_notes"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-color">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Autocomplete & Calendar Script Initialization -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Global Search Bar logic
    const searchInput = document.getElementById("globalSearchInput");
    const searchResults = document.getElementById("globalSearchResults");
    let debounceTimer;

    searchInput.addEventListener("input", function() {
        clearTimeout(debounceTimer);
        const query = searchInput.value.trim();
        
        if (query.length < 2) {
            searchResults.classList.add("d-none");
            searchResults.innerHTML = "";
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch('<?php echo URLROOT; ?>inquiry/globalSearch?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = "";
                    let hasResults = false;

                    const categories = {
                        instructors: { title: "Instructors", icon: "bi-people-fill text-primary" },
                        labs: { title: "Laboratories", icon: "bi-door-closed text-info" },
                        lessons: { title: "Lessons", icon: "bi-book text-success" },
                        computers: { title: "Computers / Assets", icon: "bi-cpu text-warning" }
                    };

                    for (const [key, cat] of Object.entries(categories)) {
                        if (data[key] && data[key].length > 0) {
                            hasResults = true;
                            
                            // Category Header
                            const header = document.createElement("div");
                            header.className = "list-group-item bg-light text-muted small fw-bold px-2 py-1 border-0 mt-2";
                            header.innerHTML = `<i class="${cat.icon} me-1"></i> ${cat.title}`;
                            searchResults.appendChild(header);

                            // Category Items
                            data[key].forEach(item => {
                                const a = document.createElement("a");
                                a.href = `<?php echo URLROOT; ?>${item.link_base}`;
                                a.className = "list-group-item list-group-item-action border-0 px-3 py-2 d-flex justify-content-between align-items-center";
                                a.innerHTML = `
                                    <div>
                                        <span class="fw-semibold d-block text-dark">${item.label}</span>
                                        <small class="text-muted">${item.sub}</small>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-secondary small">${item.module}</span>
                                `;
                                searchResults.appendChild(a);
                            });
                        }
                    }

                    if (hasResults) {
                        searchResults.classList.remove("d-none");
                    } else {
                        searchResults.innerHTML = '<div class="list-group-item text-center py-3 text-muted small">No matches found.</div>';
                        searchResults.classList.remove("d-none");
                    }
                });
        }, 250);
    });

    // Close search dropdown on click outside
    document.addEventListener("click", function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add("d-none");
        }
    });

    // 2. FullCalendar Dashboard Setup
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 450,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: false,
        droppable: false,
        events: '<?php echo URLROOT; ?>allocation/getCalendarEvents<?php echo $data["current_camp_id"] ? "?camp_id=" . $data["current_camp_id"] : ""; ?>',
        timeZone: 'local',
        
        eventClick: function(info) {
            var props = info.event.extendedProps;
            
            if (props.isMaintenance) {
                // Show maintenance details
                document.getElementById('modal_title').innerHTML = "<i class='bi bi-wrench-adjustable text-warning me-2'></i>Maintenance Schedule Details";
                document.getElementById('session_detail_fields').classList.add('d-none');
                document.getElementById('maintenance_detail_fields').classList.remove('d-none');
                
                document.getElementById('maint_equip').innerText = props.equipmentIdentifier + ' (' + props.equipmentType + ')';
                document.getElementById('maint_issue').innerText = props.issueType;
                document.getElementById('maint_tech').innerText = props.assignedTechnician;
                document.getElementById('maint_status').innerText = props.status;
                document.getElementById('maint_notes').innerText = props.remarks;
            } else {
                // Show allocation session details
                document.getElementById('modal_title').innerHTML = "<i class='bi bi-calendar-event text-primary me-2'></i>Lab Allocation Details";
                document.getElementById('maintenance_detail_fields').classList.add('d-none');
                document.getElementById('session_detail_fields').classList.remove('d-none');
                
                document.getElementById('det_lab').innerText = props.labCode + ' - ' + props.labName;
                document.getElementById('det_lesson').innerText = props.lessonCode + ' - ' + props.lessonName;
                document.getElementById('det_instructor').innerText = props.instructorName;
                
                var start = info.event.start;
                var end = info.event.end;
                var pad = function(n) { return (n < 10 ? '0' : '') + n; };
                var dateStr = start.getFullYear() + '-' + pad(start.getMonth() + 1) + '-' + pad(start.getDate());
                var timeStr = pad(start.getHours()) + ':' + pad(start.getMinutes());
                if (end) {
                    timeStr += ' - ' + pad(end.getHours()) + ':' + pad(end.getMinutes());
                }
                
                document.getElementById('det_date').innerText = dateStr;
                document.getElementById('det_time').innerText = timeStr;
                
                var statusEl = document.getElementById('det_status');
                var status = props.sessionStatus || 'Scheduled';
                statusEl.innerText = status;
                statusEl.className = 'badge';
                if (status === 'Completed Successfully' || status === 'Completed') {
                    statusEl.classList.add('bg-success-subtle', 'text-success');
                } else if (status === 'Partially Completed') {
                    statusEl.classList.add('bg-warning-subtle', 'text-warning-emphasis');
                } else if (status === 'Cancelled') {
                    statusEl.classList.add('bg-danger-subtle', 'text-danger');
                } else {
                    statusEl.classList.add('bg-primary-subtle', 'text-primary');
                }
                
                document.getElementById('det_remarks').innerText = props.remarks;
            }
            
            var modal = new bootstrap.Modal(document.getElementById('dashboardEventModal'));
            modal.show();
        }
    });
    
    calendar.render();
});
</script>
