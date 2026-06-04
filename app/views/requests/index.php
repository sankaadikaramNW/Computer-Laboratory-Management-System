<!-- Admin Change Requests View -->
<div class="card-clms mb-4">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-arrow-left-right text-primary me-2"></i> Pending Change Tickets</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Instructor</th>
                    <th>Type</th>
                    <th>Current Booking Details</th>
                    <th>Requested Shift Details</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $pendingCount = 0;
                if(!empty($data['requests'])): 
                    foreach($data['requests'] as $r):
                        if($r->status !== 'pending') continue;
                        $pendingCount++;
                ?>
                        <tr>
                            <td><span class="fw-bold text-primary">#REQ-<?php echo $r->id; ?></span></td>
                            <td>
                                <span class="fw-semibold d-block"><?php echo e($r->instructor_rank) . ' ' . e($r->instructor_name); ?></span>
                                <span class="text-muted small"><?php echo e($r->requester_name); ?></span>
                            </td>
                            <td>
                                <?php if($r->type === 'cancel'): ?>
                                    <span class="badge bg-danger">Cancellation</span>
                                <?php elseif($r->type === 'reschedule'): ?>
                                    <span class="badge bg-warning text-dark">Rescheduling</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Lab Transfer</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small">
                                    <strong><?php echo e($r->lesson_name); ?></strong><br>
                                    Lab: <span class="badge bg-secondary"><?php echo e($r->old_lab_code); ?></span><br>
                                    Date: <?php echo date('d M Y', strtotime($r->old_date)); ?><br>
                                    Time: <?php echo date('H:i', strtotime($r->old_start_time)) . '-' . date('H:i', strtotime($r->old_end_time)); ?>
                                </div>
                            </td>
                            <td>
                                <?php if($r->type === 'cancel'): ?>
                                    <span class="text-muted small italic">N/A (Delete Booking)</span>
                                <?php elseif($r->type === 'reschedule'): ?>
                                    <div class="small text-warning">
                                        Date: <strong><?php echo date('d M Y', strtotime($r->new_date)); ?></strong><br>
                                        Time: <strong><?php echo date('H:i', strtotime($r->new_start_time)) . '-' . date('H:i', strtotime($r->new_end_time)); ?></strong>
                                    </div>
                                <?php else: ?>
                                    <div class="small text-primary">
                                        New Lab: <strong><?php echo e($r->new_lab_code) . ' - ' . e($r->new_lab_name); ?></strong>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-muted" title="<?php echo e($r->reason); ?>"><?php echo e(strlen($r->reason) > 50 ? substr($r->reason, 0, 47) . '...' : $r->reason); ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary review-btn"
                                        data-id="<?php echo $r->id; ?>"
                                        data-type="<?php echo e($r->type); ?>"
                                        data-instructor="<?php echo e($r->instructor_rank) . ' ' . e($r->instructor_name); ?>"
                                        data-reason="<?php echo e($r->reason); ?>"
                                        data-bs-toggle="modal" data-bs-target="#reviewRequestModal">
                                    Review
                                </button>
                            </td>
                        </tr>
                    <?php 
                    endforeach; 
                endif; 
                if($pendingCount === 0):
                ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No pending change requests.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-journal-check text-primary me-2"></i> Processed Request Logs</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Instructor</th>
                    <th>Type</th>
                    <th>Date Processed</th>
                    <th>Reviewer Remarks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $processedCount = 0;
                if(!empty($data['requests'])): 
                    foreach($data['requests'] as $r):
                        if($r->status === 'pending') continue;
                        $processedCount++;
                        $statusBadge = $r->status === 'approved' ? 'badge bg-success' : 'badge bg-danger';
                ?>
                        <tr>
                            <td><span class="fw-semibold">#REQ-<?php echo $r->id; ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($r->instructor_rank) . ' ' . e($r->instructor_name); ?></span></td>
                            <td><span class="text-capitalize small fw-bold"><?php echo str_replace('_', ' ', $r->type); ?></span></td>
                            <td><?php echo date('d M Y H:i', strtotime($r->updated_at)); ?></td>
                            <td><small class="text-muted"><?php echo e($r->reviewer_remarks ?: 'None'); ?></small></td>
                            <td><span class="<?php echo $statusBadge; ?>"><?php echo e($r->status); ?></span></td>
                        </tr>
                    <?php 
                    endforeach; 
                endif;
                if($processedCount === 0):
                ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No processed requests recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- REVIEW REQUEST MODAL -->
<div class="modal fade" id="reviewRequestModal" tabindex="-1" aria-labelledby="reviewRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="reviewRequestModalLabel"><i class="bi bi-shield-check me-2 text-primary"></i> Review Change Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="reviewForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="p-3 bg-light-subtle rounded border border-color mb-3" style="background-color: rgba(0,0,0,0.02);">
                        <div class="small mb-1"><strong>Submitted By:</strong> <span id="lbl_instructor"></span></div>
                        <div class="small mb-1"><strong>Request Type:</strong> <span id="lbl_type" class="text-uppercase fw-bold text-primary"></span></div>
                        <div class="small"><strong>Reason:</strong> <span id="lbl_reason" class="text-muted italic"></span></div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label small fw-semibold">Review Decision</label>
                        <select name="status" id="status" class="form-select form-control-clms" required>
                            <option value="approved">Approve & Apply Schedule Shift</option>
                            <option value="rejected">Reject Request</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label small fw-semibold">Reviewer Remarks / Feedback</label>
                        <textarea name="remarks" id="remarks" class="form-control form-control-clms" rows="3" placeholder="Specify approval note or reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewBtns = document.querySelectorAll('.review-btn');
    const reviewForm = document.getElementById('reviewForm');
    
    reviewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            reviewForm.action = '<?php echo URLROOT; ?>request/review/' + id;
            
            document.getElementById('lbl_instructor').innerText = this.getAttribute('data-instructor');
            document.getElementById('lbl_type').innerText = this.getAttribute('data-type').replace('_', ' ');
            document.getElementById('lbl_reason').innerText = this.getAttribute('data-reason');
        });
    });
});
</script>
