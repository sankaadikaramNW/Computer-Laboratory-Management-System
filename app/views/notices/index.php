<!-- Notice Board View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-megaphone-fill text-primary me-2"></i> Notice Board Announcements</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
            <i class="bi bi-plus-circle me-1"></i> Publish Announcement
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Notice Title</th>
                    <th>Announcement Message</th>
                    <th>Published By</th>
                    <th>Date Published</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['notices'])): ?>
                    <?php foreach($data['notices'] as $n): ?>
                        <tr>
                            <td><span class="fw-semibold text-primary"><?php echo e($n->title); ?></span></td>
                            <td>
                                <span class="small text-muted" title="<?php echo e($n->content); ?>"><?php echo e(strlen($n->content) > 60 ? substr($n->content, 0, 57) . '...' : $n->content); ?></span>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo e($n->publisher_name); ?></span></td>
                            <td><?php echo date('d M Y H:i', strtotime($n->created_at)); ?></td>
                            <td>
                                <?php if($n->status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Archived</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $n->id; ?>"
                                            data-title="<?php echo e($n->title); ?>"
                                            data-content="<?php echo e($n->content); ?>"
                                            data-status="<?php echo e($n->status); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editNoticeModal"
                                            title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <?php if($n->status === 'active'): ?>
                                    <a href="<?php echo URLROOT; ?>notice/notify/<?php echo $n->id; ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Send notification to all instructors"
                                       onclick="return confirm('Send this announcement as a notification to all active instructors?');">
                                        <i class="bi bi-bell-fill"></i>
                                    </a>
                                    <?php endif; ?>

                                    <a href="<?php echo URLROOT; ?>notice/delete/<?php echo $n->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this announcement from the board?');" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No announcements posted yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD ANNOUNCEMENT MODAL -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" aria-labelledby="addNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addNoticeModalLabel"><i class="bi bi-megaphone me-2 text-primary"></i> Publish Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>notice/create" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_title" class="form-label small fw-semibold">Notice Title</label>
                        <input type="text" name="title" id="add_title" class="form-control form-control-clms" placeholder="e.g. Server Maintenance Scheduling" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_content" class="form-label small fw-semibold">Announcement Content</label>
                        <textarea name="content" id="add_content" class="form-control form-control-clms" rows="6" placeholder="State announcement details, instructions, links..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="add_status" class="form-label small fw-semibold">Publication Status</label>
                        <select name="status" id="add_status" class="form-select form-control-clms">
                            <option value="active">Active (Visible on Dashboard)</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Publish Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT ANNOUNCEMENT MODAL -->
<div class="modal fade" id="editNoticeModal" tabindex="-1" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editNoticeModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_title" class="form-label small fw-semibold">Notice Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_content" class="form-label small fw-semibold">Announcement Content</label>
                        <textarea name="content" id="edit_content" class="form-control form-control-clms" rows="6" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label small fw-semibold">Publication Status</label>
                        <select name="status" id="edit_status" class="form-select form-control-clms">
                            <option value="active">Active (Visible on Dashboard)</option>
                            <option value="archived">Archived</option>
                        </select>
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
            editForm.action = '<?php echo URLROOT; ?>notice/update/' + id;
            
            document.getElementById('edit_title').value = this.getAttribute('data-title');
            document.getElementById('edit_content').value = this.getAttribute('data-content');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });
});
</script>
