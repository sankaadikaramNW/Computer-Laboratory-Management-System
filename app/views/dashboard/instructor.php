<!-- Instructor Dashboard — Coursera-Inspired UI -->

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <?php if(!empty($_SESSION['instructor_photo'])): 
            $thumb = str_replace('.webp', '_thumb.webp', $_SESSION['instructor_photo']);
        ?>
            <img src="<?php echo URLROOT; ?>uploads/instructors/<?php echo $thumb; ?>" class="rounded-circle border border-primary shadow-sm" style="width:50px; height:50px; object-fit:cover;">
        <?php else: ?>
            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center shadow-sm" style="width:50px; height:50px; color:var(--primary);">
                <i class="bi bi-person-workspace fs-4"></i>
            </div>
        <?php endif; ?>
        <div>
            <h4 class="mb-1" style="font-family:var(--font-heading);color:var(--text-h);">
                Welcome, <?php echo isset($_SESSION['instructor_rank']) ? e($_SESSION['instructor_rank']) . ' ' . e($_SESSION['instructor_name']) : e($_SESSION['username']); ?>
            </h4>
            <p class="mb-0" style="color:var(--text-muted);font-size:0.85rem;">
                <?php echo date('l, d F Y'); ?> &mdash; Your lab schedule &amp; requests
            </p>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo URLROOT; ?>allocation/calendar" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar3 me-1"></i>View Calendar
        </a>
        <a href="<?php echo URLROOT; ?>request/instructor" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left-right me-1"></i>Change Request
        </a>
        <a href="<?php echo URLROOT; ?>fault/instructor" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-exclamation-triangle me-1"></i>Report Fault
        </a>
    </div>
</div>

<!-- KPI Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="stats-card accent-green">
            <div>
                <p class="stats-label mb-1">Today's Sessions</p>
                <h2 class="stats-count" style="color:var(--success);"><?php echo count($data['today_sessions']); ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Allocated for today</small>
            </div>
            <div class="stats-icon-wrapper icon-green">
                <i class="bi bi-calendar2-check-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <a href="<?php echo URLROOT; ?>allocation/mySchedule" class="text-decoration-none d-block">
        <div class="stats-card <?php echo count($data['pending_completion']) > 0 ? 'accent-yellow' : 'accent-blue'; ?>" style="cursor:pointer;">
            <div>
                <p class="stats-label mb-1">Pending Completion</p>
                <h2 class="stats-count" style="color:<?php echo count($data['pending_completion']) > 0 ? 'var(--warning)' : 'var(--primary)'; ?>;"><?php echo count($data['pending_completion']); ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Requires completion log</small>
            </div>
            <div class="stats-icon-wrapper <?php echo count($data['pending_completion']) > 0 ? 'icon-yellow' : 'icon-blue'; ?>">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="stats-card accent-blue">
            <div>
                <p class="stats-label mb-1">Upcoming Sessions</p>
                <h2 class="stats-count"><?php echo count($data['upcoming_sessions']); ?></h2>
                <small style="color:var(--text-muted);font-size:0.75rem;">Scheduled ahead</small>
            </div>
            <div class="stats-icon-wrapper icon-blue">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Grid -->
