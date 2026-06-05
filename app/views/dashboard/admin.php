<!-- Admin Dashboard — Coursera-Inspired UI -->

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1" style="font-family:var(--font-heading);color:var(--text-h);">
            <i class="bi bi-speedometer2 me-2" style="color:var(--primary);"></i>Administrator Dashboard
        </h4>
        <p class="mb-0" style="color:var(--text-muted);font-size:0.85rem;">
            <?php echo date('l, d F Y'); ?> &mdash; Overview of laboratory operations
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>allocation/schedule" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i>New Allocation
        </a>
        <a href="<?php echo URLROOT; ?>report" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-bar-graph me-1"></i>Reports
        </a>
    </div>
</div>

<!-- Global Search Bar Section -->
<div class="card-clms mb-4 position-relative">
    <div class="card-body p-3">
        <div class="input-group input-group-lg">
            <span class="input-group-text bg-transparent border-end-0 text-primary"><i class="bi bi-search fs-5"></i></span>
            <input type="text" id="globalSearchInput" class="form-control bg-transparent border-start-0 ps-0" style="box-shadow:none; font-size:1.05rem;" placeholder="Global Search: Type Service No, Instructor, Lab Name, Lesson, or Asset Number..." autocomplete="off">
            <button class="btn btn-primary px-4" type="button" id="globalSearchBtn">Search</button>
        </div>
        
        <!-- Live Autocomplete dropdown -->
        <div id="globalSearchResults" class="list-group shadow-lg position-absolute w-100 start-0 px-3 d-none" style="z-index: 1050; top: 100%; max-height: 400px; overflow-y: auto; background-color: var(--card-bg); border: 1px solid var(--card-border); border-radius: 0 0 8px 8px;">
            <!-- Category and item list will populate here -->
        </div>
    </div>
</div>

<!-- KPI Row 1 — Resources -->
<div class="row g-3 mb-3">
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>laboratory" class="text-decoration-none d-block" role="button" aria-label="Laboratories" tabindex="0">
        <div class="stats-card accent-blue" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Laboratories</p>
                <h2 class="stats-count"><?php echo $data['total_labs']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Registered labs</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-door-closed-fill"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>equipment/computers" class="text-decoration-none d-block" role="button" aria-label="Computers" tabindex="0">
        <div class="stats-card accent-blue" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Computers</p>
                <h2 class="stats-count"><?php echo $data['total_computers']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Inventory units</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-pc-display"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>equipment/smartboards" class="text-decoration-none d-block" role="button" aria-label="Smart Boards" tabindex="0">
        <div class="stats-card accent-blue" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Smart Boards</p>
                <h2 class="stats-count"><?php echo $data['total_smartboards']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Display units</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-easel-fill"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>instructor" class="text-decoration-none d-block" role="button" aria-label="Instructors" tabindex="0">
        <div class="stats-card accent-blue" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Instructors</p>
                <h2 class="stats-count"><?php echo $data['total_instructors']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Registered staff</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        </a>
    </div>
</div>

<!-- KPI Row 2 — Operational Status -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>allocation/calendar" class="text-decoration-none d-block">
        <div class="stats-card accent-green" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Active Today</p>
                <h2 class="stats-count" style="color:var(--success);"><?php echo $data['sessions_today']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Sessions running</small>
            </div>
            <div class="stats-icon-wrapper icon-green">
                <i class="bi bi-play-circle-fill"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>request" class="text-decoration-none">
            <div class="stats-card accent-yellow">
                <div>
                    <p class="stats-label mb-1">Change Requests</p>
                    <h2 class="stats-count" style="color:var(--warning);"><?php echo $data['pending_requests']; ?></h2>
                    <small style="color:var(--text-muted);font-size:0.75rem;">Pending review</small>
                </div>
                <div class="stats-icon-wrapper icon-yellow">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>fault" class="text-decoration-none">
            <div class="stats-card accent-red">
                <div>
                    <p class="stats-label mb-1">Fault Reports</p>
                    <h2 class="stats-count" style="color:var(--danger);"><?php echo $data['pending_faults']; ?></h2>
                    <small style="color:var(--text-muted);font-size:0.75rem;">Open tickets</small>
                </div>
                <div class="stats-icon-wrapper icon-red">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>maintenance" class="text-decoration-none">
            <div class="stats-card accent-yellow">
                <div>
                    <p class="stats-label mb-1">Maintenance</p>
                    <h2 class="stats-count" style="color:var(--warning);"><?php echo $data['pending_maintenance']; ?></h2>
                    <small style="color:var(--text-muted);font-size:0.75rem;">Pending tasks</small>
                </div>
                <div class="stats-icon-wrapper icon-yellow">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
    </div>
