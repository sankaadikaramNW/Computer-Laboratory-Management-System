<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Register Instructor Profile & System Account</h5>
        <a href="<?php echo URLROOT; ?>instructor" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Instructors
        </a>
    </div>

    <!-- Error Alerts -->
    <?php if(!empty($data['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Registration Validation Failed:</h6>
            <ul class="mb-0 ps-3">
                <?php foreach($data['errors'] as $err): ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>instructor/register" method="POST" class="needs-validation">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="row">
            <!-- 1. PERSONAL INFORMATION CARD -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border border-color rounded-3" style="background:var(--bg-card);">
                    <div class="card-header bg-light border-bottom border-color py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-badge-fill me-2"></i>Personal Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="service_no" class="form-label">Service Number <span class="text-danger">*</span></label>
                                <input type="text" name="service_no" id="service_no" class="form-control" 
                                       placeholder="e.g. S-12345" 
                                       value="<?php echo e($data['old']['service_no'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="rank" class="form-label">Rank <span class="text-danger">*</span></label>
                                <select name="rank" id="rank" class="form-select" required>
                                    <option value="">Choose Rank...</option>
                                    <?php foreach($data['ranks'] as $rank): ?>
                                        <option value="<?php echo $rank; ?>" <?php echo (isset($data['old']['rank']) && $data['old']['rank'] === $rank) ? 'selected' : ''; ?>><?php echo $rank; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="full_name" class="form-control" 
                                       placeholder="e.g. Perera A.B." 
                                       value="<?php echo e($data['old']['full_name'] ?? ''); ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="trade" class="form-label">Trade / Branch</label>
                                <input type="text" name="trade" id="trade" class="form-control" 
                                       placeholder="e.g. IT Specialist or Signals" 
                                       value="<?php echo e($data['old']['trade'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="contact_no" class="form-label">Contact Number</label>
                                <input type="tel" name="contact_no" id="contact_no" class="form-control" 
                                       placeholder="e.g. 0771234567" 
                                       value="<?php echo e($data['old']['contact_no'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       placeholder="e.g. ab.perera@slaf.lk" 
                                       value="<?php echo e($data['old']['email'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SYSTEM ACCOUNT INFORMATION CARD -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border border-color rounded-3" style="background:var(--bg-card);">
                    <div class="card-header bg-light border-bottom border-color py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-shield-lock-fill me-2"></i>System Account Details</h6>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="create_login" id="create_login" value="1" 
                                   <?php echo (!isset($data['old']) || isset($data['old']['create_login'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-semibold text-muted" for="create_login">Create Login Account</label>
                        </div>
                    </div>
                    <div class="card-body p-4" id="system-account-fields">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username" id="username" class="form-control" 
                                           placeholder="e.g. ab.perera" 
                                           value="<?php echo e($data['old']['username'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="temp_password" class="form-label">Temporary Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="text" name="temp_password" id="temp_password" class="form-control" 
                                           placeholder="Minimum 8 characters" 
                                           value="<?php echo e($data['old']['temp_password'] ?? ''); ?>">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-generate-pw" title="Generate password">
                                        <i class="bi bi-arrow-repeat"></i> Auto-Gen
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="account_status" class="form-label">Account Status</label>
                                <select name="account_status" id="account_status" class="form-select">
                                    <option value="active" <?php echo (isset($data['old']['account_status']) && $data['old']['account_status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo (isset($data['old']['account_status']) && $data['old']['account_status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="locked" <?php echo (isset($data['old']['account_status']) && $data['old']['account_status'] === 'locked') ? 'selected' : ''; ?>>Locked</option>
                                </select>
                            </div>

                            <div class="col-12 mt-4 pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="force_change" id="force_change" value="1" 
                                           <?php echo (!isset($data['old']) || isset($data['old']['force_change'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-semibold" for="force_change">
                                        Force Password Change on First Login
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-center text-muted d-none" id="no-account-notice">
                        <i class="bi bi-person-slash fs-2 d-block mb-3"></i>
                        <p class="mb-0">No login account will be generated. The instructor profile will exist, but they will not be able to sign in to the platform.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-3">
            <a href="<?php echo URLROOT; ?>instructor" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
            <button type="submit" class="btn btn-primary py-2 px-4">
                <i class="bi bi-save me-1"></i> Register Instructor
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const createLoginSwitch = document.getElementById('create_login');
    const systemFields = document.getElementById('system-account-fields');
    const noticeField = document.getElementById('no-account-notice');
    
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('temp_password');
    const statusSelect = document.getElementById('account_status');
    const forceChangeSwitch = document.getElementById('force_change');

    function toggleAccountFields() {
        if (createLoginSwitch.checked) {
            systemFields.classList.remove('d-none');
            noticeField.classList.add('d-none');
            usernameInput.required = true;
            passwordInput.required = true;
        } else {
            systemFields.classList.add('d-none');
            noticeField.classList.remove('d-none');
            usernameInput.required = false;
            passwordInput.required = false;
        }
    }

    createLoginSwitch.addEventListener('change', toggleAccountFields);
    toggleAccountFields(); // Run initially

    // Password Generation Helper
    document.getElementById('btn-generate-pw').addEventListener('click', function() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let pass = "";
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordInput.value = pass;
    });

    // Auto-generate username from full name
    document.getElementById('full_name').addEventListener('blur', function() {
        if (usernameInput.value === '' && this.value !== '') {
            // e.g. "Perera A.B." -> "perera.ab"
            let raw = this.value.toLowerCase().trim();
            // remove special chars except spaces/dots
            raw = raw.replace(/[^a-z0-9 .]/g, '');
            // replace spaces with dots
            let clean = raw.replace(/\s+/g, '.');
            usernameInput.value = clean;
        }
    });
});
</script>
