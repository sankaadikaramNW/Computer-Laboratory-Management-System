<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SLAF CLMS</title>
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
        <!-- SLAF Crest -->
        <img src="<?php echo URLROOT; ?>images/Picture1.png"
             alt="SLAF Crest"
             style="width:110px;height:110px;object-fit:contain;filter:drop-shadow(0 4px 16px rgba(0,0,0,0.35));margin-bottom:1.5rem;"
             onerror="this.style.display='none';document.getElementById('crest-fallback').style.display='block';"
        >
        <i id="crest-fallback" class="bi bi-shield-fill-check" style="display:none;font-size:4rem;color:rgba(255,255,255,0.9);margin-bottom:1.5rem;"></i>
        <h1>SLAF Trade Training School</h1>
        <p class="mt-2">Computer Laboratory Management System — Ekala</p>
        <div class="mt-4 d-flex flex-column gap-3" style="max-width:300px;text-align:left;">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-calendar-check-fill mt-1" style="color:rgba(255,255,255,0.75);font-size:1.1rem;"></i>
                <span style="color:rgba(255,255,255,0.8);font-size:0.9rem;">Schedule &amp; manage laboratory allocations</span>
            </div>
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-pc-display-horizontal mt-1" style="color:rgba(255,255,255,0.75);font-size:1.1rem;"></i>
                <span style="color:rgba(255,255,255,0.8);font-size:0.9rem;">Track equipment, faults &amp; maintenance</span>
            </div>
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-people-fill mt-1" style="color:rgba(255,255,255,0.75);font-size:1.1rem;"></i>
                <span style="color:rgba(255,255,255,0.8);font-size:0.9rem;">Manage instructors and syllabus lessons</span>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL — Login Form -->
    <div class="login-right">
        <div class="login-card">

            <div class="login-header">
                <img src="<?php echo URLROOT; ?>images/Picture1.png"
                     alt="SLAF Crest"
                     style="width:64px;height:64px;object-fit:contain;"
                     onerror="this.outerHTML='<i class=\'bi bi-grid-3x3-gap-fill\' style=\'font-size:2.2rem;color:var(--primary);\'></i>';"
                >
                <h4 class="mt-3 mb-1">Welcome back</h4>
                <p>Sign in to your SLAF CLMS account</p>
            </div>

            <?php flash('login_error'); ?>
            <?php flash('login_success'); ?>

            <form action="<?php echo URLROOT; ?>auth/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg-page);border:1.5px solid var(--border);border-right:none;">
                            <i class="bi bi-person-fill text-muted"></i>
                        </span>
                        <input type="text" name="username" id="username"
                            class="form-control"
                            style="border-left:none;border:1.5px solid var(--border);border-left:none;"
                            placeholder="e.g. admin or instructor" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--bg-page);border:1.5px solid var(--border);border-right:none;">
                            <i class="bi bi-lock-fill text-muted"></i>
                        </span>
                        <input type="password" name="password" id="password"
                            class="form-control"
                            style="border-left:none;"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary py-2" style="font-size:0.95rem;letter-spacing:0.3px;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </div>

                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Unauthorized access is strictly prohibited and logged.
                    </small>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