<div class="row g-4 mb-4">
    <!-- Today's Schedule -->
    <div class="col-lg-8">
        <?php if(!empty($data['pending_completion'])): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4" role="alert" style="border-radius: 8px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-hourglass-split fs-4 me-3 text-warning"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.95rem; color: #856404;">Action Required: Pending Completion Logs</strong>
                        <span style="font-size: 0.82rem; color: #856404;">You have <strong><?php echo count($data['pending_completion']); ?></strong> session(s) that require completion logs.</span>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>allocation/mySchedule" class="btn btn-warning btn-sm text-dark fw-bold px-3">View List</a>
            </div>
        <?php endif; ?>

        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check-fill" style="color:var(--success);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Today's Allocations</h6>
                </div>
                <span style="font-size:0.78rem;background:var(--primary-light);color:var(--primary);padding:3px 10px;border-radius:99px;font-weight:600;">
                    <?php echo date('d M Y'); ?>
                </span>
            </div>
            <?php if(!empty($data['today_sessions'])): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach($data['today_sessions'] as $s): ?>
                        <div style="padding:14px 16px;border-radius:8px;background:var(--bg-page);border:1px solid var(--border);position:relative;overflow:hidden;">
                            <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--primary);border-radius:0;"></div>
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2" style="padding-left:8px;">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span style="font-size:0.75rem;font-weight:700;background:var(--primary-light);color:var(--primary);padding:2px 8px;border-radius:99px;">
                                            <?php echo e($s->lab_code); ?> — <?php echo e($s->lab_name); ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-600" style="color:var(--text-h);font-size:0.9rem;"><?php echo e($s->lesson_code); ?> — <?php echo e($s->lesson_name); ?></p>
                                    <p class="mb-0" style="color:var(--text-muted);font-size:0.8rem;">
                                        <i class="bi bi-chat-left-text me-1"></i><?php echo e($s->remarks ?: 'No remarks'); ?>
                                    </p>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <span style="font-size:0.88rem;font-weight:700;color:var(--primary);">
                                        <i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($s->start_time)) . ' – ' . date('H:i', strtotime($s->end_time)); ?>
                                    </span>
                                    <a href="<?php echo URLROOT; ?>request/instructor?new_req=1&alloc_id=<?php echo $s->id; ?>"
                                        class="btn btn-sm btn-outline-secondary" style="font-size:0.75rem;padding:3px 10px;">
                                        <i class="bi bi-arrow-repeat me-1"></i>Reschedule
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar2-x d-block mb-2" style="font-size:2.5rem;color:var(--text-muted);"></i>
                    <p style="color:var(--text-muted);font-size:0.9rem;">No sessions scheduled for today.</p>
                    <a href="<?php echo URLROOT; ?>allocation/calendar" class="btn btn-outline-primary btn-sm mt-1">
                        <i class="bi bi-calendar3 me-1"></i>View Calendar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notice Board -->
    <div class="col-lg-4">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone-fill" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Notices</h6>
                </div>
            </div>
            <div style="max-height:340px;overflow-y:auto;">
                <?php if(!empty($data['active_notices'])): ?>
                    <?php foreach($data['active_notices'] as $n): ?>
                        <div class="notice-item">
                            <h6 class="fw-700 mb-1" style="font-size:0.88rem;color:var(--text-h);"><?php echo e($n->title); ?></h6>
                            <p class="mb-1" style="font-size:0.82rem;color:var(--text-muted);"><?php echo e($n->content); ?></p>
                            <div class="notice-meta">
                                <i class="bi bi-person me-1"></i><?php echo e($n->publisher_name); ?>
                                &nbsp;&middot;&nbsp;
                                <i class="bi bi-calendar me-1"></i><?php echo date('d M Y', strtotime($n->created_at)); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-megaphone d-block mb-2" style="font-size:2rem;color:var(--text-muted);"></i>
                        <span style="color:var(--text-muted);font-size:0.88rem;">No announcements.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Sessions + Ticket Status -->