</div>

<!-- KPI Row 3 — Session Completion Metrics Today -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>inquiry/sessionCompletionRecords?status=Scheduled&date_from=<?php echo date('Y-m-d'); ?>&date_to=<?php echo date('Y-m-d'); ?>" class="text-decoration-none d-block">
        <div class="stats-card accent-blue" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Scheduled Today</p>
                <h2 class="stats-count" style="color:var(--primary);"><?php echo $data['stats_today']['scheduled_today']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Awaiting completion log</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>inquiry/sessionCompletionRecords?status=Completed+Successfully&date_from=<?php echo date('Y-m-d'); ?>&date_to=<?php echo date('Y-m-d'); ?>" class="text-decoration-none d-block">
        <div class="stats-card accent-green" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Completed Today</p>
                <h2 class="stats-count" style="color:var(--success);"><?php echo $data['stats_today']['completed_today']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Successfully executed</small>
            </div>
            <div class="stats-icon-wrapper icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?php echo URLROOT; ?>inquiry/sessionCompletionRecords?status=Cancelled" class="text-decoration-none d-block">
        <div class="stats-card accent-red" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Cancelled Sessions</p>
                <h2 class="stats-count" style="color:var(--danger);"><?php echo $data['stats_today']['cancelled_total']; ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Total cancelled overall</small>
            </div>
            <div class="stats-icon-wrapper icon-red">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stats-card accent-blue">
            <div>
                <p class="stats-label mb-1">Completion Percentage</p>
                <h2 class="stats-count" style="color:var(--primary);"><?php echo $data['stats_today']['completion_percentage']; ?>%</h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">For today's scheduled runs</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-percent"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card-clms mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="width:32px;height:32px;background:var(--primary-light);border-radius:8px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-lightning-charge-fill" style="color:var(--primary);font-size:0.9rem;"></i>
        </div>
        <h6 class="mb-0 fw-700" style="font-family:var(--font-heading);color:var(--text-h);">Quick Actions</h6>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>allocation/schedule" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i>New Lab Allocation
        </a>
        <a href="<?php echo URLROOT; ?>instructor" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-plus-fill me-1"></i>Register Instructor
        </a>
        <a href="<?php echo URLROOT; ?>laboratory" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-door-closed me-1"></i>Configure Labs
        </a>
        <a href="<?php echo URLROOT; ?>notice" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-card-heading me-1"></i>Publish Notice
        </a>
        <a href="<?php echo URLROOT; ?>report" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-bar-graph me-1"></i>Generate Report
        </a>
        <a href="<?php echo URLROOT; ?>allocation/calendar" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar3 me-1"></i>View Calendar
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
                <a href="<?php echo URLROOT; ?>allocation/calendar" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-calendar3 me-1"></i>Full Calendar
                </a>
            </div>
            <div class="table-responsive">
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
                                        <span class="badge-active" style="padding:3px 10px;border-radius:99px;font-size:0.75rem;font-weight:600;background:var(--primary-light);color:var(--primary);">
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
            <div class="d-flex flex-column gap-3 mt-1">
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
            <div style="max-height:360px;overflow-y:auto;">
                <?php if(!empty($data['recent_notices'])): ?>
                    <?php foreach($data['recent_notices'] as $n): ?>
                        <div class="notice-item">
                            <h6 class="fw-700 mb-1" style="font-size:0.88rem;color:var(--text-h);"><?php echo e($n->title); ?></h6>
                            <p class="mb-2" style="font-size:0.82rem;color:var(--text-muted);"><?php echo e($n->content); ?></p>
                            <div class="notice-meta">
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
                <a href="<?php echo URLROOT; ?>audit" class="btn btn-outline-secondary btn-sm">All Logs</a>
            </div>
            <div style="max-height:360px;overflow-y:auto;">
                <?php if(!empty($data['recent_logs'])): ?>
                    <?php foreach($data['recent_logs'] as $log): ?>
                        <?php
                        $dotColor = 'var(--info)';
                        if (strpos($log->action,'DELETE')!==false || strpos($log->action,'LOCK')!==false) $dotColor='var(--danger)';
                        elseif (strpos($log->action,'UPDATE')!==false || strpos($log->action,'REJECT')!==false) $dotColor='var(--warning)';
                        ?>
                        <div class="d-flex gap-3 mb-3 pb-3" style="border-bottom:1px solid var(--border);">
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

<script>
document.addEventListener("DOMContentLoaded", function() {
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
});
</script>

