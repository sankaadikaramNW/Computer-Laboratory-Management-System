<!-- Servicing & Maintenance View -->
<div class="card-clms mb-4">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-tools text-primary me-2"></i> preventative Servicing & Repairs</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMaintModal">
            <i class="bi bi-plus-circle me-1"></i> Log Servicing Task
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Equipment Type</th>
                    <th>Asset Details</th>
                    <th>Service Scope</th>
                    <th>Technician</th>
                    <th>Servicing Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['records'])): ?>
                    <?php foreach($data['records'] as $r): ?>
                        <tr>
                            <td><span class="fw-bold text-primary">#MNT-<?php echo $r->id; ?></span></td>
                            <td><span class="text-uppercase small fw-bold"><?php echo e($r->equipment_type); ?></span></td>
                            <td>
                                <?php if($r->equipment_type === 'computer' && $r->computer_asset_no): ?>
                                    <span class="badge bg-secondary"><?php echo e($r->computer_asset_no); ?> (<?php echo e($r->computer_brand); ?>)</span>
                                <?php elseif($r->equipment_type === 'smart_board' && $r->smartboard_asset_id): ?>
                                    <span class="badge bg-secondary"><?php echo e($r->smartboard_asset_id); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic">General Infrastructure</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><span class="badge bg-outline-info text-capitalize small border" style="border-color: var(--card-border) !important;"><?php echo str_replace('_', ' ', $r->issue_type); ?></span></div>
                                <span class="small text-muted" title="<?php echo e($r->notes); ?>"><?php echo e(strlen($r->notes) > 40 ? substr($r->notes, 0, 37) . '...' : $r->notes); ?></span>
                            </td>
                            <td><span class="fw-semibold small"><?php echo e($r->assigned_technician); ?></span></td>
                            <td><?php echo date('d M Y', strtotime($r->repair_date)); ?></td>
                            <td>
                                <?php if($r->status === 'completed'): ?>
                                    <span class="badge badge-active bg-success">Completed</span>
                                <?php elseif($r->status === 'in_progress'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>In Progress</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Scheduled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $r->id; ?>"
                                            data-type="<?php echo e($r->equipment_type); ?>"
                                            data-equipid="<?php echo $r->equipment_id; ?>"
                                            data-issue="<?php echo e($r->issue_type); ?>"
                                            data-tech="<?php echo e($r->assigned_technician); ?>"
                                            data-date="<?php echo e($r->repair_date); ?>"
                                            data-status="<?php echo e($r->status); ?>"
                                            data-notes="<?php echo e($r->notes); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editMaintModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>maintenance/delete/<?php echo $r->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this maintenance record?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No servicing logs configured.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- LOG SERVICING TASK MODAL -->
<div class="modal fade" id="addMaintModal" tabindex="-1" aria-labelledby="addMaintModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addMaintModalLabel"><i class="bi bi-tools me-2 text-primary"></i> Log Servicing Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>maintenance/create" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_equipment_type" class="form-label small fw-semibold">Equipment Class</label>
                        <select name="equipment_type" id="add_equipment_type" class="form-select form-control-clms" required>
                            <option value="">-- Choose Category --</option>
                            <option value="computer">Computer Workstation</option>
                            <option value="smart_board">Smart Board Digital Board</option>
                            <option value="other">General Lab Infrastructure</option>
                        </select>
                    </div>

                    <!-- Dynamic Computer Selector -->
                    <div class="mb-3 d-none" id="add_computerField">
                        <label for="add_computer_select" class="form-label small fw-semibold">Select Computer Workstation</label>
                        <select id="add_computer_select" class="form-select form-control-clms">
                            <option value="">-- Select Workstation Asset --</option>
                            <?php foreach($data['computers'] as $c): ?>
                                <option value="<?php echo $c->id; ?>"><?php echo e($c->asset_no); ?> (<?php echo e($c->brand); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dynamic Smart Board Selector -->
                    <div class="mb-3 d-none" id="add_smartBoardField">
                        <label for="add_smartboard_select" class="form-label small fw-semibold">Select Smart Board</label>
                        <select id="add_smartboard_select" class="form-select form-control-clms">
                            <option value="">-- Select Smart Board Asset --</option>
                            <?php foreach($data['smartboards'] as $s): ?>
                                <option value="<?php echo $s->id; ?>"><?php echo e($s->asset_id); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_issue_type" class="form-label small fw-semibold">Service Scope</label>
                        <select name="issue_type" id="add_issue_type" class="form-select form-control-clms" required>
                            <option value="preventative">Routine Preventative Check</option>
                            <option value="hardware_repair">Hardware Repair / Part Swap</option>
                            <option value="software_install">Software Setup / Re-imaging</option>
                            <option value="upgrade">Hardware Upgrade</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_assigned_technician" class="form-label small fw-semibold">Assigned Technician / Vendor</label>
                        <input type="text" name="assigned_technician" id="add_assigned_technician" class="form-control form-control-clms" placeholder="e.g. Sgt. Jayasekara / Dell Tech Support" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_repair_date" class="form-label small fw-semibold">Servicing Date</label>
                        <input type="date" name="repair_date" id="add_repair_date" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_status" class="form-label small fw-semibold">Job Status</label>
                        <select name="status" id="add_status" class="form-select form-control-clms" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress (Move asset to Maintenance)</option>
                            <option value="completed">Completed (Restore asset to Active)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_notes" class="form-label small fw-semibold">Servicing details / Technician Notes</label>
                        <textarea name="notes" id="add_notes" class="form-control form-control-clms" rows="3" placeholder="Parts swapped, OS key, diagnostics notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Servicing Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT SERVICING TASK MODAL -->
<div class="modal fade" id="editMaintModal" tabindex="-1" aria-labelledby="editMaintModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editMaintModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Servicing Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_equipment_type" class="form-label small fw-semibold">Equipment Class</label>
                        <select name="equipment_type" id="edit_equipment_type" class="form-select form-control-clms" required>
                            <option value="computer">Computer Workstation</option>
                            <option value="smart_board">Smart Board Digital Board</option>
                            <option value="other">General Lab Infrastructure</option>
                        </select>
                    </div>

                    <!-- Dynamic Computer Selector -->
                    <div class="mb-3 d-none" id="edit_computerField">
                        <label for="edit_computer_select" class="form-label small fw-semibold">Select Computer Workstation</label>
                        <select id="edit_computer_select" class="form-select form-control-clms">
                            <option value="">-- Select Workstation Asset --</option>
                            <?php foreach($data['computers'] as $c): ?>
                                <option value="<?php echo $c->id; ?>"><?php echo e($c->asset_no); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dynamic Smart Board Selector -->
                    <div class="mb-3 d-none" id="edit_smartBoardField">
                        <label for="edit_smartboard_select" class="form-label small fw-semibold">Select Smart Board</label>
                        <select id="edit_smartboard_select" class="form-select form-control-clms">
                            <option value="">-- Select Smart Board Asset --</option>
                            <?php foreach($data['smartboards'] as $s): ?>
                                <option value="<?php echo $s->id; ?>"><?php echo e($s->asset_id); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_issue_type" class="form-label small fw-semibold">Service Scope</label>
                        <select name="issue_type" id="edit_issue_type" class="form-select form-control-clms" required>
                            <option value="preventative">Routine Preventative Check</option>
                            <option value="hardware_repair">Hardware Repair / Part Swap</option>
                            <option value="software_install">Software Setup / Re-imaging</option>
                            <option value="upgrade">Hardware Upgrade</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_assigned_technician" class="form-label small fw-semibold">Assigned Technician / Vendor</label>
                        <input type="text" name="assigned_technician" id="edit_assigned_technician" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_repair_date" class="form-label small fw-semibold">Servicing Date</label>
                        <input type="date" name="repair_date" id="edit_repair_date" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label small fw-semibold">Job Status</label>
                        <select name="status" id="edit_status" class="form-select form-control-clms" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress (Move asset to Maintenance)</option>
                            <option value="completed">Completed (Restore asset to Active)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label small fw-semibold">Servicing details / Technician Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control form-control-clms" rows="3"></textarea>
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
    // 1. ADD FORM SELECTOR TOGGLE
    var addType = document.getElementById('add_equipment_type');
    var addCompField = document.getElementById('add_computerField');
    var addSbField = document.getElementById('add_smartBoardField');
    var addCompSelect = document.getElementById('add_computer_select');
    var addSbSelect = document.getElementById('add_smartboard_select');

    addType.addEventListener('change', function() {
        toggleFields(this.value, addCompField, addSbField, addCompSelect, addSbSelect);
    });

    // 2. EDIT FORM SELECTOR TOGGLE
    var editType = document.getElementById('edit_equipment_type');
    var editCompField = document.getElementById('edit_computerField');
    var editSbField = document.getElementById('edit_smartBoardField');
    var editCompSelect = document.getElementById('edit_computer_select');
    var editSbSelect = document.getElementById('edit_smartboard_select');

    editType.addEventListener('change', function() {
        toggleFields(this.value, editCompField, editSbField, editCompSelect, editSbSelect);
    });

    function toggleFields(val, compF, sbF, compS, sbS) {
        if (val === 'computer') {
            compF.classList.remove('d-none');
            sbF.classList.add('d-none');
            compS.required = true;
            sbS.required = false;
            compS.name = 'equipment_id';
            sbS.name = '';
        } else if (val === 'smart_board') {
            compF.classList.add('d-none');
            sbF.classList.remove('d-none');
            compS.required = false;
            sbS.required = true;
            compS.name = '';
            sbS.name = 'equipment_id';
        } else {
            compF.classList.add('d-none');
            sbF.classList.add('d-none');
            compS.required = false;
            sbS.required = false;
            compS.name = '';
            sbS.name = '';
        }
    }

    // 3. WIRE EDIT MODAL BINDINGS
    const editBtns = document.querySelectorAll('.edit-btn');
    const editForm = document.getElementById('editForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editForm.action = '<?php echo URLROOT; ?>maintenance/update/' + id;
            
            const type = this.getAttribute('data-type');
            editType.value = type;
            toggleFields(type, editCompField, editSbField, editCompSelect, editSbSelect);

            const equipId = this.getAttribute('data-equipid') || '';
            if (type === 'computer') {
                editCompSelect.value = equipId;
            } else if (type === 'smart_board') {
                editSbSelect.value = equipId;
            }

            document.getElementById('edit_issue_type').value = this.getAttribute('data-issue');
            document.getElementById('edit_assigned_technician').value = this.getAttribute('data-tech');
            document.getElementById('edit_repair_date').value = this.getAttribute('data-date');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
            document.getElementById('edit_notes').value = this.getAttribute('data-notes') || '';
        });
    });
});
</script>
