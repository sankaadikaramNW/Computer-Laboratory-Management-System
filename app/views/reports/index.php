<!-- Reports Selector Dashboard View -->
<div class="row g-4">
    <!-- Inventory Audit Card -->
    <div class="col-md-4">
        <div class="card-clms h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-circle bg-primary-subtle me-3">
                        <i class="bi bi-pc-display text-primary fs-3"></i>
                    </div>
                    <h5 class="fw-bold m-0 text-primary">Hardware Inventory</h5>
                </div>
                <p class="text-muted small">Generates comprehensive audits of computer workstations, tech specs, warranty details, smart board allocations, and status breakdowns (faulty/active).</p>
            </div>
            <a href="<?php echo URLROOT; ?>report/inventory" class="btn btn-primary btn-sm mt-3 w-100 fw-semibold">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Generate Inventory Report
            </a>
        </div>
    </div>

    <!-- Instructor Workload Card -->
    <div class="col-md-4">
        <div class="card-clms h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-circle bg-warning-subtle me-3">
                        <i class="bi bi-people-fill text-warning fs-3"></i>
                    </div>
                    <h5 class="fw-bold m-0 text-warning">Instructor Workload</h5>
                </div>
                <p class="text-muted small">Tracks scheduled lecture sessions, duration metrics, and cumulative hours taught by each instructor rank. Perfect for staff workload audits.</p>
            </div>
            <a href="<?php echo URLROOT; ?>report/workload" class="btn btn-warning text-dark btn-sm mt-3 w-100 fw-semibold">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Generate Workload Report
            </a>
        </div>
    </div>

    <!-- Lab Utilization Card -->
    <div class="col-md-4">
        <div class="card-clms h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 rounded-circle bg-success-subtle me-3">
                        <i class="bi bi-house-gear-fill text-success fs-3"></i>
                    </div>
                    <h5 class="fw-bold m-0 text-success">Lab Utilization</h5>
                </div>
                <p class="text-muted small">Aggregates occupancy rates, total scheduled hours, and session frequency per room. Helps optimize scheduling capacity across computing labs.</p>
            </div>
            <a href="<?php echo URLROOT; ?>report/utilization" class="btn btn-success btn-sm mt-3 w-100 fw-semibold">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Generate Utilization Report
            </a>
        </div>
    </div>
</div>
