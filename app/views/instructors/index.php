<!-- Instructor Management View -->
<div class="card-clms mb-4 no-print">
    <h5 class="fw-bold mb-3"><i class="bi bi-funnel-fill text-primary me-2"></i> Search & Filters</h5>
    <form action="<?php echo URLROOT; ?>instructor" method="GET" class="row g-3">
        <div class="col-md-4">
            <label for="search" class="form-label small fw-semibold">Service Number / Name</label>
            <input type="text" name="search" id="search" class="form-control form-control-clms" placeholder="e.g. S-12345 or Wijesinghe" value="<?php echo e($_GET['search'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label for="rank" class="form-label small fw-semibold">Rank</label>
            <select name="rank" id="rank" class="form-select form-control-clms">
                <option value="">-- All Ranks --</option>
                <?php foreach($data['ranks'] as $r): ?>
                    <option value="<?php echo $r; ?>" <?php echo (isset($_GET['rank']) && $_GET['rank'] === $r) ? 'selected' : ''; ?>><?php echo $r; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="trade" class="form-label small fw-semibold">Trade</label>
            <input type="text" name="trade" id="trade" class="form-control form-control-clms" placeholder="e.g. IT Specialist" value="<?php echo e($_GET['trade'] ?? ''); ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-people-fill text-primary me-2"></i> Instructors Registry</h5>
        <button type="button" class="btn btn-primary btn-sm no-print" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add Instructor
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Service No</th>
                    <th>Rank</th>
                    <th>Full Name</th>
                    <th>Trade</th>
                    <th>Contact No</th>
                    <th>Email</th>
                    <th>Linked Login</th>
                    <th>Status</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['instructors'])): ?>
                    <?php foreach($data['instructors'] as $i): ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo e($i->service_no); ?></span></td>
                            <td><span class="badge bg-secondary"><?php echo e($i->rank); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($i->full_name); ?></span></td>
                            <td><?php echo e($i->trade); ?></td>
                            <td><?php echo e($i->contact_no ?: '-'); ?></td>
                            <td><?php echo e($i->email ?: '-'); ?></td>
                            <td>
                                <?php if($i->username): ?>
                                    <span class="small fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i><?php echo e($i->username); ?></span>
                                <?php else: ?>
                                    <span class="small text-muted"><i class="bi bi-dash-circle me-1"></i>Unlinked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($i->status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="no-print">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $i->id; ?>"
                                            data-service="<?php echo e($i->service_no); ?>"
                                            data-rank="<?php echo e($i->rank); ?>"
                                            data-name="<?php echo e($i->full_name); ?>"
                                            data-trade="<?php echo e($i->trade); ?>"
                                            data-contact="<?php echo e($i->contact_no); ?>"
                                            data-email="<?php echo e($i->email); ?>"
                                            data-userid="<?php echo $i->user_id; ?>"
                                            data-status="<?php echo e($i->status); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editInstructorModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>instructor/delete/<?php echo $i->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this instructor?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No instructor profiles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD INSTRUCTOR MODAL -->
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-labelledby="addInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addInstructorModalLabel"><i class="bi bi-person-plus-fill me-2 text-primary"></i> Add Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>instructor/create" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_service_no" class="form-label small fw-semibold">Service Number</label>
                        <input type="text" name="service_no" id="add_service_no" class="form-control form-control-clms" placeholder="e.g. S-12345" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_rank" class="form-label small fw-semibold">Rank</label>
                            <select name="rank" id="add_rank" class="form-select form-control-clms" required>
                                <option value="">-- Select Rank --</option>
                                <?php foreach($data['ranks'] as $r): ?>
                                    <option value="<?php echo $r; ?>"><?php echo $r; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_trade" class="form-label small fw-semibold">Trade</label>
                            <input type="text" name="trade" id="add_trade" class="form-control form-control-clms" placeholder="e.g. Signals / IT" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_full_name" class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="full_name" id="add_full_name" class="form-control form-control-clms" placeholder="e.g. Wijesinghe W.M." required>
                    </div>

                    <div class="mb-3">
                        <label for="add_contact_no" class="form-label small fw-semibold">Contact Number</label>
                        <input type="text" name="contact_no" id="add_contact_no" class="form-control form-control-clms" placeholder="e.g. 0771234567">
                    </div>

                    <div class="mb-3">
                        <label for="add_email" class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" id="add_email" class="form-control form-control-clms" placeholder="e.g. instructor@slaf.lk">
                    </div>

                    <div class="mb-3">
                        <label for="add_user_id" class="form-label small fw-semibold">Link User Login Account</label>
                        <select name="user_id" id="add_user_id" class="form-select form-control-clms">
                            <option value="">-- No linked login --</option>
                            <?php foreach($data['users'] as $u): ?>
                                <?php if($u->role_id == 2): ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo e($u->username); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
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
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT INSTRUCTOR MODAL -->
<div class="modal fade" id="editInstructorModal" tabindex="-1" aria-labelledby="editInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editInstructorModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Instructor Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_service_no" class="form-label small fw-semibold">Service Number</label>
                        <input type="text" name="service_no" id="edit_service_no" class="form-control form-control-clms" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_rank" class="form-label small fw-semibold">Rank</label>
                            <select name="rank" id="edit_rank" class="form-select form-control-clms" required>
                                <?php foreach($data['ranks'] as $r): ?>
                                    <option value="<?php echo $r; ?>"><?php echo $r; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_trade" class="form-label small fw-semibold">Trade</label>
                            <input type="text" name="trade" id="edit_trade" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_contact_no" class="form-label small fw-semibold">Contact Number</label>
                        <input type="text" name="contact_no" id="edit_contact_no" class="form-control form-control-clms">
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control form-control-clms">
                    </div>

                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label small fw-semibold">Link User Login Account</label>
                        <select name="user_id" id="edit_user_id" class="form-select form-control-clms">
                            <option value="">-- No linked login --</option>
                            <?php foreach($data['users'] as $u): ?>
                                <?php if($u->role_id == 2): ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo e($u->username); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
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
            editForm.action = '<?php echo URLROOT; ?>instructor/update/' + id;
            
            document.getElementById('edit_service_no').value = this.getAttribute('data-service');
            document.getElementById('edit_rank').value = this.getAttribute('data-rank');
            document.getElementById('edit_full_name').value = this.getAttribute('data-name');
            document.getElementById('edit_trade').value = this.getAttribute('data-trade');
            document.getElementById('edit_contact_no').value = this.getAttribute('data-contact');
            document.getElementById('edit_email').value = this.getAttribute('data-email');
            document.getElementById('edit_user_id').value = this.getAttribute('data-userid') || '';
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });
});
</script>
