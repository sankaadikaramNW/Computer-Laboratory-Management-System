<!-- Calendar view showing FullCalendar scheduler -->
<div class="row g-4 mb-4">
    <!-- Color legend guide panel -->
    <div class="col-lg-3 no-print">
        <div class="card-clms mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle-fill me-2 text-primary"></i> Lab Colors Guide</h6>
            <div class="d-flex flex-column gap-2" id="labLegend">
                <!-- Color codes mapping matching the controllers mapping -->
                <div class="d-flex align-items-center">
                    <span class="legend-color-dot" style="background-color: #1d3557; display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:8px;"></span>
                    <span class="small fw-semibold">Lab 01 (Database/Cisco)</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-color-dot" style="background-color: #457b9d; display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:8px;"></span>
                    <span class="small fw-semibold">Lab 02 (Software/Web)</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-color-dot" style="background-color: #2a9d8f; display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:8px;"></span>
                    <span class="small fw-semibold">Lab 03 (Hardware/Repair)</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-color-dot" style="background-color: #7209b7; display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:8px;"></span>
                    <span class="small fw-semibold">Lab 04 (Network Security)</span>
                </div>
            </div>
        </div>

        <?php if (isAdmin()): ?>
            <div class="card-clms">
                <h6 class="fw-bold mb-2"><i class="bi bi-shield-exclamation text-warning me-2"></i> Scheduler Actions</h6>
                <p class="text-muted small">You can reschedule lab sessions by dragging blocks or stretching they ends (resizing) in <strong>Week/Day view</strong>.</p>
                <button type="button" class="btn btn-primary btn-sm w-100 mt-2" data-bs-toggle="modal" data-bs-target="#addAllocationModal">
                    <i class="bi bi-calendar-plus me-1"></i> Allocate Laboratory
                </button>
            </div>
        <?php else: ?>
            <div class="card-clms">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-primary me-2"></i> Booking Conflicts</h6>
                <p class="text-muted small">Need to change an allocation slot? Submit a change request using the form in the menu.</p>
                <a href="<?php echo URLROOT; ?>request/instructor" class="btn btn-outline-primary btn-sm w-100 mt-2"><i class="bi bi-arrow-left-right me-1"></i> Request Change</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Calendar Area -->
    <div class="col-lg-9">
        <div class="card-clms p-4">
            <!-- FullCalendar Container -->
            <div id="calendar" style="min-height: 600px; color: var(--text-primary);"></div>
        </div>
    </div>
</div>

<!-- EVENT DETAILS VIEW MODAL -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="eventDetailModalLabel"><i class="bi bi-info-circle-fill me-2 text-primary"></i> Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Laboratory</label>
                    <div class="fw-semibold text-primary fs-6" id="det_lab"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Course Lesson</label>
                    <div class="fw-bold" id="det_lesson"></div>
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
            <div class="modal-footer border-color justify-content-between">
                <div class="admin-actions d-none" id="det_admin_actions">
                    <a href="" id="det_edit_btn" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-fill me-1"></i> Edit</a>
                    <a href="" id="det_delete_btn" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this scheduled allocation?');"><i class="bi bi-trash-fill me-1"></i> Cancel Slot</a>
                </div>
                <div class="instructor-actions d-none" id="det_instructor_actions">
                    <a href="" id="det_complete_btn" class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i> Complete Session</a>
                </div>
                <button type="button" class="btn btn-secondary btn-sm ms-auto" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ALLOCATE LAB MODAL (DRAG & DROP / CLICK ON CALENDAR SUPPORT) -->
