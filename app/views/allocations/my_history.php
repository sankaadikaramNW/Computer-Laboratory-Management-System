<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-clock-history me-2"></i> My Session History</h4>
            <span class="text-muted small">Review your completed, cancelled, or rescheduled laboratory sessions.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" onclick="exportTableToCSV('mySessionHistoryTable', 'my_session_history.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export History
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Status Filter</label>
                    <select id="statusFilter" class="form-select form-control-clms" onchange="filterHistoryTable()">
                        <option value="">All Statuses</option>
                        <option value="Completed Successfully">Completed Successfully</option>
                        <option value="Partially Completed">Partially Completed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Rescheduled">Rescheduled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Search Lesson / Lab</label>
                    <input type="text" id="searchInput" class="form-control form-control-clms" placeholder="Search by lesson or lab..." onkeyup="filterHistoryTable()">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">Clear Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card-clms">
        <div class="table-responsive">
            <table class="table table-hover table-clms align-middle" id="mySessionHistoryTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Session Date & Time <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(1)">Lesson <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(2)">Laboratory <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(3)">Status <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(4)">Logged At <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th>Instructor Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['history'])): ?>
                        <?php foreach($data['history'] as $s): 
                            $statusClass = 'bg-success-subtle text-success';
                            if ($s->session_status === 'Partially Completed') {
                                $statusClass = 'bg-warning-subtle text-warning-emphasis';
                            } elseif ($s->session_status === 'Cancelled') {
                                $statusClass = 'bg-danger-subtle text-danger';
                            } elseif ($s->session_status === 'Rescheduled') {
                                $statusClass = 'bg-primary-subtle text-primary';
                            }
                        ?>
                            <tr class="history-row">
                                <td>
                                    <div class="fw-semibold"><?php echo date('d M Y', strtotime($s->date)); ?></div>
                                    <span class="small text-muted"><?php echo date('H:i', strtotime($s->start_time)) . ' - ' . date('H:i', strtotime($s->end_time)); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo e($s->lesson_name); ?></div>
                                    <span class="small text-muted"><?php echo e($s->lesson_code); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo e($s->lab_code); ?></span>
                                    <span class="d-block small text-muted"><?php echo e($s->lab_name); ?></span>
                                </td>
                                <td class="status-cell"><span class="badge <?php echo $statusClass; ?>"><?php echo $s->session_status; ?></span></td>
                                <td>
                                    <?php if($s->completed_at): ?>
                                        <div class="fw-semibold small"><?php echo date('d M Y', strtotime($s->completed_at)); ?></div>
                                        <span class="small text-muted"><?php echo date('H:i', strtotime($s->completed_at)); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($s->instructor_remarks): ?>
                                        <span class="small text-muted d-inline-block text-truncate" style="max-width: 250px;" title="<?php echo e($s->instructor_remarks); ?>">
                                            <?php echo e($s->instructor_remarks); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">No remarks</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-2 text-secondary mb-2 d-block"></i> No session history records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterHistoryTable() {
    const statusVal = document.getElementById('statusFilter').value.toLowerCase();
    const searchVal = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.history-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const statusCell = row.querySelector('.status-cell').innerText.toLowerCase();
        
        const matchesStatus = statusVal === '' || statusCell.includes(statusVal);
        const matchesSearch = searchVal === '' || text.includes(searchVal);

        if (matchesStatus && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchInput').value = '';
    filterHistoryTable();
}

function exportTableToCSV(tableID, filename) {
    const csv = [];
    const rows = document.querySelectorAll("#" + tableID + " tr");
    for (let i = 0; i < rows.length; i++) {
        // Skip hidden rows
        if (rows[i].style.display === 'none') continue;
        
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
    const table = document.getElementById("mySessionHistoryTable");
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
