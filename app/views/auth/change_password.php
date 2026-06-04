<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - SLAF CLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>css/style.css">
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    </script>
</head>
<body>

<div class="login-container">

    <!-- LEFT PANEL — Branding -->
    <div class="login-left">
        <i class="bi bi-shield-fill-check mb-4" style="font-size:4rem;color:rgba(255,255,255,0.9);"></i>
        <h1>Security Policy</h1>
        <p class="mt-2">SLAF Trade Training School CLMS</p>
        <div class="mt-4 d-flex flex-column gap-3" style="max-width:300px;text-align:left;">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-shield-lock-fill mt-1" style="color:rgba(255,255,255,0.75);font-size:1.1rem;"></i>
                <span style="color:rgba(255,255,255,0.8);font-size:0.9rem;">Passwords must be at least 8 characters long.</span>
            </div>
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-arrow-repeat mt-1" style="color:rgba(255,255,255,0.75);font-size:1.1rem;"></i>
                <span style="color:rgba(255,255,255,0.8);font-size:0.9rem;">You cannot reuse any previously used password.</span>
            </div>
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-hourglass-split mt-1" style="color:rgba(255,255,255,0.75);font-size:1.1rem;"></i>
                <span style="color:rgba(255,255,255,0.8);font-size:0.9rem;">Passwords expire periodically to ensure account safety.</span>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL — Form -->
    <div class="login-right">
        <div class="login-card">

            <div class="login-header">
                <i class="bi bi-key-fill" style="font-size:2.2rem;color:var(--primary);"></i>
                <h4 class="mt-3 mb-1">Update Password</h4>
                <p>Change your password to secure your account</p>
            </div>

            <?php flash('change_password_warning'); ?>
            <?php flash('change_password_error'); ?>

            <form action="<?php echo URLROOT; ?>auth/changePassword" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg-page);border:1.5px solid var(--border);border-right:none;">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input type="password" name="current_password" id="current_password"
                            class="form-control" style="border-left:none;"
                            placeholder="Current Password" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg-page);border:1.5px solid var(--border);border-right:none;">
                            <i class="bi bi-key-fill text-muted"></i>
                        </span>
                        <input type="password" name="new_password" id="new_password"
                            class="form-control" style="border-left:none;"
                            placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg-page);border:1.5px solid var(--border);border-right:none;">
                            <i class="bi bi-check-circle-fill text-muted"></i>
                        </span>
                        <input type="password" name="confirm_password" id="confirm_password"
                            class="form-control" style="border-left:none;"
                            placeholder="Re-type new password" required>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary py-2" style="font-size:0.95rem;letter-spacing:0.3px;">
                        <i class="bi bi-shield-check me-2"></i>Change Password
                    </button>
                </div>

                <div class="text-center">
                    <a href="<?php echo URLROOT; ?>auth/logout" class="text-decoration-none small text-danger">
                        <i class="bi bi-box-arrow-left me-1"></i>
                        Cancel and Logout
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
