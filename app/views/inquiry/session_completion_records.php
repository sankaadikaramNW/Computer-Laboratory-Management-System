<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-check me-2"></i> Session Completion Records</h4>
            <span class="text-muted small">Monitor, search, and audit instructor session completion submissions and operational remarks.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" onclick="exportTableToCSV('sessionCompletionTable', 'session_completion_records.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>inquiry/sessionCompletionRecords">
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
                            <option value="Completed Successfully" <?php echo $data['filters']['status'] === 'Completed Successfully' ? 'selected' : ''; ?>>Completed Successfully</option>
                            <option value="Partially Completed" <?php echo $data['filters']['status'] === 'Partially Completed' ? 'selected' : ''; ?>>Partially Completed</option>
                            <option value="Cancelled" <?php echo $data['filters']['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
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
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Instructor Rank</label>
                            <select name="rank" class="form-select form-control-clms">
                                <option value="">All Ranks</option>
                                <?php foreach ($data['ranks'] as $rank): ?>
                                    <option value="<?php echo $rank; ?>" <?php echo $data['filters']['rank'] === $rank ? 'selected' : ''; ?>><?php echo $rank; ?></option>
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
                <span class="small opacity-75">Completed Successfully</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $data['counts']['Completed Successfully']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Partially Completed</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $data['counts']['Partially Completed']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-danger text-white p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Cancelled</span>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $data['counts']['Cancelled']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-dark text-white p-3 rounded-3 h-100 text-center">
                <span class="small opacity-75">Overall Completion Rate</span>
                <?php 
                $rate = 0;
                $nonScheduledTotal = $data['counts']['Completed Successfully'] + $data['counts']['Partially Completed'] + $data['counts']['Cancelled'];
                if ($nonScheduledTotal > 0) {
                    $rate = round((($data['counts']['Completed Successfully'] + $data['counts']['Partially Completed']) / $nonScheduledTotal) * 100);
                }
                ?>
                <h3 class="fw-bold mb-0 mt-1"><?php echo $rate; ?>%</h3>
            </div>
        </div>
    </div>

    <!-- Records Table -->
    <div class="card-clms">
        <div class="table-responsive">
            <table class="table table-hover table-clms align-middle" id="sessionCompletionTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Session <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(1)">Instructor <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(2)">Lesson <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(3)">Laboratory <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(4)">Status <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th onclick="sortTable(5)">Completed At <i class="bi bi-arrow-down-up text-muted ms-1 fs-7"></i></th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['records'])): ?>
                        <?php foreach($data['records'] as $r): 
                            $statusClass = 'bg-primary-subtle text-primary';
                            if ($r->session_status === 'Completed Successfully') {
                                $statusClass = 'bg-success-subtle text-success';
                            } elseif ($r->session_status === 'Partially Completed') {
                                $statusClass = 'bg-warning-subtle text-warning-emphasis';
                            } elseif ($r->session_status === 'Cancelled') {
                                $statusClass = 'bg-danger-subtle text-danger';
                            }
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo date('d M Y', strtotime($r->date)); ?></div>
                                    <span class="small text-muted"><?php echo date('H:i', strtotime($r->start_time)) . ' - ' . date('H:i', strtotime($r->end_time)); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Avatar Placeholder -->
                                        <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" 
                                             style="width: 38px; height: 38px; font-size: 0.82rem; flex-shrink: 0;"
                                             title="<?php echo e($r->rank); ?>">
                                            <?php 
                                            // Get initials
                                            $parts = explode(' ', $r->instructor_name);
                                            $initials = '';
                                            if (count($parts) > 0 && !empty($parts[0])) $initials .= substr($parts[0], 0, 1);
                                            if (count($parts) > 1 && !empty($parts[1])) $initials .= substr($parts[1], 0, 1);
                                            echo strtoupper($initials ?: 'IN');
                                            ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-truncate" style="max-width: 150px;"><?php echo e($r->rank . ' ' . $r->instructor_name); ?></span>
                                            <span class="small text-muted d-block"><?php echo e($r->service_no . ' | ' . $r->trade); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary text-truncate" style="max-width: 150px;" title="<?php echo e($r->lesson_name); ?>"><?php echo e($r->lesson_name); ?></div>
                                    <span class="small text-muted"><?php echo e($r->lesson_code); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo e($r->lab_code); ?></span>
                                    <span class="d-block small text-muted text-truncate" style="max-width: 120px;" title="<?php echo e($r->lab_name); ?>"><?php echo e($r->lab_name); ?></span>
                                </td>
                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo $r->session_status; ?></span></td>
                                <td>
                                    <?php if($r->completed_at): ?>
                                        <div class="fw-semibold" style="font-size: 0.85rem;"><?php echo date('d M Y', strtotime($r->completed_at)); ?></div>
                                        <span class="small text-muted" style="font-size: 0.75rem;"><?php echo date('H:i:s', strtotime($r->completed_at)); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($r->instructor_remarks): ?>
                                        <span class="small text-muted d-inline-block text-truncate" style="max-width: 180px;" title="<?php echo e($r->instructor_remarks); ?>">
                                            <?php echo e($r->instructor_remarks); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">No remarks</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 text-secondary mb-2 d-block"></i> No session completion records found matching your filters.
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
    document.querySelector('select[name="rank"]').value = '';
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
    const table = document.getElementById("sessionCompletionTable");
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
