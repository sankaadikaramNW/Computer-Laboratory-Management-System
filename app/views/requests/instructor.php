<!-- Instructor Change Requests View -->
<div class="row g-4">
    <!-- Submit Request Form Panel -->
    <div class="col-lg-4">
        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0"><i class="bi bi-calendar-plus-fill text-primary me-2"></i> Shift Schedule</h5>
            </div>
            
            <form action="<?php echo URLROOT; ?>request/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label for="allocation_id" class="form-label small fw-semibold">Select Session to Shift</label>
                    <select name="allocation_id" id="allocation_id" class="form-select form-control-clms" required>
                        <option value="">-- Choose Allocation Slot --</option>
                        <?php foreach($data['allocations'] as $a): ?>
                            <option value="<?php echo $a->id; ?>" <?php echo (isset($_GET['alloc_id']) && (int)$_GET['alloc_id'] === (int)$a->id) ? 'selected' : ''; ?>>
                                <?php echo date('d M', strtotime($a->date)); ?> | <?php echo e($a->lab_code); ?> | <?php echo e($a->lesson_code); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label small fw-semibold">Shift Action / Request Type</label>
                    <select name="type" id="type" class="form-select form-control-clms" required>
                        <option value="">-- Select Action --</option>
                        <option value="reschedule">Reschedule Date / Time Block</option>
                        <option value="change_lab">Transfer to Different Lab</option>
                        <option value="cancel">Cancel Booking Completely</option>
                    </select>
                </div>

                <!-- Dynamic Reschedule inputs -->
                <div class="d-none" id="rescheduleFields">
                    <div class="mb-3">
                        <label for="new_date" class="form-label small fw-semibold">Proposed Date</label>
                        <input type="date" name="new_date" id="new_date" class="form-control form-control-clms">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="new_start_time" class="form-label small fw-semibold">Proposed Start</label>
                            <input type="time" name="new_start_time" id="new_start_time" class="form-control form-control-clms">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="new_end_time" class="form-label small fw-semibold">Proposed End</label>
                            <input type="time" name="new_end_time" id="new_end_time" class="form-control form-control-clms">
                        </div>
                    </div>
                </div>

                <!-- Dynamic Change Lab inputs -->
                <div class="mb-3 d-none" id="labFields">
                    <label for="new_lab_id" class="form-label small fw-semibold">Proposed Laboratory Room</label>
                    <select name="new_lab_id" id="new_lab_id" class="form-select form-control-clms">
                        <option value="">-- Select Lab Room --</option>
                        <?php foreach($data['labs'] as $l): ?>
                            <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label small fw-semibold">Justification / Reason</label>
                    <textarea name="reason" id="reason" class="form-control form-control-clms" rows="3" placeholder="State reasons for this schedule shift..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold">Submit Shift Request</button>
            </form>
        </div>
    </div>

    <!-- Requests History list Panel -->
    <div class="col-lg-8">
        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0"><i class="bi bi-clock-history text-primary me-2"></i> Shift Request History</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-clms align-middle">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Type</th>
                            <th>Original Slot</th>
                            <th>Requested Shift</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['requests'])): ?>
                            <?php foreach($data['requests'] as $r): ?>
                                <?php 
                                $statusBadge = 'badge bg-warning';
                                if($r->status === 'approved') $statusBadge = 'badge bg-success';
                                if($r->status === 'rejected') $statusBadge = 'badge bg-danger';
                                ?>
                                <tr>
                                    <td><span class="fw-bold text-primary">#REQ-<?php echo $r->id; ?></span></td>
                                    <td><span class="text-capitalize small fw-bold"><?php echo str_replace('_', ' ', $r->type); ?></span></td>
                                    <td>
                                        <div class="small text-muted">
                                            <?php echo e($r->lesson_name); ?><br>
                                            Lab: <?php echo e($r->old_lab_code); ?><br>
                                            Date: <?php echo date('d M Y', strtotime($r->old_date)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($r->type === 'cancel'): ?>
                                            <span class="small italic text-danger">Cancel session</span>
                                        <?php elseif($r->type === 'reschedule'): ?>
                                            <div class="small text-warning">
                                                Date: <?php echo date('d M Y', strtotime($r->new_date)); ?><br>
                                                Time: <?php echo date('H:i', strtotime($r->new_start_time)) . '-' . date('H:i', strtotime($r->new_end_time)); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="small text-primary">
                                                New Lab: <?php echo e($r->new_lab_code); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="<?php echo $statusBadge; ?>"><?php echo e($r->status); ?></span>
                                        <?php if($r->reviewer_remarks): ?>
                                            <div class="small text-muted mt-1" style="font-size: 0.75rem;" title="<?php echo e($r->reviewer_remarks); ?>">
                                                <strong>Note:</strong> <?php echo e(strlen($r->reviewer_remarks) > 30 ? substr($r->reviewer_remarks, 0, 27) . '...' : $r->reviewer_remarks); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">You have not submitted any change requests yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var typeSelector = document.getElementById('type');
    var rescheduleFields = document.getElementById('rescheduleFields');
    var labFields = document.getElementById('labFields');
    
    // Auto toggle on load (for browser consistency)
    toggleFormFields(typeSelector.value);

    typeSelector.addEventListener('change', function() {
        toggleFormFields(this.value);
    });

    function toggleFormFields(val) {
        if (val === 'reschedule') {
            rescheduleFields.classList.remove('d-none');
            labFields.classList.add('d-none');
            document.getElementById('new_date').required = true;
            document.getElementById('new_start_time').required = true;
            document.getElementById('new_end_time').required = true;
            document.getElementById('new_lab_id').required = false;
        } else if (val === 'change_lab') {
            rescheduleFields.classList.add('d-none');
            labFields.classList.remove('d-none');
            document.getElementById('new_date').required = false;
            document.getElementById('new_start_time').required = false;
            document.getElementById('new_end_time').required = false;
            document.getElementById('new_lab_id').required = true;
        } else {
            rescheduleFields.classList.add('d-none');
            labFields.classList.add('d-none');
            document.getElementById('new_date').required = false;
            document.getElementById('new_start_time').required = false;
            document.getElementById('new_end_time').required = false;
            document.getElementById('new_lab_id').required = false;
        }
    }
});
</script>
