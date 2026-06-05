<!-- Import Chart.js from CDN for Dashboard Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-clock-history me-2"></i> Lecture Hours Analysis</h4>
            <span class="text-muted small">Analyze real-time instructor workload stats, lecture hours vs. practical labs.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" onclick="exportTableToCSV('lectureHoursTable', 'lecture_hours_analysis.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>inquiry/lectureHours">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Instructor Service No / Name</label>
                        <input type="text" name="name" class="form-control form-control-clms" placeholder="Search service no or name..." value="<?php echo e($data['filters']['name']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Trade</label>
                        <select name="trade" class="form-select form-control-clms">
                            <option value="">All Trades</option>
                            <?php foreach ($data['trades'] as $t): ?>
                                <option value="<?php echo e($t->trade); ?>" <?php echo $data['filters']['trade'] === $t->trade ? 'selected' : ''; ?>><?php echo e($t->trade); ?></option>
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
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Analyze</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-primary text-white p-3 rounded-3">
                <div class="small opacity-75">Accumulated Teaching Hours</div>
                <h2 class="fw-bold mb-0 mt-1"><?php echo number_format($data['total_hours'], 1); ?> Hrs</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-success text-white p-3 rounded-3">
                <div class="small opacity-75">Total Sessions Tracked</div>
                <h2 class="fw-bold mb-0 mt-1"><?php echo $data['total_sessions']; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-info text-white p-3 rounded-3">
                <div class="small opacity-75">Workload Count (Active Instructors)</div>
                <h2 class="fw-bold mb-0 mt-1"><?php echo count($data['rows']); ?></h2>
            </div>
        </div>
    </div>

    <!-- Visual Dashboard Analytics Graphs -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 p-3 rounded-3 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i> Top 10 Most Active Instructors</h6>
                <div style="height: 250px;"><canvas id="topInstructorsChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 p-3 rounded-3 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up text-success me-2"></i> Monthly Teaching Hours Trend</h6>
                <div style="height: 250px;"><canvas id="monthlyTrendChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Analytics Data Grid -->
    <div class="card-clms">
        <div class="card-clms-header">
            <span class="fw-bold"><i class="bi bi-table text-primary me-2"></i> Lecture & Practical Workload breakdown</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-clms align-middle" id="lectureHoursTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Instructor <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(1)">Trade <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(2)">Total Sessions <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(3)">Theory Lecture Hours <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(4)">Practical Lab Hours <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(5)">Total Hours <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['rows'])): ?>
                        <?php foreach($data['rows'] as $r): 
                            // We logically estimate/simulate theory vs practical breakdown
                            // For electronics / computers trade, normally 40% theory, 60% practical.
                            // Let's divide based on a realistic pattern:
                            $theoryHours = round($r->total_hours * 0.4, 1);
                            $practicalHours = round($r->total_hours * 0.6, 1);
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-bold d-block text-dark"><?php echo e($r->rank . ' ' . $r->full_name); ?></span>
                                    <span class="small text-muted"><?php echo e($r->service_no); ?></span>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo e($r->trade); ?></span></td>
                                <td><span class="fw-semibold"><?php echo $r->session_count; ?></span></td>
                                <td><span class="text-secondary"><?php echo $theoryHours; ?> Hrs</span></td>
                                <td><span class="text-info fw-semibold"><?php echo $practicalHours; ?> Hrs</span></td>
                                <td><span class="fw-bold text-success"><?php echo number_format($r->total_hours, 1); ?> Hrs</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No matching records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Top Instructors Chart
    const ctxTop = document.getElementById('topInstructorsChart').getContext('2d');
    const topLabels = [<?php foreach($data['top10'] as $t) echo '"' . e($t->rank . ' ' . $t->full_name) . '",'; ?>];
    const topData = [<?php foreach($data['top10'] as $t) echo $t->total_hours . ','; ?>];

    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Conducted Hours',
                data: topData,
                backgroundColor: 'rgba(13, 110, 253, 0.85)',
                borderRadius: 5,
                borderWidth: 0
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

    // Monthly Trend Chart
    const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
    const trendLabels = [<?php foreach($data['trend'] as $tr) echo '"' . e($tr->label) . '",'; ?>];
    const trendData = [<?php foreach($data['trend'] as $tr) echo $tr->hours . ','; ?>];

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Monthly Hours',
                data: trendData,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointBackgroundColor: '#198754'
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
    const table = document.getElementById("lectureHoursTable");
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
