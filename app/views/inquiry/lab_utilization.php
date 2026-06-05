<!-- Import Chart.js from CDN for Dashboard Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-pie-chart-fill me-2"></i> Laboratory Utilization Analysis</h4>
            <span class="text-muted small">Analyze laboratory operational occupancy rates, hours used, and peak usage slots.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" onclick="exportTableToCSV('labUtilizationTable', 'lab_utilization_analysis.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>inquiry/labUtilization">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Laboratory</label>
                        <select name="lab_id" class="form-select form-control-clms">
                            <option value="">All Laboratories</option>
                            <?php foreach ($data['labs'] as $lab): ?>
                                <option value="<?php echo $lab->id; ?>" <?php echo $data['filters']['lab_id'] == $lab->id ? 'selected' : ''; ?>><?php echo e($lab->lab_code . ' - ' . $lab->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Date Range</label>
                        <div class="input-group">
                            <input type="date" name="date_from" class="form-control form-control-clms" value="<?php echo e($data['filters']['date_from']); ?>">
                            <span class="input-group-text small">to</span>
                            <input type="date" name="date_to" class="form-control form-control-clms" value="<?php echo e($data['filters']['date_to']); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Month</label>
                        <select name="month" class="form-select form-control-clms">
                            <option value="">All Months</option>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $data['filters']['month'] == $m ? 'selected' : ''; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold">Year</label>
                        <select name="year" class="form-select form-control-clms">
                            <option value="">All</option>
                            <?php for($y=date('Y'); $y>=2020; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $data['filters']['year'] == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Analyze</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Usage Trends Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 p-3 rounded-3 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up text-primary me-2"></i> Daily Laboratory Booking Trend</h6>
                <div style="height: 250px;"><canvas id="labUsageChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-3 rounded-3 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-fill text-warning me-2"></i> Peak Usage Timeslots</h6>
                <div class="d-flex flex-column justify-content-around h-150 pt-2">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-danger me-2" style="width: 110px;">08:00 - 10:00</span>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 85%"></div>
                        </div>
                        <span class="ms-2 small fw-bold">85%</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-warning text-dark me-2" style="width: 110px;">10:00 - 12:00</span>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 65%"></div>
                        </div>
                        <span class="ms-2 small fw-bold">65%</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-success me-2" style="width: 110px;">13:00 - 15:00</span>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 40%"></div>
                        </div>
                        <span class="ms-2 small fw-bold">40%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Utilization Details Table -->
    <div class="card-clms">
        <div class="table-responsive">
            <table class="table table-hover table-clms align-middle" id="labUtilizationTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Lab Code <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(1)">Laboratory Name <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(2)">Total Sessions <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(3)">Allocated Hours <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(4)">Available Hours <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(5)">Utilization % <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['rows'])): ?>
                        <?php foreach($data['rows'] as $r): 
                            $utilRate = $data['available_hours'] > 0 ? min(100, round(($r->total_hours / $data['available_hours']) * 100, 1)) : 0;
                            $badgeColor = 'bg-success';
                            if ($utilRate > 75) $badgeColor = 'bg-danger';
                            elseif ($utilRate > 50) $badgeColor = 'bg-warning text-dark';
                        ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo e($r->lab_code); ?></span></td>
                                <td><span class="fw-bold text-dark"><?php echo e($r->lab_name); ?></span></td>
                                <td><span class="fw-semibold"><?php echo $r->total_sessions; ?></span></td>
                                <td><span class="text-success fw-bold"><?php echo $r->total_hours; ?> Hrs</span></td>
                                <td><span class="text-muted"><?php echo $data['available_hours']; ?> Hrs</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge <?php echo $badgeColor; ?> me-2" style="width: 55px;"><?php echo $utilRate; ?>%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; min-width: 80px;">
                                            <div class="progress-bar <?php echo str_replace(' text-dark','',$badgeColor); ?>" role="progressbar" style="width: <?php echo $utilRate; ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No laboratory statistics match filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('labUsageChart').getContext('2d');
    const labels = [<?php foreach($data['daily'] as $d) echo '"' . date('d M', strtotime($d->date)) . '",'; ?>];
    const hours = [<?php foreach($data['daily'] as $d) echo $d->hours . ','; ?>];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Usage Hours',
                data: hours,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
});

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
    const table = document.getElementById("labUtilizationTable");
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
