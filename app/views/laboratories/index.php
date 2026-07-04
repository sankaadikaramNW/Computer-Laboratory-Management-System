<!-- Laboratories View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-door-closed-fill text-primary me-2"></i> Laboratories Setup</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLabModal">
            <i class="bi bi-door-open me-1"></i> Add Laboratory
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Lab Code</th>
                    <th>Lab Name</th>
                    <th>Camp Location</th>
                    <th>Location</th>
                    <th>Seating Capacity</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['labs'])): ?>
                    <?php foreach($data['labs'] as $l): ?>
                        <tr>
                            <td><span class="fw-bold text-primary"><?php echo e($l->lab_code); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($l->lab_name); ?></span></td>
                            <td><span class="badge bg-info-light text-info fw-semibold"><i class="bi bi-geo-alt-fill me-1"></i><?php echo e($l->camp_name ?: 'Global'); ?></span></td>
                            <td><?php echo e($l->location); ?></td>
                            <td><span class="badge bg-secondary px-2 py-1"><?php echo e($l->capacity); ?> Workstations</span></td>
                            <td><small class="text-muted"><?php echo e($l->description ?: 'No description'); ?></small></td>
                            <td>
                                <?php if($l->status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $l->id; ?>"
                                            data-code="<?php echo e($l->lab_code); ?>"
                                            data-name="<?php echo e($l->lab_name); ?>"
                                            data-location="<?php echo e($l->location); ?>"
                                            data-capacity="<?php echo $l->capacity; ?>"
                                            data-desc="<?php echo e($l->description); ?>"
                                            data-status="<?php echo e($l->status); ?>"
                                            data-camp="<?php echo $l->camp_id; ?>"
                                            data-bs-toggle="modal" data-bs-target="#editLabModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>laboratory/delete/<?php echo $l->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this laboratory? Note: This will fail if classes are allocated in it.');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No laboratories configured yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD LABORATORY MODAL -->
<div class="modal fade" id="addLabModal" tabindex="-1" aria-labelledby="addLabModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addLabModalLabel"><i class="bi bi-door-open me-2 text-primary"></i> Add Laboratory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>laboratory/create" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_camp_id" class="form-label small fw-semibold">Camp Location</label>
                        <select name="camp_id" id="add_camp_id" class="form-select form-control-clms" required>
                            <?php foreach($data['camps'] as $camp): ?>
                                <option value="<?php echo $camp->id; ?>"><?php echo e($camp->name); ?> (<?php echo e($camp->code); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_lab_code" class="form-label small fw-semibold">Lab Code</label>
                        <input type="text" name="lab_code" id="add_lab_code" class="form-control form-control-clms" placeholder="e.g. LAB-01" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_lab_name" class="form-label small fw-semibold">Laboratory Name</label>
                        <input type="text" name="lab_name" id="add_lab_name" class="form-control form-control-clms" placeholder="e.g. Primary Computing Lab" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_location" class="form-label small fw-semibold">Location / Block</label>
                        <input type="text" name="location" id="add_location" class="form-control form-control-clms" placeholder="e.g. Main Block, Ground Floor" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_capacity" class="form-label small fw-semibold">Seating Capacity (Workstations)</label>
                        <input type="number" name="capacity" id="add_capacity" class="form-control form-control-clms" min="1" max="100" placeholder="e.g. 30" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_description" class="form-label small fw-semibold">Description / Notes</label>
                        <textarea name="description" id="add_description" class="form-control form-control-clms" rows="3" placeholder="Specify OS, equipment configurations, etc."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="add_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="add_status" class="form-select form-control-clms">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Laboratory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT LABORATORY MODAL -->
<div class="modal fade" id="editLabModal" tabindex="-1" aria-labelledby="editLabModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editLabModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Laboratory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_camp_id" class="form-label small fw-semibold">Camp Location</label>
                        <select name="camp_id" id="edit_camp_id" class="form-select form-control-clms" required>
                            <?php foreach($data['camps'] as $camp): ?>
                                <option value="<?php echo $camp->id; ?>"><?php echo e($camp->name); ?> (<?php echo e($camp->code); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_lab_code" class="form-label small fw-semibold">Lab Code</label>
                        <input type="text" name="lab_code" id="edit_lab_code" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_lab_name" class="form-label small fw-semibold">Laboratory Name</label>
                        <input type="text" name="lab_name" id="edit_lab_name" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_location" class="form-label small fw-semibold">Location / Block</label>
                        <input type="text" name="location" id="edit_location" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_capacity" class="form-label small fw-semibold">Seating Capacity (Workstations)</label>
                        <input type="number" name="capacity" id="edit_capacity" class="form-control form-control-clms" min="1" max="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label small fw-semibold">Description / Notes</label>
                        <textarea name="description" id="edit_description" class="form-control form-control-clms" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="edit_status" class="form-select form-control-clms">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
            editForm.action = '<?php echo URLROOT; ?>laboratory/update/' + id;
            
            document.getElementById('edit_camp_id').value = this.getAttribute('data-camp');
            document.getElementById('edit_lab_code').value = this.getAttribute('data-code');
            document.getElementById('edit_lab_name').value = this.getAttribute('data-name');
            document.getElementById('edit_location').value = this.getAttribute('data-location');
            document.getElementById('edit_capacity').value = this.getAttribute('data-capacity');
            document.getElementById('edit_description').value = this.getAttribute('data-desc');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });
});
</script>
