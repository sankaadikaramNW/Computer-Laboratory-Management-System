<div class="card-clms mb-4">
    <div class="card-clms-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold m-0"><i class="bi bi-clock-history text-primary me-2"></i> My Login & Security Activity</h5>
        <a href="<?php echo URLROOT; ?>auth/myLoginActivityReport" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF Report
        </a>
    </div>

    <!-- Search filter bar -->
    <div class="p-3 bg-light-subtle border-bottom border-color">
        <div class="row">
            <div class="col-md-4">
                <input type="text" id="logSearch" class="form-control form-control-clms form-control-sm" placeholder="Search by event, IP, details...">
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
        <table class="table table-hover table-clms align-middle" id="logsTable" style="table-layout: fixed; width: 100%;">
            <colgroup>
                <col style="width: 180px;">  <!-- Timestamp -->
                <col style="width: 150px;">  <!-- Event/Action -->
                <col style="width: 150px;">  <!-- IP Address -->
                <col style="width: auto;">   <!-- Details -->
            </colgroup>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Event / Action</th>
                    <th>IP Address</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['logs'])): ?>
                    <?php foreach($data['logs'] as $log): ?>
                        <?php
                            $badgeClass = 'bg-secondary';
                            if ($log->action === 'LOGIN') $badgeClass = 'bg-success';
                            if ($log->action === 'LOGOUT') $badgeClass = 'bg-info text-dark';
                            if ($log->action === 'ACCOUNT_LOCKED') $badgeClass = 'bg-danger';
                            if ($log->action === 'PASSWORD_CHANGED') $badgeClass = 'bg-warning text-dark';
                        ?>
                        <tr class="log-row">
                            <td class="text-nowrap small text-muted"><?php echo date('d M Y H:i:s', strtotime($log->created_at)); ?></td>
                            <td>
                                <span class="badge <?php echo $badgeClass; ?> text-uppercase px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.04em;">
                                    <?php echo e($log->action); ?>
                                </span>
                            </td>
                            <td>
                                <code class="small text-muted"><?php echo e($log->ip_address ?: '-'); ?></code>
                            </td>
                            <td>
                                <span class="small text-secondary" style="display: block; word-wrap: break-word; white-space: normal;">
                                    <?php echo e($log->details); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No security activity logs recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('logSearch');
    var rows = document.querySelectorAll('.log-row');

    searchInput.addEventListener('input', function() {
        var term = this.value.toLowerCase().trim();
        
        rows.forEach(row => {
            var text = row.innerText.toLowerCase();
            if (text.indexOf(term) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
