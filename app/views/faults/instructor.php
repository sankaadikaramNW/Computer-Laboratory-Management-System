<!-- Instructor Fault Reporting View -->
<div class="row g-4">
    <!-- Submit Ticket Form Panel -->
    <div class="col-lg-4">
        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0"><i class="bi bi-exclamation-octagon-fill text-danger me-2"></i> Report Support Ticket</h5>
            </div>
            
            <form action="<?php echo URLROOT; ?>fault/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label for="equipment_type" class="form-label small fw-semibold">Equipment / Issue Scope</label>
                    <select name="equipment_type" id="equipment_type" class="form-select form-control-clms" required>
                        <option value="">-- Choose Category --</option>
                        <option value="computer">Computer Workstation</option>
                        <option value="smart_board">Smart Board Digital Board</option>
                        <option value="network">General Network Issue</option>
                        <option value="other">Other Laboratory Issue</option>
                    </select>
                </div>

                <!-- Dynamic Computer selector -->
                <div class="mb-3 d-none" id="computerField">
                    <label for="computer_select" class="form-label small fw-semibold">Select Computer Workstation</label>
                    <select id="computer_select" class="form-select form-control-clms">
                        <option value="">-- Select Workstation Asset --</option>
                        <?php foreach($data['computers'] as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo e($c->asset_no); ?> (<?php echo e($c->brand); ?> spec)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Dynamic Smart Board selector -->
                <div class="mb-3 d-none" id="smartBoardField">
                    <label for="smartboard_select" class="form-label small fw-semibold">Select Smart Board</label>
                    <select id="smartboard_select" class="form-select form-control-clms">
                        <option value="">-- Select Smart Board Asset --</option>
                        <?php foreach($data['smartboards'] as $s): ?>
                            <option value="<?php echo $s->id; ?>"><?php echo e($s->asset_id); ?> (<?php echo e($s->brand); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small fw-semibold">Describe Fault Symptoms</label>
                    <textarea name="description" id="description" class="form-control form-control-clms" rows="4" placeholder="Detail hardware damage, software errors, or network speed drops..." required></textarea>
                </div>

                <button type="submit" class="btn btn-danger w-100 fw-semibold">Report Support Ticket</button>
            </form>
        </div>
    </div>

    <!-- Ticket history logs Panel -->
    <div class="col-lg-8">
        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0"><i class="bi bi-ticket-detailed-fill text-primary me-2"></i> Support Tickets History</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-clms align-middle">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Equipment Type</th>
                            <th>Device ID</th>
                            <th>Logged Issue</th>
                            <th>Logged Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['faults'])): ?>
                            <?php foreach($data['faults'] as $f): ?>
                                <?php 
                                $statusBadge = 'badge bg-danger';
                                if($f->status === 'in_progress') $statusBadge = 'badge bg-warning text-dark';
                                if($f->status === 'resolved') $statusBadge = 'badge bg-success';
                                if($f->status === 'closed') $statusBadge = 'badge bg-secondary';
                                ?>
                                <tr>
                                    <td><span class="fw-bold text-primary">#FLT-<?php echo $f->id; ?></span></td>
                                    <td><span class="text-uppercase small fw-bold"><?php echo e($f->equipment_type); ?></span></td>
                                    <td>
                                        <?php if($f->equipment_type === 'computer' && $f->computer_asset_no): ?>
                                            <span class="badge bg-secondary"><?php echo e($f->computer_asset_no); ?></span>
                                        <?php elseif($f->equipment_type === 'smart_board' && $f->smartboard_asset_id): ?>
                                            <span class="badge bg-secondary"><?php echo e($f->smartboard_asset_id); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo e($f->description); ?></div>
                                        <?php if($f->resolution_notes): ?>
                                            <div class="p-2 bg-light-subtle rounded border border-color small mt-1" style="font-size: 0.75rem; background-color: rgba(0,0,0,0.02);">
                                                <strong>Resolution Note:</strong> <?php echo e($f->resolution_notes); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($f->created_at)); ?></td>
                                    <td><span class="<?php echo $statusBadge; ?>"><?php echo e($f->status); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">You have not logged any support tickets.</td>
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
    var typeSelector = document.getElementById('equipment_type');
    var computerField = document.getElementById('computerField');
    var smartBoardField = document.getElementById('smartBoardField');
    
    var compSelect = document.getElementById('computer_select');
    var smartSelect = document.getElementById('smartboard_select');

    typeSelector.addEventListener('change', function() {
        var type = this.value;
        if (type === 'computer') {
            computerField.classList.remove('d-none');
            smartBoardField.classList.add('d-none');
            
            compSelect.required = true;
            smartSelect.required = false;
            
            compSelect.name = 'equipment_id';
            smartSelect.name = '';
        } else if (type === 'smart_board') {
            computerField.classList.add('d-none');
            smartBoardField.classList.remove('d-none');
            
            compSelect.required = false;
            smartSelect.required = true;
            
            compSelect.name = '';
            smartSelect.name = 'equipment_id';
        } else {
            computerField.classList.add('d-none');
            smartBoardField.classList.add('d-none');
            
            compSelect.required = false;
            smartSelect.required = false;
            
            compSelect.name = '';
            smartSelect.name = '';
        }
    });
});
</script>
