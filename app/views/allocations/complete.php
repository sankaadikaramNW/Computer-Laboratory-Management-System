<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back navigation -->
            <div class="mb-3">
                <a href="<?php echo URLROOT; ?>allocation/mySchedule" class="btn btn-link text-decoration-none p-0 text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to My Schedule
                </a>
            </div>

            <!-- Page Header -->
            <div class="mb-4">
                <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-pencil-square me-2"></i> Log Session Completion</h4>
                <p class="text-muted small">Update the execution status of this allocated session and provide any operational remarks.</p>
            </div>

            <!-- Form Card -->
            <div class="card-clms mb-4">
                <div class="card-clms-header bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill text-primary" style="font-size:1.1rem;"></i>
                        <h6 class="mb-0 fw-bold">Session Information (Read-Only)</h6>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Read-Only Info Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label small text-muted mb-1">Session ID</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="#<?php echo $data['alloc']->id; ?>" readonly disabled>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label small text-muted mb-1">Session Date</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="<?php echo date('d M Y', strtotime($data['alloc']->date)); ?>" readonly disabled>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label small text-muted mb-1">Time Slot</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="<?php echo date('H:i', strtotime($data['alloc']->start_time)) . ' – ' . date('H:i', strtotime($data['alloc']->end_time)); ?>" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Instructor</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['alloc']->instructor_rank . ' ' . $data['alloc']->instructor_name); ?>" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Trade / Branch</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['alloc']->instructor_trade ?: 'N/A'); ?>" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Lesson</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['alloc']->lesson_code . ' - ' . $data['alloc']->lesson_name); ?>" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Laboratory</label>
                            <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['alloc']->lab_code . ' - ' . $data['alloc']->lab_name); ?>" readonly disabled>
                        </div>
                    </div>

                    <!-- Input Form -->
                    <form action="<?php echo URLROOT; ?>allocation/complete/<?php echo $data['alloc']->id; ?>" method="POST" id="completeSessionForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <!-- Status Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark"><span class="text-danger">*</span> Session Completion Status</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="session_status" id="statusSuccess" value="Completed Successfully" checked autocomplete="off">
                                    <label class="btn btn-outline-success w-100 p-3 d-flex flex-column align-items-center gap-2 fw-semibold" for="statusSuccess">
                                        <i class="bi bi-check-circle-fill fs-3"></i>
                                        <span>Completed Successfully</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="session_status" id="statusPartial" value="Partially Completed" autocomplete="off">
                                    <label class="btn btn-outline-warning w-100 p-3 d-flex flex-column align-items-center gap-2 fw-semibold" for="statusPartial">
                                        <i class="bi bi-exclamation-circle-fill fs-3"></i>
                                        <span>Partially Completed</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="session_status" id="statusCancelled" value="Cancelled" autocomplete="off">
                                    <label class="btn btn-outline-danger w-100 p-3 d-flex flex-column align-items-center gap-2 fw-semibold" for="statusCancelled">
                                        <i class="bi bi-x-circle-fill fs-3"></i>
                                        <span>Cancelled</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks Input -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="instructor_remarks" class="form-label fw-bold small text-dark">Remarks / Comments <span class="text-muted fw-normal">(Optional)</span></label>
                                <span class="small text-muted" id="charCount">0 / 1000 characters</span>
                            </div>
                            <textarea class="form-control form-control-clms" name="instructor_remarks" id="instructor_remarks" rows="5" 
                                      placeholder="e.g., Session completed successfully. All trainees present. Core topics covered."
                                      maxlength="1000" oninput="updateCharCount()"></textarea>
                            <div class="form-text small text-muted">Briefly summarize the lesson outcome or document any issues (e.g. power issues, trainees late).</div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?php echo URLROOT; ?>allocation/mySchedule" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Submit Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateCharCount() {
    const textarea = document.getElementById('instructor_remarks');
    const charCountLabel = document.getElementById('charCount');
    const len = textarea.value.length;
    charCountLabel.innerText = `${len} / 1000 characters`;
    
    if (len >= 950) {
        charCountLabel.classList.add('text-danger');
    } else {
        charCountLabel.classList.remove('text-danger');
    }
}
</script>
