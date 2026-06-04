<!-- Instructor profile view -->
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0"><i class="bi bi-person-gear text-primary me-2"></i> Update Contact Details</h5>
            </div>

            <!-- Profile Info Banner -->
            <div class="p-3 bg-light-subtle rounded border border-color mb-4" style="background-color: rgba(69, 110, 157, 0.03) !important;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check text-primary fs-3 me-3"></i>
                    <div>
                        <h6 class="mb-0 fw-bold"><?php echo e($data['instructor']->rank) . ' ' . e($data['instructor']->full_name); ?></h6>
                        <span class="small text-muted">Service No: <?php echo e($data['instructor']->service_no); ?> | Trade: <?php echo e($data['instructor']->trade); ?></span>
                    </div>
                </div>
            </div>

            <form action="<?php echo URLROOT; ?>instructor/profile" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <!-- Locked Service Fields -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Service Number (Read-only)</label>
                        <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['instructor']->service_no); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Rank (Read-only)</label>
                        <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['instructor']->rank); ?>" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Trade (Read-only)</label>
                    <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['instructor']->trade); ?>" disabled>
                </div>

                <hr class="border-color my-4">

                <!-- Editable Fields -->
                <div class="mb-3">
                    <label for="contact_no" class="form-label small fw-bold">Contact Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone-fill text-secondary"></i></span>
                        <input type="text" name="contact_no" id="contact_no" class="form-control form-control-clms" value="<?php echo e($data['instructor']->contact_no); ?>" placeholder="e.g. 0771234567" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label small fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill text-secondary"></i></span>
                        <input type="email" name="email" id="email" class="form-control form-control-clms" value="<?php echo e($data['instructor']->email); ?>" placeholder="e.g. instructor@slaf.lk" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Contact details are visible to schedulers.</span>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Contact Details</button>
                </div>
            </form>
        </div>
    </div>
</div>
