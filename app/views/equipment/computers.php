<!-- Computers Inventory View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-pc-display text-primary me-2"></i> Computer Workstations Inventory</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addComputerModal">
            <i class="bi bi-plus-circle me-1"></i> Register Workstation
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Asset No</th>
                    <th>Serial No</th>
                    <th>Specifications (Processor / RAM / Storage / OS)</th>
                    <th>Assigned Laboratory</th>
                    <th>Warranty</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['computers'])): ?>
                    <?php foreach($data['computers'] as $c): ?>
                        <tr>
                            <td><span class="fw-bold text-primary"><?php echo e($c->asset_no); ?></span></td>
                            <td><span class="small fw-semibold text-muted"><?php echo e($c->serial_no); ?></span></td>
                            <td>
                                <div>
                                    <strong class="small"><?php echo e($c->brand) . ' ' . e($c->model); ?></strong>
                                </div>
                                <span class="text-secondary small" style="font-size: 0.8rem;">
                                    <?php echo e($c->processor); ?> | <?php echo e($c->ram); ?> RAM | <?php echo e($c->storage); ?> | <?php echo e($c->os); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($c->lab_code): ?>
                                    <span class="badge bg-secondary"><?php echo e($c->lab_code); ?> - <?php echo e($c->lab_name); ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic"><i class="bi bi-dash-circle me-1"></i>Unallocated</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small"><?php echo e($c->warranty_status); ?></span>
                                <?php if($c->purchase_date): ?>
                                    <div class="text-muted" style="font-size: 0.7rem;">P: <?php echo date('d M Y', strtotime($c->purchase_date)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($c->status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php elseif($c->status === 'faulty'): ?>
                                    <span class="badge badge-inactive bg-danger">Faulty</span>
                                <?php elseif($c->status === 'maintenance'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-wrench me-1"></i>Maint.</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Removed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $c->id; ?>"
                                            data-asset="<?php echo e($c->asset_no); ?>"
                                            data-serial="<?php echo e($c->serial_no); ?>"
                                            data-brand="<?php echo e($c->brand); ?>"
                                            data-model="<?php echo e($c->model); ?>"
                                            data-processor="<?php echo e($c->processor); ?>"
                                            data-ram="<?php echo e($c->ram); ?>"
                                            data-storage="<?php echo e($c->storage); ?>"
                                            data-os="<?php echo e($c->os); ?>"
                                            data-purchase="<?php echo e($c->purchase_date); ?>"
                                            data-warranty="<?php echo e($c->warranty_status); ?>"
                                            data-labid="<?php echo $c->lab_id; ?>"
                                            data-status="<?php echo e($c->status); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editComputerModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>equipment/deleteComputer/<?php echo $c->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this computer from registry?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No computer workstations registered in inventory.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- REGISTER WORKSTATION MODAL -->
<div class="modal fade" id="addComputerModal" tabindex="-1" aria-labelledby="addComputerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addComputerModalLabel"><i class="bi bi-pc-display me-2 text-primary"></i> Register Workstation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>equipment/createComputer" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_asset_no" class="form-label small fw-semibold">Asset Number</label>
                            <input type="text" name="asset_no" id="add_asset_no" class="form-control form-control-clms" placeholder="e.g. SLAF-COMP-001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_serial_no" class="form-label small fw-semibold">Serial Number</label>
                            <input type="text" name="serial_no" id="add_serial_no" class="form-control form-control-clms" placeholder="e.g. S/N 8923497" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_brand" class="form-label small fw-semibold">Brand</label>
                            <input type="text" name="brand" id="add_brand" class="form-control form-control-clms" placeholder="e.g. HP / Dell" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_model" class="form-label small fw-semibold">Model</label>
                            <input type="text" name="model" id="add_model" class="form-control form-control-clms" placeholder="e.g. ProDesk 600 G3" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_processor" class="form-label small fw-semibold">Processor</label>
                            <input type="text" name="processor" id="add_processor" class="form-control form-control-clms" placeholder="e.g. Intel Core i5-7500" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_ram" class="form-label small fw-semibold">RAM Specs</label>
                            <input type="text" name="ram" id="add_ram" class="form-control form-control-clms" placeholder="e.g. 8GB DDR4" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_storage" class="form-label small fw-semibold">Storage Capacity</label>
                            <input type="text" name="storage" id="add_storage" class="form-control form-control-clms" placeholder="e.g. 256GB SSD" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_os" class="form-label small fw-semibold">Operating System</label>
                            <input type="text" name="os" id="add_os" class="form-control form-control-clms" placeholder="e.g. Windows 11 Pro / Ubuntu 22.04" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_purchase_date" class="form-label small fw-semibold">Purchase Date</label>
                            <input type="date" name="purchase_date" id="add_purchase_date" class="form-control form-control-clms">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_warranty_status" class="form-label small fw-semibold">Warranty Details</label>
                            <input type="text" name="warranty_status" id="add_warranty_status" class="form-control form-control-clms" placeholder="e.g. 3 Years Vendor Warranty" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_lab_id" class="form-label small fw-semibold">Assign to Laboratory</label>
                            <select name="lab_id" id="add_lab_id" class="form-select form-control-clms">
                                <option value="">-- Leave Unallocated --</option>
                                <?php foreach($data['labs'] as $l): ?>
                                    <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_status" class="form-label small fw-semibold">Status</label>
                            <select name="status" id="add_status" class="form-select form-control-clms">
                                <option value="active">Active</option>
                                <option value="faulty">Faulty</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Workstation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT WORKSTATION MODAL -->
<div class="modal fade" id="editComputerModal" tabindex="-1" aria-labelledby="editComputerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editComputerModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Workstation Specifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_asset_no" class="form-label small fw-semibold">Asset Number</label>
                            <input type="text" name="asset_no" id="edit_asset_no" class="form-control form-control-clms" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_serial_no" class="form-label small fw-semibold">Serial Number</label>
                            <input type="text" name="serial_no" id="edit_serial_no" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_brand" class="form-label small fw-semibold">Brand</label>
                            <input type="text" name="brand" id="edit_brand" class="form-control form-control-clms" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_model" class="form-label small fw-semibold">Model</label>
                            <input type="text" name="model" id="edit_model" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_processor" class="form-label small fw-semibold">Processor</label>
                            <input type="text" name="processor" id="edit_processor" class="form-control form-control-clms" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_ram" class="form-label small fw-semibold">RAM Specs</label>
                            <input type="text" name="ram" id="edit_ram" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_storage" class="form-label small fw-semibold">Storage Capacity</label>
                            <input type="text" name="storage" id="edit_storage" class="form-control form-control-clms" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_os" class="form-label small fw-semibold">Operating System</label>
                            <input type="text" name="os" id="edit_os" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_purchase_date" class="form-label small fw-semibold">Purchase Date</label>
                            <input type="date" name="purchase_date" id="edit_purchase_date" class="form-control form-control-clms">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_warranty_status" class="form-label small fw-semibold">Warranty Details</label>
                            <input type="text" name="warranty_status" id="edit_warranty_status" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_lab_id" class="form-label small fw-semibold">Assign to Laboratory</label>
                            <select name="lab_id" id="edit_lab_id" class="form-select form-control-clms">
                                <option value="">-- Leave Unallocated --</option>
                                <?php foreach($data['labs'] as $l): ?>
                                    <option value="<?php echo $l->id; ?>"><?php echo e($l->lab_code); ?> - <?php echo e($l->lab_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label small fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select form-control-clms">
                                <option value="active">Active</option>
                                <option value="faulty">Faulty</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="removed">Removed</option>
                            </select>
                        </div>
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
            editForm.action = '<?php echo URLROOT; ?>equipment/updateComputer/' + id;
            
            document.getElementById('edit_asset_no').value = this.getAttribute('data-asset');
            document.getElementById('edit_serial_no').value = this.getAttribute('data-serial');
            document.getElementById('edit_brand').value = this.getAttribute('data-brand');
            document.getElementById('edit_model').value = this.getAttribute('data-model');
            document.getElementById('edit_processor').value = this.getAttribute('data-processor');
            document.getElementById('edit_ram').value = this.getAttribute('data-ram');
            document.getElementById('edit_storage').value = this.getAttribute('data-storage');
            document.getElementById('edit_os').value = this.getAttribute('data-os');
            document.getElementById('edit_purchase_date').value = this.getAttribute('data-purchase') || '';
            document.getElementById('edit_warranty_status').value = this.getAttribute('data-warranty');
            document.getElementById('edit_lab_id').value = this.getAttribute('data-labid') || '';
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });
});
</script>