<?php if (isAdmin()): ?>
    <!-- Copy from schedule index to support adding directly from calendar click -->
    <div class="modal fade" id="addAllocationModal" tabindex="-1" aria-labelledby="addAllocationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="modal-header border-color">
                    <h5 class="modal-title fw-bold" id="addAllocationModalLabel"><i class="bi bi-calendar-plus me-2 text-primary"></i> Allocate Laboratory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo URLROOT; ?>allocation/create" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                        <div class="mb-3">
                            <label for="add_lab_id" class="form-label small fw-semibold">Select Laboratory</label>
                            <select name="lab_id" id="add_lab_id" class="form-select form-control-clms" required>
                                <option value="">-- Choose Lab Room --</option>
                                <?php foreach($data['labs'] as $l): ?>
                                    <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="add_lesson_id" class="form-label small fw-semibold">Course Lesson / Syllabus</label>
                            <select name="lesson_id" id="add_lesson_id" class="form-select form-control-clms" required>
                                <option value="">-- Choose Syllabus Lesson --</option>
                                <?php foreach($data['lessons'] as $les): ?>
                                    <option value="<?php echo $les->id; ?>"><?php echo e($les->lesson_code); ?> - <?php echo e($les->lesson_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="add_instructor_id" class="form-label small fw-semibold">Assigned Instructor</label>
                            <select name="instructor_id" id="add_instructor_id" class="form-select form-control-clms" required>
                                <option value="">-- Choose Instructor --</option>
                                <?php foreach($data['instructors'] as $i): ?>
                                    <option value="<?php echo $i->id; ?>"><?php echo e($i->rank); ?> <?php echo e($i->full_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="add_date" class="form-label small fw-semibold">Date</label>
                            <input type="date" name="date" id="add_date" class="form-control form-control-clms" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_start_time" class="form-label small fw-semibold">Start Time</label>
                                <input type="time" name="start_time" id="add_start_time" class="form-control form-control-clms" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_end_time" class="form-label small fw-semibold">End Time</label>
                                <input type="time" name="end_time" id="add_end_time" class="form-control form-control-clms" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="add_remarks" class="form-label small fw-semibold">Remarks / Batch / Notes</label>
                            <textarea name="remarks" id="add_remarks" class="form-control form-control-clms" rows="2" placeholder="e.g. IT Cpl Course, Batch 45"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-color">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Book Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: <?php echo isAdmin() ? 'true' : 'false'; ?>,
        droppable: false,
        events: '<?php echo URLROOT; ?>allocation/getCalendarEvents',
        timeZone: 'local',
        
        // Handle drag and drop reschedule
        eventDrop: function(info) {
            updateEventSchedule(info);
        },
        
        // Handle time resize reschedule
        eventResize: function(info) {
            updateEventSchedule(info);
        },
        
        // Handle event click (Detail view)
        eventClick: function(info) {
            var props = info.event.extendedProps;
            var start = info.event.start;
            var end = info.event.end;
            
            // Format times
            var pad = function(num) { return (num < 10 ? '0' : '') + num; };
            var dateStr = start.getFullYear() + '-' + pad(start.getMonth() + 1) + '-' + pad(start.getDate());
            var startTimeStr = pad(start.getHours()) + ':' + pad(start.getMinutes());
            var endTimeStr = end ? (pad(end.getHours()) + ':' + pad(end.getMinutes())) : '';
            
            document.getElementById('det_lab').innerText = props.labCode + ' - ' + props.labName;
            document.getElementById('det_lesson').innerText = props.lessonCode + ' - ' + props.lessonName;
            document.getElementById('det_instructor').innerText = props.instructorName;
            document.getElementById('det_date').innerText = dateStr;
            document.getElementById('det_time').innerText = startTimeStr + ' - ' + endTimeStr;
            document.getElementById('det_remarks').innerText = props.remarks;
            
            // Reset action panels
            var adminPanel = document.getElementById('det_admin_actions');
            if (adminPanel) adminPanel.classList.add('d-none');
            var instructorPanel = document.getElementById('det_instructor_actions');
            if (instructorPanel) instructorPanel.classList.add('d-none');

            // Set session status badge
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
            
            // Show admin options if authorized
            <?php if(isAdmin()): ?>
                if (adminPanel) {
                    adminPanel.classList.remove('d-none');
                    // Set links
                    document.getElementById('det_edit_btn').href = '<?php echo URLROOT; ?>allocation/schedule';
                    document.getElementById('det_delete_btn').href = '<?php echo URLROOT; ?>allocation/delete/' + info.event.id;
                }
            <?php endif; ?>

            // Show instructor options if authorized
            <?php if(isInstructor()): ?>
                if (instructorPanel) {
                    var currentInstructorId = <?php echo $_SESSION['instructor_id'] ?: 0; ?>;
                    if (parseInt(props.instructorId) === currentInstructorId && (status === 'Scheduled' || status === 'In Progress')) {
                        instructorPanel.classList.remove('d-none');
                        document.getElementById('det_complete_btn').href = '<?php echo URLROOT; ?>allocation/complete/' + info.event.id;
                    }
                }
            <?php endif; ?>
            
            // Open modal
            var modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            modal.show();
        }
    });
    
    calendar.render();

    // AJAX drag/resize helper
    function updateEventSchedule(info) {
        var startIso = info.event.start.toISOString();
        // Handle null end by adding 1 hour default
        var endIso = info.event.end ? info.event.end.toISOString() : new Date(info.event.start.getTime() + 60*60*1000).toISOString();
        
        var payload = {
            id: info.event.id,
            start: startIso,
            end: endIso
        };
        
        // Use our secure AJAX handler (which automatically handles CSRF if header is passed)
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('<?php echo URLROOT; ?>allocation/apiMoveEvent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success
                showAlert('success', data.message || 'Allocation updated.');
            } else {
                // Fail - Revert move
                showAlert('danger', data.message || 'Scheduling conflict detected.');
                info.revert();
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('danger', 'System connection error.');
            info.revert();
        });
    }

    // Quick notification banner helper
    function showAlert(type, msg) {
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 z-3';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        document.body.appendChild(alertDiv);
        setTimeout(function() {
            var bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }
});
</script>
