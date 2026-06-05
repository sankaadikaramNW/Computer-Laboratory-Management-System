<div class="container-fluid px-0">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-people-fill me-2"></i> Instructor Activity Inquiry</h4>
            <span class="text-muted small">Monitor instructor workload, scheduled teaching hours, and calendars.</span>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm me-2" onclick="exportTableToCSV('instructorSummaryTable', 'instructor_workload_summary.csv')">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Summary
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV('sessionsDetailTable', 'instructor_detailed_sessions.csv')">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Sessions
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-clms mb-4">
        <div class="card-body p-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>inquiry/instructorActivity">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Service Number / Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="name" class="form-control form-control-clms" placeholder="Search service no or name..." value="<?php echo e($data['filters']['name']); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Rank</label>
                        <select name="rank" class="form-select form-control-clms">
                            <option value="">All Ranks</option>
                            <?php foreach ($data['ranks'] as $rank): ?>
                                <option value="<?php echo $rank; ?>" <?php echo $data['filters']['rank'] === $rank ? 'selected' : ''; ?>><?php echo $rank; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Trade</label>
                        <select name="trade" class="form-select form-control-clms">
                            <option value="">All Trades</option>
                            <?php foreach ($data['trades'] as $t): ?>
                                <option value="<?php echo e($t->trade); ?>" <?php echo $data['filters']['trade'] === $t->trade ? 'selected' : ''; ?>><?php echo e($t->trade); ?></option>
                            <?php endforeach; ?>
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
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Apply</button>
                    </div>
                </div>

                <!-- Advanced Sub-Filters -->
                <div class="collapse mt-3" id="advancedFilters">
                    <div class="row g-3 pt-3 border-top">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Laboratory</label>
                            <select name="lab_id" class="form-select form-control-clms">
                                <option value="">All Labs</option>
                                <?php foreach ($data['labs'] as $lab): ?>
                                    <option value="<?php echo $lab->id; ?>" <?php echo $data['filters']['lab_id'] == $lab->id ? 'selected' : ''; ?>><?php echo e($lab->lab_code . ' - ' . $lab->lab_name); ?></option>
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
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Month</label>
                            <select name="month" class="form-select form-control-clms">
                                <option value="">All Months</option>
                                <?php for($m=1; $m<=12; $m++): ?>
                                    <option value="<?php echo $m; ?>" <?php echo $data['filters']['month'] == $m ? 'selected' : ''; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Year</label>
                            <select name="year" class="form-select form-control-clms">
                                <option value="">All Years</option>
                                <?php for($y=date('Y'); $y>=2020; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo $data['filters']['year'] == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset All</button>
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
                        <i class="bi bi-sliders me-1"></i> More Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics Panel -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white p-3 rounded-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="small opacity-75">Conducted Sessions</span>
                        <h3 class="fw-bold mb-0 mt-1"><?php echo $data['totals']['sessions']; ?></h3>
                    </div>
                    <i class="bi bi-play-circle-fill fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white p-3 rounded-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="small opacity-75">Total Teaching Hours</span>
                        <h3 class="fw-bold mb-0 mt-1"><?php echo number_format($data['totals']['hours'], 1); ?> Hrs</h3>
                    </div>
                    <i class="bi bi-clock-history fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white p-3 rounded-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="small opacity-75">Active Instructors</span>
                        <h3 class="fw-bold mb-0 mt-1"><?php echo $data['totals']['instructors']; ?></h3>
                    </div>
                    <i class="bi bi-people-fill fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark p-3 rounded-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="small opacity-75">Avg Hours/Instructor</span>
                        <h3 class="fw-bold mb-0 mt-1">
                            <?php echo $data['totals']['instructors'] > 0 ? number_format($data['totals']['hours'] / $data['totals']['instructors'], 1) : 0; ?> Hrs
                        </h3>
                    </div>
                    <i class="bi bi-speedometer fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <ul class="nav nav-tabs mb-3" id="inquiryTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-panel" type="button" role="tab">
                <i class="bi bi-grid-fill me-1"></i> Instructor Workload Summary
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions-panel" type="button" role="tab">
                <i class="bi bi-list-task me-1"></i> Detailed Sessions List
            </button>
        </li>
    </ul>

    <div class="tab-content" id="inquiryTabContent">
        <!-- Panel 1: Summary -->
        <div class="tab-pane fade show active" id="summary-panel" role="tabpanel">
            <div class="card-clms">
                <div class="table-responsive">
                    <table class="table table-hover table-clms align-middle" id="instructorSummaryTable">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Service No</th>
                                <th>Trade</th>
                                <th>Total Sessions</th>
                                <th>Conducted Hours</th>
                                <th>Completed</th>
                                <th>Upcoming</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['summary'])): ?>
                                <?php foreach($data['summary'] as $r): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2 bg-light-primary text-primary fw-bold">
                                                    <?php echo substr($r->full_name, 0, 1); ?>
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block"><?php echo e($r->rank . ' ' . $r->full_name); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo e($r->service_no); ?></span></td>
                                        <td><span class="small"><?php echo e($r->trade); ?></span></td>
                                        <td><span class="fw-semibold text-dark"><?php echo $r->total_sessions; ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold text-success me-2"><?php echo $r->total_hours; ?> Hrs</span>
                                                <div class="progress flex-grow-1" style="height: 6px; min-width: 50px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo min(100, ($r->total_hours / 40) * 100); ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success"><?php echo $r->completed; ?></span></td>
                                        <td><span class="badge bg-info-subtle text-info"><?php echo $r->upcoming; ?></span></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewInstructorCalendar(<?php echo $r->id; ?>, '<?php echo e($r->rank . ' ' . $r->full_name); ?>')">
                                                <i class="bi bi-calendar3 me-1"></i> Calendar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No instructor summary matches filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel 2: Detailed Sessions -->
        <div class="tab-pane fade" id="sessions-panel" role="tabpanel">
            <div class="card-clms">
                <div class="table-responsive">
                    <table class="table table-hover table-clms align-middle" id="sessionsDetailTable">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Instructor</th>
                                <th>Lesson</th>
                                <th>Laboratory</th>
                                <th>Trade</th>
                                <th>Duration</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['sessions'])): ?>
                                <?php foreach($data['sessions'] as $s): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?php echo date('d M Y', strtotime($s->date)); ?></div>
                                            <span class="small text-muted"><?php echo date('H:i', strtotime($s->start_time)) . ' - ' . date('H:i', strtotime($s->end_time)); ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold"><?php echo e($s->rank . ' ' . $s->full_name); ?></span>
                                            <span class="d-block small text-muted"><?php echo e($s->service_no); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-primary"><?php echo e($s->lesson_name); ?></div>
                                            <span class="small text-muted"><?php echo e($s->lesson_code); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($s->lab_code); ?></span>
                                            <span class="d-block small text-muted"><?php echo e($s->lab_name); ?></span>
                                        </td>
                                        <td><span class="small"><?php echo e($s->trade); ?></span></td>
                                        <td><span class="fw-bold text-success"><?php echo number_format($s->hours, 1); ?> Hrs</span></td>
                                        <td><span class="small text-muted" title="<?php echo e($s->remarks); ?>"><?php echo e(strlen($s->remarks) > 20 ? substr($s->remarks, 0, 17) . '...' : $s->remarks); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No detailed sessions matches filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- INSTRUCTOR CALENDAR MODAL -->
