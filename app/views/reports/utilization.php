<!-- Lab Utilization Report -->
<style>
@media print {
    .sidebar-clms, .navbar-clms, .no-print, .btn, footer, .breadcrumb-clms {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .card-clms {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    body {
        background-color: #fff !important;
        color: #000 !important;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <a href="<?php echo URLROOT; ?>report" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to Reports</a>
    <button onclick="window.print();" class="btn btn-primary btn-sm"><i class="bi bi-printer me-1"></i> Print / Save as PDF</button>
</div>

<!-- REPORT HEADER (MILITARY STYLED) -->
<div class="text-center mb-4">
    <h4 class="fw-bold m-0 text-primary">SRI LANKA AIR FORCE TRADE TRAINING SCHOOL EKALA</h4>
    <h5 class="fw-semibold text-secondary">Computer Laboratories Management System</h5>
    <h6 class="text-uppercase fw-bold border-bottom border-secondary pb-2 d-inline-block px-4">Laboratory Room Utilization Report</h6>
    <div class="small text-muted mt-1">Generated Date: <?php echo date('d F Y H:i'); ?> | Admin Operations Section</div>
</div>

<!-- UTILIZATION TABLE -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-house-gear-fill me-2 text-primary"></i> Lab Occupancy & Hours Breakdown</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-clms text-dark align-middle" style="border-color: var(--card-border) !important;">
            <thead>
                <tr class="table-secondary">
                    <th>Lab Code</th>
                    <th>Laboratory Name</th>
                    <th class="text-center">Workstation Capacity</th>
                    <th class="text-center">Allocated Sessions Count</th>
                    <th class="text-center">Total Scheduled Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['utilization'])): ?>
                    <?php foreach($data['utilization'] as $u): ?>
                        <tr>
                            <td><strong class="text-primary"><?php echo e($u->lab_code); ?></strong></td>
                            <td><span class="fw-semibold"><?php echo e($u->lab_name); ?></span></td>
                            <td class="text-center"><span class="badge bg-secondary text-dark fw-bold"><?php echo $u->capacity; ?> Workstations</span></td>
                            <td class="text-center fw-bold"><?php echo $u->session_count; ?></td>
                            <td class="text-center"><span class="badge bg-secondary px-3 py-2 text-dark fw-bold"><?php echo number_format($u->total_hours, 1); ?> Hours</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No laboratory utilization logs recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
