<!-- SLAF Camps View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i> SLAF Camp Locations</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCampModal">
            <i class="bi bi-plus-circle me-1"></i> Register Camp Location
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="camp-search-input" class="form-control border-start-0" placeholder="Search by name or code...">
            </div>
        </div>
        <div class="col-md-4">
            <select id="camp-status-filter" class="form-select">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle" id="camps-table">
            <thead>
                <tr>
                    <th>Camp Code</th>
                    <th>Camp Name</th>
                    <th>Address / Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="camps-table-body">
                <?php if(!empty($data['camps'])): ?>
                    <?php foreach($data['camps'] as $c): ?>
                        <tr class="camp-row" data-name="<?php echo strtolower(e($c->name)); ?>" data-code="<?php echo strtolower(e($c->code)); ?>" data-status="<?php echo $c->status; ?>">
                            <td><span class="fw-bold text-primary"><?php echo e($c->code); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($c->name); ?></span></td>
                            <td><small class="text-muted"><?php echo e($c->address ?: 'N/A'); ?></small></td>
                            <td>
                                <?php if($c->status === 'active'): ?>
                                    <span class="badge-active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                                <?php else: ?>
                                    <span class="badge-inactive"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $c->id; ?>"
                                            data-code="<?php echo e($c->code); ?>"
                                            data-name="<?php echo e($c->name); ?>"
                                            data-address="<?php echo e($c->address); ?>"
                                            data-status="<?php echo $c->status; ?>"
                                            data-bs-toggle="modal" data-bs-target="#editCampModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>camp/toggle/<?php echo $c->id; ?>" class="btn btn-sm <?php echo $c->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?>" title="Toggle Status">
                                        <i class="bi <?php echo $c->status === 'active' ? 'bi-slash-circle-fill' : 'bi-check-circle-fill'; ?>"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="no-records">
                        <td colspan="5" class="text-center py-4 text-muted">No camp locations registered in database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- REGISTER CAMP MODAL -->
<div class="modal fade" id="addCampModal" tabindex="-1" aria-labelledby="addCampModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="addCampModalLabel"><i class="bi bi-plus-circle me-2 text-primary"></i> Register Camp Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>camp/create" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="add_camp_code" class="form-label small fw-semibold">Camp Code</label>
                        <input type="text" name="code" id="add_camp_code" class="form-control form-control-clms" placeholder="e.g. KAT" required maxlength="20">
                    </div>

                    <div class="mb-3">
                        <label for="add_camp_name" class="form-label small fw-semibold">Camp Name</label>
                        <input type="text" name="name" id="add_camp_name" class="form-control form-control-clms" placeholder="e.g. SLAF Base Katunayake" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_address" class="form-label small fw-semibold">Address / Geographical Details</label>
                        <textarea name="address" id="add_address" class="form-control form-control-clms" rows="3" placeholder="Location, Block..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="add_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="add_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-color">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Camp Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT CAMP MODAL -->
<div class="modal fade" id="editCampModal" tabindex="-1" aria-labelledby="editCampModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
            <div class="modal-header border-color">
                <h5 class="modal-title fw-bold" id="editCampModalLabel"><i class="bi bi-pencil-fill me-2 text-primary"></i> Edit Camp Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_camp_code" class="form-label small fw-semibold">Camp Code</label>
                        <input type="text" name="code" id="edit_camp_code" class="form-control form-control-clms" required maxlength="20">
                    </div>

                    <div class="mb-3">
                        <label for="edit_camp_name" class="form-label small fw-semibold">Camp Name</label>
                        <input type="text" name="name" id="edit_camp_name" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_address" class="form-label small fw-semibold">Address / Location</label>
                        <textarea name="address" id="edit_address" class="form-control form-control-clms" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
            editForm.action = '<?php echo URLROOT; ?>camp/update/' + id;
            
            document.getElementById('edit_camp_code').value = this.getAttribute('data-code');
            document.getElementById('edit_camp_name').value = this.getAttribute('data-name');
            document.getElementById('edit_address').value = this.getAttribute('data-address');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
        });
    });

    // jQuery-like search filter logic
    const searchInput = document.getElementById('camp-search-input');
    const statusFilter = document.getElementById('camp-status-filter');
    const rows = document.querySelectorAll('.camp-row');

    function filterCamps() {
        const query = searchInput.value.toLowerCase().trim();
        const statusVal = statusFilter.value;

        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const code = row.getAttribute('data-code');
            const status = row.getAttribute('data-status');

            const matchesSearch = name.includes(query) || code.includes(query);
            const matchesStatus = !statusVal || status === statusVal;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noRecords = document.getElementById('no-records');
        if (visibleCount === 0) {
            if (!noRecords) {
                const tr = document.createElement('tr');
                tr.id = 'no-records';
                tr.innerHTML = `<td colspan="5" class="text-center py-4 text-muted">No camps match search criteria.</td>`;
                document.getElementById('camps-table-body').appendChild(tr);
            } else {
                noRecords.style.display = '';
            }
        } else {
            if (noRecords) noRecords.style.display = 'none';
        }
    }

    searchInput.addEventListener('input', filterCamps);
    statusFilter.addEventListener('change', filterCamps);
});
</script>
