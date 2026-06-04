<!-- Audit Logs Registry View -->
<div class="card-clms mb-4">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i> System Activity Logs</h5>
        <form action="<?php echo URLROOT; ?>audit/clear" method="POST" onsubmit="return confirm('Are you sure you want to delete all audit logs older than 90 days?');">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash3 me-1"></i> Purge Logs > 90 Days
            </button>
        </form>
    </div>

    <!-- Quick Search bar -->
    <div class="p-3 bg-light-subtle border-bottom border-color">
        <div class="row">
            <div class="col-md-4">
                <input type="text" id="logSearch" class="form-control form-control-clms form-control-sm" placeholder="Type to filter action, user, module...">
            </div>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
        <table class="table table-hover table-clms align-middle" id="logsTable">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User Account</th>
                    <th>Scope</th>
                    <th>Action Event</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['logs'])): ?>
                    <?php foreach($data['logs'] as $log): ?>
                        <tr class="log-row">
                            <td class="text-nowrap small text-muted"><?php echo date('d M Y H:i:s', strtotime($log->created_at)); ?></td>
                            <td>
                                <strong class="small"><?php echo e($log->username ?: 'System'); ?></strong>
                                <?php if($log->role_name): ?>
                                    <span class="badge bg-secondary text-capitalize" style="font-size: 0.65rem;"><?php echo e($log->role_name); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-outline-primary border small text-uppercase"><?php echo e($log->module); ?></span></td>
                            <td><span class="fw-semibold small text-warning"><?php echo e($log->action); ?></span></td>
                            <td><small class="text-secondary"><?php echo e($log->details); ?></small></td>
                            <td><code class="small text-muted"><?php echo e($log->ip_address ?: '-'); ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No activity logs recorded.</td>
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

    searchInput.addEventListener('keyup', function() {
        var term = this.value.toLowerCase();
        
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
