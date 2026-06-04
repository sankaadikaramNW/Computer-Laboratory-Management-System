<!-- Instructor Management View -->
<div class="card-clms mb-4 no-print">
    <h5 class="fw-bold mb-3"><i class="bi bi-funnel-fill text-primary me-2"></i> Search & Filters</h5>
    <form id="instructor-filter-form" onsubmit="event.preventDefault();" class="row g-3">
        <div class="col-md-4">
            <label for="search" class="form-label small fw-semibold">Service Number / Name / Username / Email</label>
            <input type="text" name="search" id="search" class="form-control form-control-clms" placeholder="e.g. S-12345 or Perera" value="<?php echo e($_GET['search'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label for="rank" class="form-label small fw-semibold">Rank</label>
            <select name="rank" id="rank" class="form-select form-control-clms">
                <option value="">-- All Ranks --</option>
                <?php foreach($data['ranks'] as $r): ?>
                    <option value="<?php echo $r; ?>" <?php echo (isset($_GET['rank']) && $_GET['rank'] === $r) ? 'selected' : ''; ?>><?php echo $r; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="trade" class="form-label small fw-semibold">Trade</label>
            <input type="text" name="trade" id="trade" class="form-control form-control-clms" placeholder="e.g. IT Specialist" value="<?php echo e($_GET['trade'] ?? ''); ?>">
        </div>
    </form>
</div>

