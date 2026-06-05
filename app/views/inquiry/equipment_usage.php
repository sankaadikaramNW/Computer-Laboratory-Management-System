<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-cpu-fill me-2"></i> Equipment Usage Inquiry</h4>
            <span class="text-muted small">Monitor lab hardware, computers, smartboards usage frequency, and service logs.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" onclick="exportTableToCSV('equipmentUsageTable', 'equipment_usage_report.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>inquiry/equipmentUsage">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Assigned Lab</label>
                        <select name="lab_id" class="form-select form-control-clms">
                            <option value="">All Labs</option>
                            <?php foreach ($data['labs'] as $lab): ?>
                                <option value="<?php echo $lab->id; ?>" <?php echo $data['filters']['lab_id'] == $lab->id ? 'selected' : ''; ?>><?php echo e($lab->lab_code . ' - ' . $lab->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Equipment Type</label>
                        <select name="type" class="form-select form-control-clms">
                            <option value="">All Types</option>
                            <option value="computer" <?php echo $data['filters']['type'] === 'computer' ? 'selected' : ''; ?>>Computers</option>
                            <option value="smartboard" <?php echo $data['filters']['type'] === 'smartboard' ? 'selected' : ''; ?>>Smart Boards</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Asset / Serial Number</label>
                        <input type="text" name="asset_no" class="form-control form-control-clms" placeholder="Search asset no..." value="<?php echo e($data['filters']['asset_no']); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card-clms">
        <div class="table-responsive">
            <table class="table table-hover table-clms align-middle" id="equipmentUsageTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Asset No <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(1)">Equipment Model <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(2)">Type <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(3)">Assigned Laboratory <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(4)">Usage Frequency (Sessions) <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(5)">Status <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['rows'])): ?>
                        <?php foreach($data['rows'] as $r): 
                            $statusClass = 'badge bg-success';
                            if ($r->status === 'faulty') $statusClass = 'badge bg-danger';
                            elseif ($r->status === 'maintenance') $statusClass = 'badge bg-warning text-dark';
                            elseif ($r->status === 'removed') $statusClass = 'badge bg-secondary';
                        ?>
                            <tr>
                                <td><span class="fw-bold text-primary"><?php echo e($r->asset_no); ?></span></td>
                                <td>
                                    <span class="fw-semibold d-block text-dark"><?php echo e($r->brand . ' ' . $r->model); ?></span>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo e($r->equipment_type); ?></span></td>
                                <td>
                                    <span class="fw-semibold text-dark"><?php echo e($r->lab_code); ?></span>
                                    <span class="d-block small text-muted"><?php echo e($r->lab_name); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold text-dark me-2"><?php echo $r->usage_sessions; ?> Sessions</span>
                                        <div class="progress flex-grow-1" style="height: 6px; min-width: 50px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min(100, ($r->usage_sessions / 30) * 100); ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="<?php echo $statusClass; ?>"><?php echo ucfirst($r->status); ?></span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="viewMaintenanceHistory('<?php echo e($r->equipment_type); ?>', <?php echo $r->id; ?>, '<?php echo e($r->asset_no); ?>')">
                                        <i class="bi bi-shield-fill-check me-1"></i> Maintenance Logs
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No equipment usage records matches current filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MAINTENANCE LOGS MODAL -->
<div class="modal fade" id="maintenanceLogsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="maintenanceModalLabel"><i class="bi bi-wrench me-2 text-primary"></i> Maintenance History - <span id="mAssetNo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-clms align-middle" id="maintenanceTable">
                        <thead>
                            <tr>
                                <th>Date Scheduled</th>
                                <th>Issue / Maintenance Type</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="maintenanceLogsBody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-color">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewMaintenanceHistory(type, id, assetNo) {
    document.getElementById('mAssetNo').innerText = assetNo;
    const tbody = document.getElementById('maintenanceLogsBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';
    
    const modal = new bootstrap.Modal(document.getElementById('maintenanceLogsModal'));
    modal.show();

    fetch('<?php echo URLROOT; ?>inquiry/maintenanceHistory/' + type + '/' + id)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data && data.length > 0) {
                data.forEach(log => {
                    let badge = 'bg-secondary';
                    if (log.status === 'completed') badge = 'bg-success';
                    else if (log.status === 'in_progress') badge = 'bg-warning text-dark';
                    else if (log.status === 'scheduled') badge = 'bg-info';

                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${log.repair_date}</strong></td>
                            <td>${log.issue_type}</td>
                            <td>${log.assigned_technician}</td>
                            <td><span class="badge ${badge}">${log.status.toUpperCase()}</span></td>
                            <td><span class="small text-muted">${log.notes || '-'}</span></td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No maintenance history recorded for this asset.</td></tr>';
            }
        });
}

function exportTableToCSV(tableID, filename) {
    const csv = [];
    const rows = document.querySelectorAll("#" + tableID + " tr");
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            text = text.replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(","));
    }
    const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

function sortTable(n) {
    const table = document.getElementById("equipmentUsageTable");
    let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    switching = true;
    dir = "asc"; 
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount ++;      
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
</script>
