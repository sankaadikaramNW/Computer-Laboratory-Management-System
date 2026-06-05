<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar-check me-2"></i> My Schedule</h4>
            <span class="text-muted small">Manage your allocated laboratory sessions, complete pending events, and view upcoming schedules.</span>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>allocation/calendar" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar3 me-1"></i> School Calendar
            </a>
        </div>
    </div>

    <!-- Alert for Sessions Pending Completion -->
    <?php if(!empty($data['pending_sessions'])): ?>
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center p-3 mb-4" role="alert" style="border-radius: 8px;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
            <div>
                <strong class="d-block" style="font-size: 1rem; color: #856404;">Action Required: Pending Completion Logs</strong>
                <span style="font-size: 0.88rem; color: #856404;">You have <strong><?php echo count($data['pending_sessions']); ?></strong> session(s) awaiting completion status. Please update them to maintain accurate logs.</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Tabs -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <ul class="nav nav-pills gap-1 mb-4" id="scheduleTabs" role="tablist" style="background:var(--bg-page);padding:5px;border-radius:8px; width: fit-content;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-2 px-3" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab" style="font-size:0.88rem;border-radius:6px; font-weight: 600;">
                        <i class="bi bi-hourglass-split me-1"></i> Pending Completion (<?php echo count($data['pending_sessions']); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-2 px-3" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-pane" type="button" role="tab" style="font-size:0.88rem;border-radius:6px; font-weight: 600;">
                        <i class="bi bi-calendar-event me-1"></i> Upcoming Sessions (<?php echo count($data['upcoming_sessions']); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-2 px-3" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" style="font-size:0.88rem;border-radius:6px; font-weight: 600;">
                        <i class="bi bi-check-circle me-1"></i> Recently Completed
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="scheduleTabsContent">
                <!-- PENDING COMPLETION PANE -->
                <div class="tab-pane fade show active" id="pending-pane" role="tabpanel" tabindex="0">
                    <?php if(!empty($data['pending_sessions'])): ?>
                        <div class="row g-3">
                            <?php foreach($data['pending_sessions'] as $s): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm border" style="border-radius: 8px; overflow: hidden; background: var(--bg-card);">
                                        <div style="height: 4px; background: var(--warning);"></div>
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-weight: 600;">Pending Log</span>
                                                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--primary);">
                                                        <i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($s->start_time)) . ' - ' . date('H:i', strtotime($s->end_time)); ?>
                                                    </span>
                                                </div>
                                                <h6 class="fw-bold mb-1 text-dark"><?php echo e($s->lesson_code); ?>: <?php echo e($s->lesson_name); ?></h6>
                                                <p class="mb-2 text-muted small"><i class="bi bi-building me-1"></i><?php echo e($s->lab_code); ?> &mdash; <?php echo e($s->lab_name); ?></p>
                                                <div class="p-2 bg-light rounded mb-3" style="font-size: 0.8rem; border-left: 3px solid var(--primary);">
                                                    <strong>Allocated Date:</strong> <?php echo date('l, d M Y', strtotime($s->date)); ?>
                                                </div>
                                            </div>
                                            <a href="<?php echo URLROOT; ?>allocation/complete/<?php echo $s->id; ?>" class="btn btn-warning w-100 btn-sm text-dark fw-bold mt-auto">
                                                <i class="bi bi-pencil-square me-1"></i> Complete Session
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check-all fs-1 text-success mb-2 d-block"></i>
                            <h6 class="fw-bold">All caught up!</h6>
                            <p class="small mb-0">You have no laboratory sessions pending completion logs.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- UPCOMING SESSIONS PANE -->
                <div class="tab-pane fade" id="upcoming-pane" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover table-clms align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Laboratory</th>
                                    <th>Lesson</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($data['upcoming_sessions'])): ?>
                                    <?php foreach($data['upcoming_sessions'] as $s): ?>
                                        <tr>
                                            <td><div class="fw-bold"><?php echo date('d M Y', strtotime($s->date)); ?></div><span class="small text-muted"><?php echo date('l', strtotime($s->date)); ?></span></td>
                                            <td><span class="fw-semibold text-primary"><i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($s->start_time)) . ' - ' . date('H:i', strtotime($s->end_time)); ?></span></td>
                                            <td><span class="badge bg-secondary"><?php echo e($s->lab_code); ?></span><span class="d-block small text-muted"><?php echo e($s->lab_name); ?></span></td>
                                            <td><div class="fw-bold text-dark"><?php echo e($s->lesson_name); ?></div><span class="small text-muted"><?php echo e($s->lesson_code); ?></span></td>
                                            <td><span class="small text-muted"><?php echo e($s->remarks ?: 'No remarks'); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-calendar2-x fs-2 mb-2 d-block"></i> No upcoming scheduled allocations.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RECENTLY COMPLETED PANE -->
                <div class="tab-pane fade" id="completed-pane" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover table-clms align-middle">
                            <thead>
                                <tr>
                                    <th>Session Date</th>
                                    <th>Laboratory</th>
                                    <th>Lesson</th>
                                    <th>Completion Status</th>
                                    <th>Completed At</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($data['completed_sessions'])): ?>
                                    <?php foreach($data['completed_sessions'] as $s): 
                                        $statusClass = 'bg-success-subtle text-success';
                                        if ($s->session_status === 'Partially Completed') {
                                            $statusClass = 'bg-warning-subtle text-warning-emphasis';
                                        } elseif ($s->session_status === 'Cancelled') {
                                            $statusClass = 'bg-danger-subtle text-danger';
                                        }
                                    ?>
                                        <tr>
                                            <td><div class="fw-bold"><?php echo date('d M Y', strtotime($s->date)); ?></div><span class="small text-muted"><?php echo date('H:i', strtotime($s->start_time)) . ' - ' . date('H:i', strtotime($s->end_time)); ?></span></td>
                                            <td><span class="badge bg-secondary"><?php echo e($s->lab_code); ?></span><span class="d-block small text-muted"><?php echo e($s->lab_name); ?></span></td>
                                            <td><div class="fw-bold text-dark"><?php echo e($s->lesson_name); ?></div><span class="small text-muted"><?php echo e($s->lesson_code); ?></span></td>
                                            <td><span class="badge <?php echo $statusClass; ?>"><?php echo e($s->session_status); ?></span></td>
                                            <td>
                                                <div class="fw-semibold small"><?php echo date('d M Y', strtotime($s->completed_at)); ?></div>
                                                <span class="small text-muted"><?php echo date('H:i', strtotime($s->completed_at)); ?></span>
                                            </td>
                                            <td><span class="small text-muted text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo e($s->instructor_remarks); ?>"><?php echo e($s->instructor_remarks ?: 'No remarks'); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-journal-text fs-2 mb-2 d-block"></i> No recently completed sessions.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
