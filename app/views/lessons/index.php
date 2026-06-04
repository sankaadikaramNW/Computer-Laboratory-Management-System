<!-- Syllabus Lessons View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-book-half text-primary me-2"></i> Course Syllabus Lessons</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLessonModal">
            <i class="bi bi-plus-circle me-1"></i> Add Syllabus Lesson
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Lesson Code</th>
                    <th>Lesson Name</th>
                    <th>Targeted Trade</th>
                    <th>Expected Duration</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['lessons'])): ?>
                    <?php foreach($data['lessons'] as $l): ?>
                        <tr>
                            <td><span class="fw-bold text-primary"><?php echo e($l->lesson_code); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($l->lesson_name); ?></span></td>
                            <td><span class="badge bg-secondary"><?php echo e($l->trade); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($l->duration); ?> Hours</span></td>
                            <td><small class="text-muted"><?php echo e($l->description ?: 'No description'); ?></small></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $l->id; ?>"
                                            data-code="<?php echo e($l->lesson_code); ?>"
                                            data-name="<?php echo e($l->lesson_name); ?>"
                                            data-trade="<?php echo e($l->trade); ?>"
                                            data-duration="<?php echo $l->duration; ?>"
                                            data-desc="<?php echo e($l->description); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editLessonModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>lesson/delete/<?php echo $l->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this syllabus lesson? Note: This will fail if classes are allocated with it.');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No syllabus lessons registered in database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD LESSON MODAL -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addLessonModalLabel"><i class="bi bi-plus-circle me-2 text-primary"></i> Add Syllabus Lesson</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>lesson/create" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_lesson_code" class="form-label small fw-semibold">Lesson Code</label>
                        <input type="text" name="lesson_code" id="add_lesson_code" class="form-control form-control-clms" placeholder="e.g. LES-NET01" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_lesson_name" class="form-label small fw-semibold">Lesson Name</label>
                        <input type="text" name="lesson_name" id="add_lesson_name" class="form-control form-control-clms" placeholder="e.g. Network Routing Configurations" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_trade" class="form-label small fw-semibold">Target Trade / Course Branch</label>
                        <input type="text" name="trade" id="add_trade" class="form-control form-control-clms" placeholder="e.g. IT/SIG-CPL" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_duration" class="form-label small fw-semibold">Expected Duration (Hours)</label>
                        <input type="number" name="duration" id="add_duration" class="form-control form-control-clms" min="1" max="500" placeholder="e.g. 45" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_description" class="form-label small fw-semibold">Description / Syllabus Scope</label>
                        <textarea name="description" id="add_description" class="form-control form-control-clms" rows="3" placeholder="Outline contents, prerequisites..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Syllabus Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT LESSON MODAL -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-labelledby="editLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editLessonModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Syllabus Lesson</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_lesson_code" class="form-label small fw-semibold">Lesson Code</label>
                        <input type="text" name="lesson_code" id="edit_lesson_code" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_lesson_name" class="form-label small fw-semibold">Lesson Name</label>
                        <input type="text" name="lesson_name" id="edit_lesson_name" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_trade" class="form-label small fw-semibold">Target Trade / Course Branch</label>
                        <input type="text" name="trade" id="edit_trade" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_duration" class="form-label small fw-semibold">Expected Duration (Hours)</label>
                        <input type="number" name="duration" id="edit_duration" class="form-control form-control-clms" min="1" max="500" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label small fw-semibold">Description / Syllabus Scope</label>
                        <textarea name="description" id="edit_description" class="form-control form-control-clms" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.edit-btn');
    const editForm = document.getElementById('editForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editForm.action = '<?php echo URLROOT; ?>lesson/update/' + id;
            
            document.getElementById('edit_lesson_code').value = this.getAttribute('data-code');
            document.getElementById('edit_lesson_name').value = this.getAttribute('data-name');
            document.getElementById('edit_trade').value = this.getAttribute('data-trade');
            document.getElementById('edit_duration').value = this.getAttribute('data-duration');
            document.getElementById('edit_description').value = this.getAttribute('data-desc');
        });
    });
});
</script>