<div class="modal fade" id="instructorCalendarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="calendarModalLabel"><i class="bi bi-calendar-event me-2 text-primary"></i> <span id="calendarInstructorName"></span> - Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="instructorCalendar"></div>
            </div>
            <div class="modal-footer border-color">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
}
.bg-light-primary {
    background-color: rgba(var(--primary-rgb, 13, 110, 253), 0.1);
}
</style>

<script>
let calendar = null;

function viewInstructorCalendar(instructorId, name) {
    document.getElementById('calendarInstructorName').innerText = name;
    const modal = new bootstrap.Modal(document.getElementById('instructorCalendarModal'));
    modal.show();

    // Initialize calendar after modal is fully open to ensure layout is computed correctly
    document.getElementById('instructorCalendarModal').addEventListener('shown.bs.modal', function () {
        const calendarEl = document.getElementById('instructorCalendar');
        if (calendar) {
            calendar.destroy();
        }
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            themeSystem: 'bootstrap5',
            events: function(info, successCallback, failureCallback) {
                fetch('<?php echo URLROOT; ?>allocation/getInstructorEvents/' + instructorId + '?start=' + info.startStr + '&end=' + info.endStr)
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map(item => ({
                            title: item.lesson_code + ' (' + item.lab_code + ')',
                            start: item.date + 'T' + item.start_time,
                            end: item.date + 'T' + item.end_time,
                            description: item.lesson_name + ' | ' + item.trade,
                            backgroundColor: '#0d6efd',
                            borderColor: '#0d6efd'
                        }));
                        successCallback(events);
                    });
            },
            eventDidMount: function(info) {
                const tooltip = new bootstrap.Tooltip(info.el, {
                    title: info.event.extendedProps.description,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
}

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
    document.querySelector('select[name="rank"]').value = '';
    document.querySelector('select[name="trade"]').value = '';
    document.querySelector('input[name="date_from"]').value = '';
    document.querySelector('input[name="date_to"]').value = '';
    document.querySelector('select[name="lab_id"]').value = '';
    document.querySelector('select[name="lesson_id"]').value = '';
    document.querySelector('select[name="month"]').value = '';
    document.querySelector('select[name="year"]').value = '';
    document.getElementById('filterForm').submit();
}

function exportTableToCSV(tableID, filename) {
    const csv = [];
    const rows = document.querySelectorAll("#" + tableID + " tr");
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (let j = 0; j < cols.length; j++) {
            // Clean text contents
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            // Escape double quotes
            text = text.replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(","));
    }

    // Download CSV
    const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    const downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