<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-people-fill text-primary me-2"></i> Instructors Registry</h5>
        <a href="<?php echo URLROOT; ?>instructor/register" class="btn btn-primary btn-sm no-print">
            <i class="bi bi-person-plus-fill me-1"></i> Register Instructor
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-clms align-middle">
            <thead>
                <tr>
                    <th>Service No</th>
                    <th>Rank</th>
                    <th>Full Name</th>
                    <th>Trade</th>
                    <th>Contact No</th>
                    <th>Email</th>
                    <th>Linked Login</th>
                    <th>Status</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody id="instructors-table-body">
                <?php if(!empty($data['instructors'])): ?>
                    <?php foreach($data['instructors'] as $i): ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo e($i->service_no); ?></span></td>
                            <td><span class="badge bg-secondary"><?php echo e($i->rank); ?></span></td>
                            <td><span class="fw-semibold"><?php echo e($i->full_name); ?></span></td>
                            <td><?php echo e($i->trade); ?></td>
                            <td><?php echo e($i->contact_no ?: '-'); ?></td>
                            <td><?php echo e($i->email ?: '-'); ?></td>
                            <td>
                                <?php if($i->username): ?>
                                    <span class="small fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i><?php echo e($i->username); ?></span>
                                <?php else: ?>
                                    <span class="small text-muted"><i class="bi bi-dash-circle me-1"></i>Unlinked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($i->status === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="no-print">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" 
                                            data-id="<?php echo $i->id; ?>"
                                            data-service="<?php echo e($i->service_no); ?>"
                                            data-rank="<?php echo e($i->rank); ?>"
                                            data-name="<?php echo e($i->full_name); ?>"
                                            data-trade="<?php echo e($i->trade); ?>"
                                            data-contact="<?php echo e($i->contact_no); ?>"
                                            data-email="<?php echo e($i->email); ?>"
                                            data-userid="<?php echo $i->user_id; ?>"
                                            data-status="<?php echo e($i->status); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editInstructorModal"
                                            title="Edit Profile">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <?php if(!$i->username): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-login-btn"
                                            data-id="<?php echo $i->id; ?>"
                                            data-name="<?php echo e($i->full_name); ?>"
                                            data-rank="<?php echo e($i->rank); ?>"
                                            data-bs-toggle="modal" data-bs-target="#addLoginModal"
                                            title="Add Login Credentials">
                                        <i class="bi bi-person-plus-fill"></i>
                                    </button>
                                    <?php endif; ?>

                                    <!-- Secure Delete Form -->
                                    <form action="<?php echo URLROOT; ?>instructor/delete/<?php echo $i->id; ?>" method="POST" class="d-inline delete-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Instructor">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No instructor profiles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- EDIT INSTRUCTOR MODAL -->
<div class="modal fade" id="editInstructorModal" tabindex="-1" aria-labelledby="editInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editInstructorModalLabel"><i class="bi bi-pencil-fill me-2"></i> Edit Instructor Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="edit_service_no" class="form-label small fw-semibold">Service Number</label>
                        <input type="text" name="service_no" id="edit_service_no" class="form-control form-control-clms" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_rank" class="form-label small fw-semibold">Rank</label>
                            <select name="rank" id="edit_rank" class="form-select form-control-clms" required>
                                <?php foreach($data['ranks'] as $r): ?>
                                    <option value="<?php echo $r; ?>"><?php echo $r; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_trade" class="form-label small fw-semibold">Trade</label>
                            <input type="text" name="trade" id="edit_trade" class="form-control form-control-clms" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control form-control-clms" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_contact_no" class="form-label small fw-semibold">Contact Number</label>
                        <input type="text" name="contact_no" id="edit_contact_no" class="form-control form-control-clms">
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control form-control-clms">
                    </div>

                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label small fw-semibold">Link User Login Account</label>
                        <select name="user_id" id="edit_user_id" class="form-select form-control-clms">
                            <option value="">-- No linked login --</option>
                            <?php foreach($data['users'] as $u): ?>
                                <?php if($u->role_id == 2): ?>
                                    <option value="<?php echo $u->id; ?>"><?php echo e($u->username); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label small fw-semibold">Status</label>
                        <select name="status" id="edit_status" class="form-select form-control-clms">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD LOGIN CREDENTIALS MODAL -->
<div class="modal fade" id="addLoginModal" tabindex="-1" aria-labelledby="addLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addLoginModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Add Login Credentials
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addLoginForm" action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="alert alert-info py-2 mb-3" style="font-size:0.85rem;">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Creating a login account for: <strong id="add-login-instructor-name"></strong>
                    </div>

                    <div class="mb-3">
                        <label for="add_login_username" class="form-label small fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="add_login_username" class="form-control form-control-clms"
                                   placeholder="e.g. ab.perera" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_login_password" class="form-label small fw-semibold">Temporary Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="text" name="temp_password" id="add_login_password" class="form-control form-control-clms"
                                   placeholder="Minimum 8 characters" required>
                            <button type="button" class="btn btn-outline-secondary" id="add-login-gen-pw" title="Auto-generate">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_login_status" class="form-label small fw-semibold">Account Status</label>
                        <select name="account_status" id="add_login_status" class="form-select form-control-clms">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-check form-switch pt-1">
                        <input class="form-check-input" type="checkbox" name="force_change" id="add_login_force" value="1" checked>
                        <label class="form-check-label fw-semibold small" for="add_login_force">Force Password Change on First Login</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Create & Link Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('instructors-table-body');
    const searchInput = document.getElementById('search');
    const rankSelect = document.getElementById('rank');
    const tradeInput = document.getElementById('trade');
    const URLROOT = '<?php echo URLROOT; ?>';
    const CSRF = '<?php echo generateCsrfToken(); ?>';

    // ── Delete confirmation ────────────────────────────────────────
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-form')) {
            if (!confirm('Are you sure you want to delete this instructor profile? This action cannot be undone.')) {
                e.preventDefault();
            }
        }
    });

    // ── Bind Edit modal buttons ────────────────────────────────────
    function bindEditButtons() {
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('editForm').action = URLROOT + 'instructor/update/' + id;
                document.getElementById('edit_service_no').value = this.getAttribute('data-service');
                document.getElementById('edit_rank').value       = this.getAttribute('data-rank');
                document.getElementById('edit_full_name').value  = this.getAttribute('data-name');
                document.getElementById('edit_trade').value      = this.getAttribute('data-trade');
                document.getElementById('edit_contact_no').value = this.getAttribute('data-contact');
                document.getElementById('edit_email').value      = this.getAttribute('data-email');
                document.getElementById('edit_user_id').value    = this.getAttribute('data-userid') || '';
                document.getElementById('edit_status').value     = this.getAttribute('data-status');
            });
        });
    }

    // ── Bind Add-Login modal buttons ───────────────────────────────
    function bindAddLoginButtons() {
        document.querySelectorAll('.add-login-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        document.querySelectorAll('.add-login-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id   = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const rank = this.getAttribute('data-rank');
                document.getElementById('add-login-instructor-name').textContent = rank + ' ' + name;
                document.getElementById('addLoginForm').action = URLROOT + 'instructor/addLogin/' + id;
                document.getElementById('add_login_username').value = '';
                document.getElementById('add_login_password').value = '';
                document.getElementById('add_login_force').checked  = true;
            });
        });
    }

    // ── Password auto-generate ─────────────────────────────────────
    document.getElementById('add-login-gen-pw').addEventListener('click', function() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
        let pass = '';
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('add_login_password').value = pass;
    });

    // ── AJAX Search ────────────────────────────────────────────────
    function performSearch() {
        const search = encodeURIComponent(searchInput.value);
        const rank   = encodeURIComponent(rankSelect.value);
        const trade  = encodeURIComponent(tradeInput.value);

        fetch(`${URLROOT}instructor/searchAjax?search=${search}&rank=${rank}&trade=${trade}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(i => {
                        const statusBadge = i.status === 'active'
                            ? '<span class="badge badge-active">Active</span>'
                            : '<span class="badge badge-inactive">Inactive</span>';

                        const linkedLogin = i.username !== 'No Login'
                            ? `<span class="small fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i>${escapeHtml(i.username)}</span>`
                            : `<span class="small text-muted"><i class="bi bi-dash-circle me-1"></i>Unlinked</span>`;

                        const addLoginBtn = i.username === 'No Login'
                            ? `<button type="button" class="btn btn-sm btn-outline-primary add-login-btn"
                                   data-id="${i.id}"
                                   data-name="${escapeHtml(i.full_name)}"
                                   data-rank="${escapeHtml(i.rank)}"
                                   data-bs-toggle="modal" data-bs-target="#addLoginModal"
                                   title="Add Login Credentials">
                                   <i class="bi bi-person-plus-fill"></i>
                               </button>`
                            : '';

                        html += `
                            <tr>
                                <td><span class="fw-bold">${escapeHtml(i.service_no)}</span></td>
                                <td><span class="badge bg-secondary">${escapeHtml(i.rank)}</span></td>
                                <td><span class="fw-semibold">${escapeHtml(i.full_name)}</span></td>
                                <td>${escapeHtml(i.trade)}</td>
                                <td>${escapeHtml(i.contact_no !== 'N/A' ? i.contact_no : '-')}</td>
                                <td>${escapeHtml(i.email !== 'N/A' ? i.email : '-')}</td>
                                <td>${linkedLogin}</td>
                                <td>${statusBadge}</td>
                                <td class="no-print">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                                                data-id="${i.id}"
                                                data-service="${escapeHtml(i.service_no)}"
                                                data-rank="${escapeHtml(i.rank)}"
                                                data-name="${escapeHtml(i.full_name)}"
                                                data-trade="${escapeHtml(i.trade)}"
                                                data-contact="${escapeHtml(i.contact_no !== 'N/A' ? i.contact_no : '')}"
                                                data-email="${escapeHtml(i.email !== 'N/A' ? i.email : '')}"
                                                data-userid="${i.user_id || ''}"
                                                data-status="${escapeHtml(i.status)}"
                                                data-bs-toggle="modal" data-bs-target="#editInstructorModal"
                                                title="Edit Profile">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        ${addLoginBtn}
                                        <form action="${URLROOT}instructor/delete/${i.id}" method="POST" class="d-inline delete-form">
                                            <input type="hidden" name="csrf_token" value="${CSRF}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Instructor">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                    bindEditButtons();
                    bindAddLoginButtons();
                } else {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-muted">No instructor profiles found.</td></tr>`;
                }
            })
            .catch(err => console.error('AJAX Search Failed:', err));
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    searchInput.addEventListener('input', performSearch);
    rankSelect.addEventListener('change', performSearch);
    tradeInput.addEventListener('input', performSearch);

    bindEditButtons();
    bindAddLoginButtons();
});
</script>


