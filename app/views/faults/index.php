<!-- Admin Fault Ticket Registry View -->
<div class="card-clms mb-4">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Active Support & Fault Tickets</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Reporter</th>
                    <th>Equipment Type</th>
                    <th>Device Details</th>
                    <th>Issue Description</th>
                    <th>Date Logged</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $activeCount = 0;
                if(!empty($data['faults'])): 
                    foreach($data['faults'] as $f):
                        if($f->status === 'resolved' || $f->status === 'closed') continue;
                        $activeCount++;
                        $statusBadge = 'badge bg-danger';
                        if($f->status === 'in_progress') $statusBadge = 'badge bg-warning text-dark';
                ?>
                        <tr>
                            <td><span class="fw-bold text-primary">#FLT-<?php echo $f->id; ?></span></td>
                            <td>
                                <span class="fw-semibold d-block"><?php echo e($f->instructor_rank) . ' ' . e($f->instructor_name); ?></span>
                                <span class="text-muted small"><?php echo e($f->reported_by_name); ?></span>
                            </td>
                            <td><span class="text-uppercase small fw-bold"><?php echo e($f->equipment_type); ?></span></td>
                            <td>
                                <?php if($f->equipment_type === 'computer' && $f->computer_asset_no): ?>
                                    <span class="badge bg-secondary"><?php echo e($f->computer_asset_no); ?> (<?php echo e($f->computer_brand); ?>)</span>
                                <?php elseif($f->equipment_type === 'smart_board' && $f->smartboard_asset_id): ?>
                                    <span class="badge bg-secondary"><?php echo e($f->smartboard_asset_id); ?> (<?php echo e($f->smartboard_brand); ?>)</span>
                                <?php else: ?>
                                    <span class="text-muted small italic">General Network / Lab Issue</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-muted" title="<?php echo e($f->description); ?>"><?php echo e(strlen($f->description) > 50 ? substr($f->description, 0, 47) . '...' : $f->description); ?></span>
                            </td>
                            <td><?php echo date('d M Y H:i', strtotime($f->created_at)); ?></td>
                            <td><span class="<?php echo $statusBadge; ?>"><?php echo e($f->status); ?></span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary review-btn"
                                        data-id="<?php echo $f->id; ?>"
                                        data-type="<?php echo e($f->equipment_type); ?>"
                                        data-desc="<?php echo e($f->description); ?>"
                                        data-status="<?php echo e($f->status); ?>"
                                        data-notes="<?php echo e($f->resolution_notes); ?>"
                                        data-bs-toggle="modal" data-bs-target="#reviewFaultModal">
                                    Update
                                </button>
                            </td>
                        </tr>
                    <?php 
                    endforeach; 
                endif; 
                if($activeCount === 0):
                ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No active fault reports logged. All systems optimal.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-shield-check text-primary me-2"></i> Resolved Ticket Logs</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Reporter</th>
                    <th>Equipment Type</th>
                    <th>Resolution Notes</th>
                    <th>Date Processed</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $resolvedCount = 0;
                if(!empty($data['faults'])): 
                    foreach($data['faults'] as $f):
                        if($f->status !== 'resolved' && $f->status !== 'closed') continue;
                        $resolvedCount++;
                        $statusBadge = $f->status === 'resolved' ? 'badge bg-success' : 'badge bg-secondary';
                ?>
                        <tr>
                            <td><span class="fw-semibold">#FLT-<?php echo $f->id; ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($f->instructor_rank) . ' ' . e($f->instructor_name); ?></span></td>
                            <td><span class="text-uppercase small fw-bold"><?php echo e($f->equipment_type); ?></span></td>
                            <td><small class="text-muted"><?php echo e($f->resolution_notes ?: 'No resolution note specified.'); ?></small></td>
                            <td><?php echo date('d M Y H:i', strtotime($f->updated_at)); ?></td>
                            <td><span class="<?php echo $statusBadge; ?>"><?php echo e($f->status); ?></span></td>
                        </tr>
                    <?php 
                    endforeach; 
                endif;
                if($resolvedCount === 0):
                ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No resolved fault logs recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- REVIEW FAULT MODAL -->
<div class="modal fade" id="reviewFaultModal" tabindex="-1" aria-labelledby="reviewFaultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="reviewFaultModalLabel"><i class="bi bi-wrench-adjustable me-2 text-primary"></i> Process Fault Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="reviewForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="p-3 bg-light-subtle rounded border border-color mb-3" style="background-color: rgba(0,0,0,0.02);">
                        <div class="small mb-1"><strong>Device Type:</strong> <span id="lbl_type" class="text-uppercase fw-bold text-danger"></span></div>
                        <div class="small"><strong>Description:</strong> <span id="lbl_desc" class="text-muted"></span></div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label small fw-semibold">Ticket Status</label>
                        <select name="status" id="status" class="form-select form-control-clms" required>
                            <option value="reported">Reported (Faulty status)</option>
                            <option value="in_progress">In Progress (Maintenance status)</option>
                            <option value="resolved">Resolved (Restore Active Status)</option>
                            <option value="closed">Closed Archive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label small fw-semibold">Resolution / Repairs Notes</label>
                        <textarea name="notes" id="notes" class="form-control form-control-clms" rows="3" placeholder="Detail action taken, replaced parts, technician details..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Ticket</button>
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
            reviewForm.action = '<?php echo URLROOT; ?>fault/review/' + id;
            
            document.getElementById('lbl_type').innerText = this.getAttribute('data-type');
            document.getElementById('lbl_desc').innerText = this.getAttribute('data-desc');
            document.getElementById('status').value = this.getAttribute('data-status');
            document.getElementById('notes').value = this.getAttribute('data-notes') || '';
        });
    });
});
</script>
