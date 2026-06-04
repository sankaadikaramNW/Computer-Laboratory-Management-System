<!-- Smart Boards Registry View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-easel-fill text-primary me-2"></i> Smart Board Interactive Systems</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSmartBoardModal">
            <i class="bi bi-plus-circle me-1"></i> Register Smart Board
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>SmartBoard ID</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Installation Date</th>
                    <th>Assigned Laboratory</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['smartboards'])): ?>
                    <?php foreach($data['smartboards'] as $s): ?>
                        <tr>
                            <td><span class="fw-bold text-primary"><?php echo e($s->asset_id); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($s->brand); ?></span></td>
                            <td><?php echo e($s->model); ?></td>
                            <td>
                                <?php if($s->installation_date): ?>
                                    <?php echo date('d M Y', strtotime($s->installation_date)); ?>
                                <?php else: ?>
                                    <span class="text-muted small italic">Not recorded</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($s->lab_code): ?>
                                    <span class="badge bg-secondary"><?php echo e($s->lab_code); ?> - <?php echo e($s->lab_name); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic"><i class="bi bi-dash-circle me-1"></i>Unallocated</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($s->status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php elseif($s->status === 'faulty'): ?>
                                    <span class="badge badge-inactive bg-danger">Faulty</span>
                                <?php elseif($s->status === 'maintenance'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-wrench me-1"></i>Maint.</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Removed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $s->id; ?>"
                                            data-asset="<?php echo e($s->asset_id); ?>"
                                            data-brand="<?php echo e($s->brand); ?>"
                                            data-model="<?php echo e($s->model); ?>"
                                            data-installation="<?php echo e($s->installation_date); ?>"
                                            data-labid="<?php echo $s->lab_id; ?>"
                                            data-status="<?php echo e($s->status); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editSmartBoardModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>equipment/deleteSmartBoard/<?php echo $s->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this smart board?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No Smart Board devices configured.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- REGISTER SMART BOARD MODAL -->
<div class="modal fade" id="addSmartBoardModal" tabindex="-1" aria-labelledby="addSmartBoardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addSmartBoardModalLabel"><i class="bi bi-plus-circle me-2 text-primary"></i> Register Smart Board</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>equipment/createSmartBoard" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_asset_id" class="form-label small fw-semibold">Asset ID</label>
                        <input type="text" name="asset_id" id="add_asset_id" class="form-control form-control-clms" placeholder="e.g. SLAF-SB-001" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_brand" class="form-label small fw-semibold">Brand / Manufacturer</label>
                        <input type="text" name="brand" id="add_brand" class="form-control form-control-clms" placeholder="e.g. Promethean / ViewSonic" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_model" class="form-label small fw-semibold">Model / Size</label>
                        <input type="text" name="model" id="add_model" class="form-control form-control-clms" placeholder="e.g. ActivPanel 75-inch" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_installation_date" class="form-label small fw-semibold">Installation Date</label>
                        <input type="date" name="installation_date" id="add_installation_date" class="form-control form-control-clms">
                    </div>

                    <div class="mb-3">
                        <label for="add_lab_id" class="form-label small fw-semibold">Assign to Laboratory</label>
                        <select name="lab_id" id="add_lab_id" class="form-select form-control-clms">
                            <option value="">-- Leave Unallocated --</option>
                            <?php foreach($data['labs'] as $l): ?>
                                <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="add_status" class="form-select form-control-clms">
                            <option value="active">Active</option>
                            <option value="faulty">Faulty</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Smart Board</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT SMART BOARD MODAL -->
<div class="modal fade" id="editSmartBoardModal" tabindex="-1" aria-labelledby="editSmartBoardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editSmartBoardModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Smart Board Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_asset_id" class="form-label small fw-semibold">Asset ID</label>
                        <input type="text" name="asset_id" id="edit_asset_id" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_brand" class="form-label small fw-semibold">Brand / Manufacturer</label>
                        <input type="text" name="brand" id="edit_brand" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_model" class="form-label small fw-semibold">Model / Size</label>
                        <input type="text" name="model" id="edit_model" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_installation_date" class="form-label small fw-semibold">Installation Date</label>
                        <input type="date" name="installation_date" id="edit_installation_date" class="form-control form-control-clms">
                    </div>

                    <div class="mb-3">
                        <label for="edit_lab_id" class="form-label small fw-semibold">Assign to Laboratory</label>
                        <select name="lab_id" id="edit_lab_id" class="form-select form-control-clms">
                            <option value="">-- Leave Unallocated --</option>
                            <?php foreach($data['labs'] as $l): ?>
                                <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="edit_status" class="form-select form-control-clms">
                            <option value="active">Active</option>
                            <option value="faulty">Faulty</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="removed">Removed</option>
                        </select>
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
    const editBtns = document.querySelectorAll('.edit-btn');
    const editForm = document.getElementById('editForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editForm.action = '<?php echo URLROOT; ?>equipment/updateSmartBoard/' + id;
            
            document.getElementById('edit_asset_id').value = this.getAttribute('data-asset');
            document.getElementById('edit_brand').value = this.getAttribute('data-brand');
            document.getElementById('edit_model').value = this.getAttribute('data-model');
            document.getElementById('edit_installation_date').value = this.getAttribute('data-installation') || '';
            document.getElementById('edit_lab_id').value = this.getAttribute('data-labid') || '';
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });
});
</script>
