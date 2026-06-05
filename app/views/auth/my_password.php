<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0">
                    <i class="bi bi-key-fill text-primary me-2"></i>Change My Password
                </h5>
            </div>

            <!-- Password Policy Info Banner -->
            <div class="alert alert-info border-0 mb-4 d-flex gap-3 align-items-start" style="background:var(--primary-light);border-radius:10px;">
                <i class="bi bi-shield-lock-fill text-primary mt-1 fs-5 flex-shrink-0"></i>
                <div class="small">
                    <strong>Password Policy:</strong><br>
                    Passwords must be <strong>at least 8 characters</strong>. You cannot reuse a previously used password.
                </div>
            </div>

            <?php flash('change_password_error'); ?>
            <?php flash('change_password_warning'); ?>

            <form action="<?php echo URLROOT; ?>auth/myPassword" method="POST" id="myPasswordForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold">
                        <i class="bi bi-lock-fill me-1 text-secondary"></i>Current Password
                    </label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="current_password"
                               class="form-control form-control-clms"
                               placeholder="Enter your current password" required autofocus>
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('current_password', this)" title="Toggle visibility">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label fw-semibold">
                        <i class="bi bi-key-fill me-1 text-secondary"></i>New Password
                    </label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="new_password"
                               class="form-control form-control-clms"
                               placeholder="Minimum 8 characters" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('new_password', this)" title="Toggle visibility">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <!-- Strength Indicator -->
                    <div class="mt-2">
                        <div class="progress" style="height:5px;border-radius:10px;">
                            <div class="progress-bar" id="strength-bar" role="progressbar" style="width:0%;transition:width 0.3s,background 0.3s;"></div>
                        </div>
                        <small id="strength-label" class="text-muted d-block mt-1"></small>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label fw-semibold">
                        <i class="bi bi-check-circle-fill me-1 text-secondary"></i>Confirm New Password
                    </label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password"
                               class="form-control form-control-clms"
                               placeholder="Re-type new password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('confirm_password', this)" title="Toggle visibility">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <div id="match-feedback" class="small mt-1"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center gap-3 mt-4">
                    <?php if (isAdmin()): ?>
                        <a href="<?php echo URLROOT; ?>dashboard/admin" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                    <?php else: ?>
                        <a href="<?php echo URLROOT; ?>dashboard/instructor" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary px-4" id="submit-btn">
                        <i class="bi bi-shield-check me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function toggleVis(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
    }
}

// Password Strength
document.getElementById('new_password').addEventListener('input', function () {
    const val = this.value;
    const bar = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const levels = [
        { width: '0%', color: '', text: '' },
        { width: '25%', color: '#ef4444', text: 'Weak' },
        { width: '50%', color: '#f97316', text: 'Fair' },
        { width: '75%', color: '#eab308', text: 'Good' },
        { width: '100%', color: '#22c55e', text: 'Strong' },
    ];
    const lvl = levels[strength];
    bar.style.width = lvl.width;
    bar.style.backgroundColor = lvl.color;
    label.textContent = lvl.text ? 'Strength: ' + lvl.text : '';
    label.style.color = lvl.color;
});

// Confirm password match
document.getElementById('confirm_password').addEventListener('input', function () {
    const newPw = document.getElementById('new_password').value;
    const feedback = document.getElementById('match-feedback');
    if (this.value === '') {
        feedback.textContent = '';
        return;
    }
    if (this.value === newPw) {
        feedback.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i><span class="text-success">Passwords match</span>';
    } else {
        feedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger me-1"></i><span class="text-danger">Passwords do not match</span>';
    }
});

// Client-side validation
document.getElementById('myPasswordForm').addEventListener('submit', function (e) {
    const newPw = document.getElementById('new_password').value;
    const confirmPw = document.getElementById('confirm_password').value;
    if (newPw !== confirmPw) {
        e.preventDefault();
        document.getElementById('confirm_password').classList.add('is-invalid');
        alert('New passwords do not match. Please check and try again.');
    }
});
</script>
