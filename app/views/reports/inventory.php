<!-- Hardware Inventory Report -->
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
    <h6 class="text-uppercase fw-bold border-bottom border-secondary pb-2 d-inline-block px-4">Hardware & Workstations Inventory Audit Report</h6>
    <div class="small text-muted mt-1">Generated Date: <?php echo date('d F Y H:i'); ?> | System Administrator Section</div>
</div>

<!-- STATS SUMMARY ROWS -->
<?php
$totalComps = count($data['computers']);
$activeComps = 0; $faultyComps = 0; $maintComps = 0;
foreach($data['computers'] as $c) {
    if($c->status === 'active') $activeComps++;
    elseif($c->status === 'faulty') $faultyComps++;
    elseif($c->status === 'maintenance') $maintComps++;
}
$totalSB = count($data['smartboards']);
?>
<div class="row g-3 mb-4 text-center">
    <div class="col-md-2 col-6">
        <div class="border border-color p-3 rounded" style="background-color: var(--card-bg);">
            <div class="small text-muted fw-semibold">Total Workstations</div>
            <h3 class="fw-bold text-primary m-0"><?php echo $totalComps; ?></h3>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="border border-color p-3 rounded" style="background-color: var(--card-bg);">
            <div class="small text-muted fw-semibold">Active Stations</div>
            <h3 class="fw-bold text-success m-0"><?php echo $activeComps; ?></h3>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="border border-color p-3 rounded" style="background-color: var(--card-bg);">
            <div class="small text-muted fw-semibold">Faulty Stations</div>
            <h3 class="fw-bold text-danger m-0"><?php echo $faultyComps; ?></h3>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="border border-color p-3 rounded" style="background-color: var(--card-bg);">
            <div class="small text-muted fw-semibold">In Servicing</div>
            <h3 class="fw-bold text-warning m-0"><?php echo $maintComps; ?></h3>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="border border-color p-3 rounded" style="background-color: var(--card-bg);">
            <div class="small text-muted fw-semibold">Total Smart Boards</div>
            <h3 class="fw-bold text-primary m-0"><?php echo $totalSB; ?> Interactive Screens</h3>
        </div>
    </div>
</div>

<!-- COMPUTERS LIST -->
<div class="card-clms mb-4">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-pc-display me-2 text-primary"></i> Workstations Inventory</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-clms text-dark align-middle" style="border-color: var(--card-border) !important;">
            <thead>
                <tr class="table-secondary">
                    <th>Asset ID</th>
                    <th>Specifications</th>
                    <th>Assigned Lab</th>
                    <th>Warranty Info</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['computers'] as $c): ?>
                    <tr>
                        <td><strong><?php echo e($c->asset_no); ?></strong><br><small class="text-muted"><?php echo e($c->serial_no); ?></small></td>
                        <td>
                            <strong><?php echo e($c->brand) . ' ' . e($c->model); ?></strong><br>
                            <span class="small text-secondary"><?php echo e($c->processor); ?> | <?php echo e($c->ram); ?> RAM | <?php echo e($c->storage); ?> | <?php echo e($c->os); ?></span>
                        </td>
                        <td><?php echo $c->lab_code ? e($c->lab_code) . ' - ' . e($c->lab_name) : '<span class="text-muted">Unallocated</span>'; ?></td>
                        <td><?php echo e($c->warranty_status); ?></td>
                        <td><span class="text-uppercase small fw-bold text-primary"><?php echo e($c->status); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SMARTBOARDS LIST -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-easel-fill me-2 text-primary"></i> Smart Board Interactive Systems</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-clms text-dark align-middle" style="border-color: var(--card-border) !important;">
            <thead>
                <tr class="table-secondary">
                    <th>SmartBoard ID</th>
                    <th>Brand & Model</th>
                    <th>Assigned Lab</th>
                    <th>Installation Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['smartboards'] as $s): ?>
                    <tr>
                        <td><strong><?php echo e($s->asset_id); ?></strong></td>
                        <td><?php echo e($s->brand) . ' ' . e($s->model); ?></td>
                        <td><?php echo $s->lab_code ? e($s->lab_code) . ' - ' . e($s->lab_name) : '<span class="text-muted">Unallocated</span>'; ?></td>
                        <td><?php echo $s->installation_date ? date('d M Y', strtotime($s->installation_date)) : '-'; ?></td>
                        <td><span class="text-uppercase small fw-bold text-primary"><?php echo e($s->status); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
