<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-text me-2"></i> Session History Inquiry</h4>
            <span class="text-muted small">Search and review historical lab booking records, statuses, and logs.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" onclick="exportTableToCSV('sessionHistoryTable', 'session_history.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>inquiry/sessionHistory">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Instructor Name / Service No</label>
                        <input type="text" name="name" class="form-control form-control-clms" placeholder="Search instructor..." value="<?php echo e($data['filters']['name']); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Laboratory</label>
                        <select name="lab_id" class="form-select form-control-clms">
                            <option value="">All Labs</option>
                            <?php foreach ($data['labs'] as $lab): ?>
                                <option value="<?php echo $lab->id; ?>" <?php echo $data['filters']['lab_id'] == $lab->id ? 'selected' : ''; ?>><?php echo e($lab->lab_code . ' - ' . $lab->lab_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Session Status</label>
                        <select name="status" class="form-select form-control-clms">
                            <option value="">All Statuses</option>
                            <option value="Completed" <?php echo $data['filters']['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Today" <?php echo $data['filters']['status'] === 'Today' ? 'selected' : ''; ?>>Today</option>
                            <option value="Scheduled" <?php echo $data['filters']['status'] === 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Date Range</label>
                        <div class="input-group">
                            <input type="date" name="date_from" class="form-control form-control-clms" value="<?php echo e($data['filters']['date_from']); ?>">
                            <span class="input-group-text small">to</span>
                            <input type="date" name="date_to" class="form-control form-control-clms" value="<?php echo e($data['filters']['date_to']); ?>">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Apply Filters</button>
                    </div>
                </div>

                <div class="collapse mt-3" id="advancedFilters">
                    <div class="row g-3 pt-3 border-top">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Trade</label>
                            <select name="trade" class="form-select form-control-clms">
                                <option value="">All Trades</option>
                                <?php foreach ($data['trades'] as $t): ?>
                                    <option value="<?php echo e($t->trade); ?>" <?php echo $data['filters']['trade'] === $t->trade ? 'selected' : ''; ?>><?php echo e($t->trade); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Lesson</label>
                            <select name="lesson_id" class="form-select form-control-clms">
                                <option value="">All Lessons</option>
                                <?php foreach ($data['lessons'] as $lesson): ?>
                                    <option value="<?php echo $lesson->id; ?>" <?php echo $data['filters']['lesson_id'] == $lesson->id ? 'selected' : ''; ?>><?php echo e($lesson->lesson_code . ' - ' . $lesson->lesson_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </div>

                <!-- Quick Date Filters and Advanced Toggle -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-link text-decoration-none px-1" onclick="setQuickDate('today')">Today</button>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none px-1" onclick="setQuickDate('week')">This Week</button>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none px-1" onclick="setQuickDate('month')">This Month</button>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none px-1" onclick="setQuickDate('quarter')">This Quarter</button>
                    </div>
                    <button class="btn btn-sm btn-link text-decoration-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false">
                        <i class="bi bi-sliders me-1"></i> Advanced Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Completed Sessions</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $data['counts']['Completed']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Scheduled / Upcoming</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $data['counts']['Scheduled']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Today's Sessions</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $data['counts']['Today']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-dark text-white p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Total Accumulated Hours</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo number_format($data['total_hours'], 1); ?> Hrs</h3>
            </div>
        </div>
    </div>

    <!-- Session History Table -->
    <div class="card-clms">
        <div class="table-responsive">
            <table class="table table-hover table-clms align-middle" id="sessionHistoryTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Session Date & Time <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(1)">Instructor <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(2)">Lesson <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(3)">Laboratory <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(4)">Duration <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(5)">Status <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['sessions'])): ?>
                        <?php foreach($data['sessions'] as $s): 
                            $statusClass = 'bg-primary-subtle text-primary';
                            if ($s->status === 'Completed') $statusClass = 'bg-success-subtle text-success';
                            elseif ($s->status === 'Today') $statusClass = 'bg-warning-subtle text-warning-emphasis';
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo date('d M Y', strtotime($s->date)); ?></div>
                                    <span class="small text-muted"><?php echo date('H:i', strtotime($s->start_time)) . ' - ' . date('H:i', strtotime($s->end_time)); ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold"><?php echo e($s->rank . ' ' . $s->instructor_name); ?></span>
                                    <span class="d-block small text-muted"><?php echo e($s->service_no . ' | ' . $s->trade); ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary"><?php echo e($s->lesson_name); ?></div>
                                    <span class="small text-muted"><?php echo e($s->lesson_code); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo e($s->lab_code); ?></span>
                                    <span class="d-block small text-muted"><?php echo e($s->lab_name); ?></span>
                                </td>
                                <td><span class="fw-bold text-success"><?php echo number_format($s->hours, 1); ?> Hrs</span></td>
                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo $s->status; ?></span></td>
                                <td><span class="small text-muted" title="<?php echo e($s->remarks); ?>"><?php echo e(strlen($s->remarks) > 25 ? substr($s->remarks, 0, 22) . '...' : $s->remarks); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-2 text-secondary mb-2 d-block"></i> No session matches current query criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function setQuickDate(type) {
    const today = new Date();
    let fromDate = new Date();
    let toDate = new Date();

    if (type === 'today') {
        fromDate = today;
        toDate = today;
    } else if (type === 'week') {
        const first = today.getDate() - today.getDay() + 1; // Mon
        const last = first + 6; // Sun
        fromDate = new Date(today.setDate(first));
        toDate = new Date(today.setDate(last));
    } else if (type === 'month') {
        fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
        toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    } else if (type === 'quarter') {
        const quarter = Math.floor((today.getMonth() / 3));
        fromDate = new Date(today.getFullYear(), quarter * 3, 1);
        toDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
    }

    const formatDate = (d) => d.toISOString().split('T')[0];
    document.querySelector('input[name="date_from"]').value = formatDate(fromDate);
    document.querySelector('input[name="date_to"]').value = formatDate(toDate);
    document.getElementById('filterForm').submit();
}

function resetFilters() {
    document.querySelector('input[name="name"]').value = '';
    document.querySelector('select[name="lab_id"]').value = '';
    document.querySelector('select[name="status"]').value = '';
    document.querySelector('input[name="date_from"]').value = '';
    document.querySelector('input[name="date_to"]').value = '';
    document.querySelector('select[name="trade"]').value = '';
    document.querySelector('select[name="lesson_id"]').value = '';
    document.getElementById('filterForm').submit();
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
    const table = document.getElementById("sessionHistoryTable");
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
