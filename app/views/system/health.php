<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 fw-bold text-h text-capitalize mb-1">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>System Health Check
            </h1>
            <p class="text-muted mb-0">System environment verification, database integrity, and directory permission status.</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary shadow-sm" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise me-1"></i> Run Diagnostics
            </button>
        </div>
    </div>

    <!-- Alert for configuration errors -->
    <?php if (isset($_SESSION['startup_warnings']) && !empty($_SESSION['startup_warnings'])): ?>
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 mt-1"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Active Startup Warnings</h5>
                <p class="mb-0 small text-body">
                    The following warnings were intercepted during the last startup:
                </p>
                <ul class="mb-0 mt-2 small">
                    <?php foreach ($_SESSION['startup_warnings'] as $warning): ?>
                        <li><?php echo htmlspecialchars($warning); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; background: var(--bg-card); border: 1px solid var(--border)!important;">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-0" style="color: var(--text-h);">Diagnostics Summary</h5>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table align-middle" style="color: var(--text-body);">
                    <thead>
                        <tr class="text-uppercase small fw-bold text-secondary" style="border-bottom: 2px solid var(--border);">
                            <th style="width: 250px;">Component</th>
                            <th style="width: 200px;">Expected / Current</th>
                            <th style="width: 150px; text-align: center;">Status</th>
                            <th>Diagnostic Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['results'] as $key => $item): 
                            // Determine status badge classes
                            $badgeClass = '';
                            $iconClass = '';
                            switch ($item['status']) {
                                case 'PASS':
                                    $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                    $iconClass = 'bi-check-circle-fill';
                                    break;
                                case 'WARNING':
                                    $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                                    $iconClass = 'bi-exclamation-triangle-fill';
                                    break;
                                case 'ERROR':
                                    $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                    $iconClass = 'bi-x-circle-fill';
                                    break;
                            }
                        ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td class="fw-bold" style="color: var(--text-primary);">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td>
                                    <code class="px-2 py-1 bg-light text-dark rounded small">
                                        <?php echo htmlspecialchars($item['value']); ?>
                                    </code>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 <?php echo $badgeClass; ?>" style="font-size: 0.8rem;">
                                        <i class="bi <?php echo $iconClass; ?>"></i>
                                        <?php echo $item['status']; ?>
                                    </span>
                                </td>
                                <td class="small" style="color: var(--text-muted);">
                                    <?php echo $item['message']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
