<!-- Lab Allocations Scheduling List -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i> Scheduled Laboratory Allocations</h5>
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>allocation/calendar" class="btn btn-outline-primary btn-sm"><i class="bi bi-calendar3 me-1"></i> Interactive Calendar</a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAllocationModal">
                <i class="bi bi-calendar-plus me-1"></i> Allocate Laboratory
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Camp Location</th>
                    <th>Lab Code</th>
                    <th>Lesson / Syllabus</th>
                    <th>Instructor Assigned</th>
                    <th>Scheduled Date</th>
                    <th>Time Slots</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['allocations'])): ?>
                    <?php foreach($data['allocations'] as $a): ?>
                        <tr>
                            <td><span class="badge bg-info-light text-info fw-semibold"><i class="bi bi-geo-alt-fill me-1"></i><?php echo e($a->camp_name ?: 'Global'); ?></span></td>
                            <td><span class="badge bg-secondary"><?php echo e($a->lab_code); ?> - <?php echo e($a->lab_name); ?></span></td>
                            <td>
                                <div>
                                    <strong class="text-primary small"><?php echo e($a->lesson_code); ?></strong>
                                </div>
                                <span class="small fw-semibold"><?php echo e($a->lesson_name); ?></span>
                            </td>
                            <td><span class="fw-semibold"><?php echo e($a->instructor_rank); ?> <?php echo e($a->instructor_name); ?></span></td>
                            <td><?php echo date('d M Y', strtotime($a->date)); ?></td>
                            <td><span class="fw-bold text-nowrap"><?php echo date('H:i', strtotime($a->start_time)) . ' - ' . date('H:i', strtotime($a->end_time)); ?></span></td>
                            <td><small class="text-muted"><?php echo e($a->remarks ?: 'None'); ?></small></td>
                            <td>
                                <div class="btn-group">
                                    <!-- Edit Trigger -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $a->id; ?>"
                                            data-labid="<?php echo $a->lab_id; ?>"
                                            data-lessonid="<?php echo $a->lesson_id; ?>"
                                            data-instructorid="<?php echo $a->instructor_id; ?>"
                                            data-date="<?php echo e($a->date); ?>"
                                            data-start="<?php echo e($a->start_time); ?>"
                                            data-end="<?php echo e($a->end_time); ?>"
                                            data-remarks="<?php echo e($a->remarks); ?>"
                                            data-camp="<?php echo $a->camp_id; ?>"
                                            data-bs-toggle="modal" data-bs-target="#editAllocationModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <!-- Delete Trigger -->
                                    <a href="<?php echo URLROOT; ?>allocation/delete/<?php echo $a->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this scheduled allocation?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No scheduled allocations found in system.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ALLOCATE LAB MODAL -->
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
                        <label for="add_camp_id" class="form-label small fw-semibold">Camp Location</label>
                        <select name="camp_id" id="add_camp_id" class="form-select form-control-clms camp-filter-select" data-target="#add_lab_id" required>
                            <option value="">-- Choose Camp Location --</option>
                            <?php foreach($data['camps'] as $camp): ?>
                                <option value="<?php echo $camp->id; ?>"><?php echo e($camp->name); ?> (<?php echo e($camp->code); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_lab_id" class="form-label small fw-semibold">Select Laboratory</label>
                        <select name="lab_id" id="add_lab_id" class="form-select form-control-clms" required>
                            <option value="">-- Choose Lab Room --</option>
                            <?php foreach($data['labs'] as $l): ?>
                                <option value="<?php echo $l->id; ?>" data-camp="<?php echo $l->camp_id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?> (Cap: <?php echo $l->capacity; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_lesson_id" class="form-label small fw-semibold">Course Lesson / Syllabus</label>
                        <select name="lesson_id" id="add_lesson_id" class="form-select form-control-clms" required>
                            <option value="">-- Choose Syllabus Lesson --</option>
                            <?php foreach($data['lessons'] as $les): ?>
                                <option value="<?php echo $les->id; ?>"><?php echo e($les->lesson_code); ?> - <?php echo e($les->lesson_name); ?> (<?php echo $les->duration; ?> hrs)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_instructor_id" class="form-label small fw-semibold">Assigned Instructor</label>
                        <select name="instructor_id" id="add_instructor_id" class="form-select form-control-clms" required>
                            <option value="">-- Choose Instructor --</option>
                            <?php foreach($data['instructors'] as $i): ?>
                                <?php if($i->status === 'active'): ?>
                                    <option value="<?php echo $i->id; ?>"><?php echo e($i->rank); ?> <?php echo e($i->full_name); ?> (<?php echo e($i->trade); ?>)</option>
                                <?php else: ?>
                                    <option value="<?php echo $i->id; ?>" class="inactive-archived-option d-none" disabled style="display: none;"><?php echo e($i->rank); ?> <?php echo e($i->full_name); ?> (<?php echo e($i->trade); ?>) - [<?php echo ucfirst($i->status); ?>]</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input toggle-inactive-instructors" type="checkbox" id="add_toggle_inactive">
                            <label class="form-check-label small text-muted" for="add_toggle_inactive">Include Inactive/Archived Instructors</label>
                        </div>
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

<!-- EDIT ALLOCATION MODAL -->
<div class="modal fade" id="editAllocationModal" tabindex="-1" aria-labelledby="editAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editAllocationModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Scheduled Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_camp_id" class="form-label small fw-semibold">Camp Location</label>
                        <select name="camp_id" id="edit_camp_id" class="form-select form-control-clms camp-filter-select" data-target="#edit_lab_id" required>
                            <option value="">-- Choose Camp Location --</option>
                            <?php foreach($data['camps'] as $camp): ?>
                                <option value="<?php echo $camp->id; ?>"><?php echo e($camp->name); ?> (<?php echo e($camp->code); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_lab_id" class="form-label small fw-semibold">Select Laboratory</label>
                        <select name="lab_id" id="edit_lab_id" class="form-select form-control-clms" required>
                            <option value="">-- Choose Lab Room --</option>
                            <?php foreach($data['labs'] as $l): ?>
                                <option value="<?php echo $l->id; ?>" data-camp="<?php echo $l->camp_id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_lesson_id" class="form-label small fw-semibold">Course Lesson / Syllabus</label>
                        <select name="lesson_id" id="edit_lesson_id" class="form-select form-control-clms" required>
                            <?php foreach($data['lessons'] as $les): ?>
                                <option value="<?php echo $les->id; ?>"><?php echo e($les->lesson_code); ?> - <?php echo e($les->lesson_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_instructor_id" class="form-label small fw-semibold">Assigned Instructor</label>
                        <select name="instructor_id" id="edit_instructor_id" class="form-select form-control-clms" required>
                            <?php foreach($data['instructors'] as $i): ?>
                                <?php if($i->status === 'active'): ?>
                                    <option value="<?php echo $i->id; ?>"><?php echo e($i->rank); ?> <?php echo e($i->full_name); ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $i->id; ?>" class="inactive-archived-option d-none" disabled style="display: none;"><?php echo e($i->rank); ?> <?php echo e($i->full_name); ?> - [<?php echo ucfirst($i->status); ?>]</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input toggle-inactive-instructors" type="checkbox" id="edit_toggle_inactive">
                            <label class="form-check-label small text-muted" for="edit_toggle_inactive">Include Inactive/Archived Instructors</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_date" class="form-label small fw-semibold">Date</label>
                        <input type="date" name="date" id="edit_date" class="form-control form-control-clms" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_time" class="form-label small fw-semibold">Start Time</label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control form-control-clms" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_time" class="form-label small fw-semibold">End Time</label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_remarks" class="form-label small fw-semibold">Remarks / Batch / Notes</label>
                        <textarea name="remarks" id="edit_remarks" class="form-control form-control-clms" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Camp Filtering logic for Laboratories dropdown
    const filterLabsByCamp = function(campSelect) {
        const targetSelector = campSelect.getAttribute('data-target');
        const labSelect = document.querySelector(targetSelector);
        if (!labSelect) return;

        const selectedCamp = campSelect.value;
        let currentValueStillValid = false;

        Array.from(labSelect.options).forEach(opt => {
            const optCamp = opt.getAttribute('data-camp');
            // Show placeholder option always
            if (!optCamp) {
                opt.style.display = '';
                opt.disabled = false;
                return;
            }

            if (!selectedCamp || optCamp == selectedCamp) {
                opt.style.display = '';
                opt.disabled = false;
                if (opt.value == labSelect.value) {
                    currentValueStillValid = true;
                }
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
            }
        });

        if (!currentValueStillValid) {
            labSelect.value = '';
        }
    };

    document.querySelectorAll('.camp-filter-select').forEach(select => {
        select.addEventListener('change', function() {
            filterLabsByCamp(this);
        });
        // Initial run to filter on page load if a camp is preselected
        if (select.value) {
            filterLabsByCamp(select);
        }
    });

    // Toggle inactive/archived options logic
    document.querySelectorAll('.toggle-inactive-instructors').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const modal = this.closest('.modal');
            const options = modal.querySelectorAll('.inactive-archived-option');
            options.forEach(opt => {
                if (this.checked) {
                    opt.classList.remove('d-none');
                    opt.style.display = '';
                    opt.disabled = false;
                } else {
                    opt.classList.add('d-none');
                    opt.style.display = 'none';
                    if (opt.selected) {
                        modal.querySelector('select[name="instructor_id"]').value = "";
                    }
                    opt.disabled = true;
                }
            });
        });
    });

    const editBtns = document.querySelectorAll('.edit-btn');
    const editForm = document.getElementById('editForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editForm.action = '<?php echo URLROOT; ?>allocation/update/' + id;
            
            // Set camp first and dispatch change event to filter laboratories
            const campId = this.getAttribute('data-camp');
            const campSelect = document.getElementById('edit_camp_id');
            campSelect.value = campId;
            filterLabsByCamp(campSelect);

            document.getElementById('edit_lab_id').value = this.getAttribute('data-labid');
            document.getElementById('edit_lesson_id').value = this.getAttribute('data-lessonid');
            
            const instructorId = this.getAttribute('data-instructorid');
            const select = document.getElementById('edit_instructor_id');
            
            // Check if selected option is inactive/archived
            let isInactiveOrArchived = false;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value == instructorId && select.options[i].classList.contains('inactive-archived-option')) {
                    isInactiveOrArchived = true;
                    break;
                }
            }
            
            const toggle = document.getElementById('edit_toggle_inactive');
            if (isInactiveOrArchived) {
                toggle.checked = true;
                toggle.dispatchEvent(new Event('change'));
            } else {
                toggle.checked = false;
                toggle.dispatchEvent(new Event('change'));
            }
            
            select.value = instructorId;
            document.getElementById('edit_date').value = this.getAttribute('data-date');
            
            // Format time strings (remove seconds if present)
            let start = this.getAttribute('data-start');
            let end = this.getAttribute('data-end');
            document.getElementById('edit_start_time').value = start.substring(0, 5);
            document.getElementById('edit_end_time').value = end.substring(0, 5);
            
            document.getElementById('edit_remarks').value = this.getAttribute('data-remarks');
        });
    });
});
</script>