<div class="row g-4">
    <!-- Upcoming Sessions -->
    <div class="col-md-6">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-event" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">Upcoming Allocations</h6>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-clms align-middle mb-0">
                    <thead>
                        <tr>
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
                                        <span style="font-size:0.75rem;font-weight:700;background:var(--primary-light);color:var(--primary);padding:2px 8px;border-radius:99px;">
                                            <?php echo e($s->lab_code); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width:130px;font-size:0.85rem;" title="<?php echo e($s->lesson_name); ?>">
                                            <?php echo e($s->lesson_name); ?>
                                        </div>
                                    </td>
                                    <td style="font-size:0.82rem;color:var(--text-muted);"><?php echo date('d M Y', strtotime($s->date)); ?></td>
                                    <td style="font-size:0.8rem;font-weight:600;color:var(--primary);">
                                        <?php echo date('H:i', strtotime($s->start_time)) . '–' . date('H:i', strtotime($s->end_time)); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color:var(--text-muted);">No upcoming sessions.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- My Requests & Faults -->
    <div class="col-md-6">
        <div class="card-clms h-100">
            <div class="card-clms-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-check-fill" style="color:var(--primary);font-size:1.1rem;"></i>
                    <h6 class="mb-0" style="font-family:var(--font-heading);">My Requests &amp; Tickets</h6>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-pills mb-3 gap-1" id="ticketTabs" role="tablist" style="background:var(--bg-page);padding:5px;border-radius:8px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-1 px-3" id="req-tab" data-bs-toggle="tab" data-bs-target="#req-pane" type="button" style="font-size:0.82rem;border-radius:6px;">
                        <i class="bi bi-arrow-left-right me-1"></i>Change Requests
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1 px-3" id="fault-tab" data-bs-toggle="tab" data-bs-target="#fault-pane" type="button" style="font-size:0.82rem;border-radius:6px;">
                        <i class="bi bi-exclamation-triangle me-1"></i>Fault Reports
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1 px-3" id="comp-tab" data-bs-toggle="tab" data-bs-target="#comp-pane" type="button" style="font-size:0.82rem;border-radius:6px;">
                        <i class="bi bi-check-circle me-1"></i>Completed Sessions
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="ticketTabsContent">
                <!-- Change Requests -->
                <div class="tab-pane fade show active" id="req-pane" role="tabpanel" tabindex="0">
                    <div style="max-height:260px;overflow-y:auto;">
                        <?php if(!empty($data['my_requests'])): ?>
                            <?php foreach($data['my_requests'] as $r): ?>
                                <?php
                                $badge = 'badge-pending';
                                if ($r->status==='approved') $badge='badge-approved';
                                if ($r->status==='rejected') $badge='badge-rejected';
                                ?>
                                <div class="mb-3 pb-3" style="border-bottom:1px solid var(--border);">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span style="font-size:0.78rem;font-weight:700;color:var(--primary);text-transform:uppercase;"><?php echo e($r->type); ?> Request</span>
                                        <span class="<?php echo $badge; ?>"><?php echo ucfirst(e($r->status)); ?></span>
                                    </div>
                                    <p class="mb-1" style="font-size:0.82rem;color:var(--text-muted);">
                                        <?php echo e($r->lesson_name); ?> &mdash; <?php echo date('d M Y', strtotime($r->old_date)); ?>
                                    </p>
                                    <?php if($r->reviewer_remarks): ?>
                                        <div style="padding:6px 10px;background:var(--primary-light);border-radius:6px;font-size:0.78rem;color:var(--primary-dark);">
                                            <strong>Admin:</strong> <?php echo e($r->reviewer_remarks); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center py-4" style="color:var(--text-muted);font-size:0.88rem;">No change requests submitted.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Fault Reports -->
                <div class="tab-pane fade" id="fault-pane" role="tabpanel" tabindex="0">
                    <div style="max-height:260px;overflow-y:auto;">
                        <?php if(!empty($data['my_faults'])): ?>
                            <?php foreach($data['my_faults'] as $f): ?>
                                <?php
                                $badge = 'badge-pending';
                                if ($f->status==='resolved') $badge='badge-approved';
                                if ($f->status==='closed') $badge='badge-inactive';
                                if ($f->status==='reported') $badge='badge-faulty';
                                ?>
                                <div class="mb-3 pb-3" style="border-bottom:1px solid var(--border);">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span style="font-size:0.82rem;font-weight:600;color:var(--text-h);text-transform:capitalize;">
                                            <?php echo e($f->equipment_type); ?> Issue
                                        </span>
                                        <span class="<?php echo $badge; ?>"><?php echo ucfirst(e($f->status)); ?></span>
                                    </div>
                                    <p class="mb-1" style="font-size:0.82rem;color:var(--text-muted);"><?php echo e($f->description); ?></p>
                                    <span style="font-size:0.72rem;color:var(--text-muted);">
                                        <i class="bi bi-calendar me-1"></i><?php echo date('d M Y', strtotime($f->created_at)); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center py-4" style="color:var(--text-muted);font-size:0.88rem;">No fault tickets submitted.</p>
                        <?php endif; ?>
                </div>

                <!-- Completed Sessions -->
                <div class="tab-pane fade" id="comp-pane" role="tabpanel" tabindex="0">
                    <div style="max-height:260px;overflow-y:auto;">
                        <?php if(!empty($data['recently_completed'])): ?>
                            <?php foreach($data['recently_completed'] as $s): ?>
                                <div class="mb-3 pb-3" style="border-bottom:1px solid var(--border);">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-secondary-subtle text-secondary small"><?php echo e($s->lab_code); ?></span>
                                        <span class="badge bg-success-subtle text-success small"><?php echo e($s->session_status); ?></span>
                                    </div>
                                    <p class="mb-1 fw-bold text-dark" style="font-size:0.82rem;"><?php echo e($s->lesson_name); ?></p>
                                    <span style="font-size:0.72rem;color:var(--text-muted);">
                                        <i class="bi bi-calendar-check me-1"></i>Completed: <?php echo date('d M Y H:i', strtotime($s->completed_at)); ?>
                                    </span>
                                    <?php if($s->instructor_remarks): ?>
                                        <p class="mb-0 text-muted mt-1 small italic" style="font-size: 0.78rem;">&ldquo;<?php echo e($s->instructor_remarks); ?>&rdquo;</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center py-4" style="color:var(--text-muted);font-size:0.88rem;">No recently completed sessions.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
