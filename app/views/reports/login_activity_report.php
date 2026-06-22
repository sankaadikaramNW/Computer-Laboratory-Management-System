<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? e($data['title']) : 'User Login Activity Report'; ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #fff !important;
            color: #000 !important;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            padding: 30px;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .badge-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-info { background-color: #cff4fc; color: #055160; border: 1px solid #b6effb; }
        .badge-danger { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .badge-warning { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
        .badge-secondary { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8; }

        .table-clms {
            font-size: 0.85rem;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <button onclick="window.close();" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg me-1"></i> Close Tab</button>
    <button onclick="window.print();" class="btn btn-primary btn-sm"><i class="bi bi-printer me-1"></i> Print / Save as PDF</button>
</div>

<!-- REPORT HEADER (MILITARY STYLED) -->
<div class="report-header">
    <h4 class="fw-bold m-0 text-dark">SRI LANKA AIR FORCE TRADE TRAINING SCHOOL EKALA</h4>
    <h5 class="fw-semibold text-secondary">Computer Laboratories Management System</h5>
    <h6 class="text-uppercase fw-bold pb-2 d-inline-block mt-2">User Login & Security Audit Report</h6>
    <div class="small text-muted mt-1">Generated Date: <?php echo date('d F Y H:i'); ?> | Admin Operations Section</div>
</div>

<!-- TARGET USER PROFILE DETAILS -->
<div class="row mb-4">
    <div class="col-md-6 col-6">
        <div class="card p-3 border-dark-subtle">
            <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Target User Account</div>
            <div class="fw-bold text-dark fs-5 text-capitalize"><?php echo e($data['target_user']); ?></div>
        </div>
    </div>
    <div class="col-md-6 col-6">
        <div class="card p-3 border-dark-subtle">
            <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Access Role / Scope</div>
            <div class="fw-bold text-dark fs-5 text-capitalize"><?php echo e($data['target_role']); ?></div>
        </div>
    </div>
</div>

<!-- AUDIT LOGS TABLE -->
<div class="card border-dark-subtle mb-4">
    <div class="card-header bg-light border-dark-subtle">
        <h5 class="fw-bold m-0 text-dark" style="font-size: 1rem;"><i class="bi bi-shield-lock-fill me-2"></i> Security Event Logs (Last 100 Entries)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-clms text-dark align-middle mb-0" style="border-color: #dee2e6 !important;">
            <thead>
                <tr class="table-secondary">
                    <th style="width: 200px;">Timestamp</th>
                    <th style="width: 150px;">Event Action</th>
                    <th style="width: 150px;">IP Address</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['logs'])): ?>
                    <?php foreach($data['logs'] as $log): ?>
                        <?php
                            $badgeClass = 'badge-secondary';
                            if ($log->action === 'LOGIN') $badgeClass = 'badge-success';
                            if ($log->action === 'LOGOUT') $badgeClass = 'badge-info';
                            if ($log->action === 'ACCOUNT_LOCKED') $badgeClass = 'badge-danger';
                            if ($log->action === 'PASSWORD_CHANGED') $badgeClass = 'badge-warning';
                        ?>
                        <tr>
                            <td><small class="text-muted fw-semibold"><?php echo date('d M Y H:i:s', strtotime($log->created_at)); ?></small></td>
                            <td>
                                <span class="badge <?php echo $badgeClass; ?> text-uppercase px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">
                                    <?php echo e($log->action); ?>
                                </span>
                            </td>
                            <td>
                                <code class="small text-muted"><?php echo e($log->ip_address ?: '-'); ?></code>
                            </td>
                            <td>
                                <span class="small text-secondary"><?php echo e($log->details); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No login activities recorded for this user account.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Automatically trigger printing/PDF save when the page is loaded
    window.addEventListener('DOMContentLoaded', (event) => {
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>

</body>
</html>
