<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold"><i class="bi bi-person-workspace me-2 text-primary"></i>User Account Management</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Create User Account
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="user-search-input" class="form-control border-start-0" placeholder="Search by username..." value="<?php echo e($data['q'] ?? ''); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select id="user-role-filter" class="form-select">
                <option value="">All Roles</option>
                <option value="1" <?php echo (isset($data['role_id']) && $data['role_id'] === 1) ? 'selected' : ''; ?>>Administrator</option>
                <option value="2" <?php echo (isset($data['role_id']) && $data['role_id'] === 2) ? 'selected' : ''; ?>>Instructor</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-clms align-middle" id="users-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Failed Attempts</th>
                    <th>Last Login</th>
                    <th>Last Password Change</th>
                    <th>Security Options</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
                <?php if(!empty($data['users'])): ?>
                    <?php foreach($data['users'] as $user): ?>
                        <tr id="user-row-<?php echo $user->id; ?>">
                            <td>#<?php echo $user->id; ?></td>
                            <td>
                                <span class="fw-semibold text-capitalize"><?php echo e($user->username); ?></span>
                                <?php if((int)$user->id === (int)$_SESSION['user_id']): ?>
                                    <span class="badge bg-secondary ms-1">You</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if((int)$user->role_id === 1): ?>
                                    <span class="badge bg-primary-light text-primary" style="font-weight:600;"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary" style="font-weight:600;"><i class="bi bi-person-fill me-1"></i>Instructor</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user->status === 'active'): ?>
                                    <span class="badge-active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                                <?php elseif($user->status === 'locked'): ?>
                                    <span class="badge-faulty text-danger" style="background:#FEE2E2;"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                                <?php else: ?>
                                    <span class="badge-inactive"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-bold <?php echo $user->failed_attempts > 0 ? 'text-danger' : 'text-muted'; ?>">
                                <?php echo $user->failed_attempts; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo $user->last_login ? date('d M Y H:i', strtotime($user->last_login)) : 'Never'; ?>
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo $user->last_password_change ? date('d M Y', strtotime($user->last_password_change)) : 'Never'; ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <?php if((int)$user->force_password_change === 1): ?>
                                        <span class="text-warning small" style="font-size:0.75rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Force Reset Pending</span>
                                    <?php endif; ?>
                                    <span class="text-muted small" style="font-size:0.75rem;">Expires: <?php echo $user->password_expiry_days; ?> days</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm p-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-user-btn" href="#" 
                                               data-id="<?php echo $user->id; ?>"
                                               data-username="<?php echo e($user->username); ?>"
                                               data-role="<?php echo $user->role_id; ?>"
                                               data-status="<?php echo $user->status; ?>"
                                               data-force="<?php echo $user->force_password_change; ?>"
                                               data-expiry="<?php echo $user->password_expiry_days; ?>"
                                               data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                <i class="bi bi-pencil-fill me-2 text-secondary"></i>Edit Account
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item reset-password-btn" href="#" 
                                               data-id="<?php echo $user->id; ?>"
                                               data-username="<?php echo e($user->username); ?>"
                                               data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                                <i class="bi bi-key-fill me-2 text-secondary"></i>Reset Password
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item history-user-btn" href="#" 
                                               data-id="<?php echo $user->id; ?>"
                                               data-bs-toggle="modal" data-bs-target="#historyUserModal">
                                                <i class="bi bi-clock-history me-2 text-secondary"></i>Login History
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        
                                        <?php if((int)$user->id !== (int)$_SESSION['user_id']): ?>
                                            <?php if($user->status === 'active'): ?>
                                                <li><a class="dropdown-item text-warning" href="<?php echo URLROOT; ?>user/deactivate/<?php echo $user->id; ?>"><i class="bi bi-dash-circle me-2"></i>Deactivate</a></li>
                                                <li><a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>user/lock/<?php echo $user->id; ?>"><i class="bi bi-lock-fill me-2"></i>Lock Account</a></li>
                                            <?php elseif($user->status === 'locked'): ?>
                                                <li><a class="dropdown-item text-success" href="<?php echo URLROOT; ?>user/unlock/<?php echo $user->id; ?>"><i class="bi bi-unlock-fill me-2"></i>Unlock Account</a></li>
                                            <?php else: ?>
                                                <li><a class="dropdown-item text-success" href="<?php echo URLROOT; ?>user/activate/<?php echo $user->id; ?>"><i class="bi bi-check-circle me-2"></i>Activate</a></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="no-records-row">
                        <td colspan="9" class="text-center text-muted py-4">No user accounts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================== -->
<!-- 1. CREATE USER ACCOUNT MODAL -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>user/create" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Create User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="create_username" class="form-label">Username</label>
                        <input type="text" name="username" id="create_username" class="form-control" required placeholder="e.g. j.doe">
                    </div>

                    <div class="mb-3">
                        <label for="create_password" class="form-label">Password</label>
                        <input type="password" name="password" id="create_password" class="form-control" required placeholder="Minimum 8 characters">
                    </div>

                    <div class="mb-3">
                        <label for="create_role_id" class="form-label">Role</label>
                        <select name="role_id" id="create_role_id" class="form-select" required>
                            <option value="2">Instructor</option>
                            <option value="1">Administrator</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="create_status" class="form-label">Account Status</label>
                        <select name="status" id="create_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="locked">Locked</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="create_expiry" class="form-label">Password Expiry (Days)</label>
                        <input type="number" name="password_expiry_days" id="create_expiry" class="form-control" value="90" min="0" required>
                        <small class="text-muted">Set to 0 for no expiry.</small>
                    </div>

                    <div class="mb-3 form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" name="force_password_change" id="create_force" value="1" checked>
                        <label class="form-check-label fw-semibold" for="create_force">Force Password Change on next login</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- 2. EDIT USER ACCOUNT MODAL -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel"><i class="bi bi-pencil-fill me-2"></i>Edit User Account: <span id="edit-user-title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3" id="edit-role-container">
                        <label for="edit_role_id" class="form-label">Role</label>
                        <select name="role_id" id="edit_role_id" class="form-select" required>
                            <option value="2">Instructor</option>
                            <option value="1">Administrator</option>
                        </select>
                    </div>

                    <div class="mb-3" id="edit-status-container">
                        <label for="edit_status" class="form-label">Account Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="locked">Locked</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_expiry" class="form-label">Password Expiry (Days)</label>
                        <input type="number" name="password_expiry_days" id="edit_expiry" class="form-control" min="0" required>
                    </div>

                    <div class="mb-3 form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" name="force_password_change" id="edit_force" value="1">
                        <label class="form-check-label fw-semibold" for="edit_force">Force Password Change on next login</label>
                    </div>

                    <div class="alert alert-info py-2 d-none" id="edit-self-warning" style="font-size:0.82rem;">
                        <i class="bi bi-info-circle-fill me-2"></i>You are editing your own account. Role and Status modifications are disabled to prevent lockout.
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

<!-- ============================================== -->
<!-- 3. RESET PASSWORD MODAL -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="resetPasswordForm" action="" method="POST">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="resetPasswordModalLabel"><i class="bi bi-key-fill me-2"></i>Reset Password: <span id="reset-user-title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                    <div class="mb-3">
                        <label for="reset_new_password" class="form-label">Temporary / New Password</label>
                        <input type="password" name="new_password" id="reset_new_password" class="form-control" required placeholder="Minimum 8 characters">
                    </div>

                    <div class="mb-3 form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" name="force_password_change" id="reset_force" value="1" checked>
                        <label class="form-check-label fw-semibold" for="reset_force">Force Password Change on next login</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- 4. LOGIN HISTORY MODAL -->
<div class="modal fade" id="historyUserModal" tabindex="-1" aria-labelledby="historyUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="historyUserModalLabel"><i class="bi bi-clock-history me-2"></i>Login & Security Audit: <span id="history-user-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height:400px;">
                    <table class="table table-clms align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Action</th>
                                <th>IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body">
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                    Loading history logs...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a id="export-history-pdf-btn" href="#" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF Report
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editUserModal = document.getElementById('editUserModal');
    const resetPasswordModal = document.getElementById('resetPasswordModal');
    const historyUserModal = document.getElementById('historyUserModal');

    // 1. Populate Edit Modal
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const username = this.getAttribute('data-username');
            const role = this.getAttribute('data-role');
            const status = this.getAttribute('data-status');
            const force = this.getAttribute('data-force');
            const expiry = this.getAttribute('data-expiry');

            document.getElementById('edit-user-title').innerText = username;
            document.getElementById('editUserForm').action = '<?php echo URLROOT; ?>user/update/' + id;
            document.getElementById('edit_role_id').value = role;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_expiry').value = expiry;
            document.getElementById('edit_force').checked = (parseInt(force) === 1);

            // Hide/disable if self editing
            const isSelf = (parseInt(id) === <?php echo (int)$_SESSION['user_id']; ?>);
            if (isSelf) {
                document.getElementById('edit-role-container').style.display = 'none';
                document.getElementById('edit-status-container').style.display = 'none';
                document.getElementById('edit-self-warning').classList.remove('d-none');
            } else {
                document.getElementById('edit-role-container').style.display = 'block';
                document.getElementById('edit-status-container').style.display = 'block';
                document.getElementById('edit-self-warning').classList.add('d-none');
            }
        });
    });

    // 2. Populate Reset Modal
    document.querySelectorAll('.reset-password-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const username = this.getAttribute('data-username');

            document.getElementById('reset-user-title').innerText = username;
            document.getElementById('resetPasswordForm').action = '<?php echo URLROOT; ?>user/resetPassword/' + id;
            document.getElementById('reset_new_password').value = '';
            document.getElementById('reset_force').checked = true;
        });
    });

    // 3. Fetch & Render Login History
    document.querySelectorAll('.history-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('export-history-pdf-btn').href = '<?php echo URLROOT; ?>user/loginHistoryReport/' + id;
            const tbody = document.getElementById('history-table-body');
            
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>Loading logs...</td></tr>`;
            document.getElementById('history-user-title').innerText = '...';

            fetch('<?php echo URLROOT; ?>user/loginHistory/' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('history-user-title').innerText = data.username;
                    
                    if (data.history && data.history.length > 0) {
                        let html = '';
                        data.history.forEach(log => {
                            let badgeClass = 'bg-secondary';
                            if (log.action === 'LOGIN') badgeClass = 'bg-success';
                            if (log.action === 'LOGOUT') badgeClass = 'bg-info';
                            if (log.action === 'ACCOUNT_LOCKED') badgeClass = 'bg-danger';
                            if (log.action === 'PASSWORD_CHANGED') badgeClass = 'bg-warning text-dark';

                            html += `
                                <tr>
                                    <td><small class="text-muted">${log.created_at}</small></td>
                                    <td><span class="badge ${badgeClass}">${log.action}</span></td>
                                    <td><code>${log.ip_address}</code></td>
                                    <td><span class="small">${log.details || ''}</span></td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">No audit logs found for this user.</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching audit logs:', error);
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4"><i class="bi bi-x-circle me-1"></i>Failed to load logs.</td></tr>`;
                });
        });
    });

    // 4. AJAX Instant Search
    const searchInput = document.getElementById('user-search-input');
    const roleFilter = document.getElementById('user-role-filter');
    const tbody = document.getElementById('users-table-body');

    function performSearch() {
        const q = encodeURIComponent(searchInput.value);
        const roleId = roleFilter.value;
        
        fetch(`<?php echo URLROOT; ?>user/search?q=${q}&role_id=${roleId}`)
            .then(res => res.json())
            .then(users => {
                if (users.length > 0) {
                    let html = '';
                    users.forEach(user => {
                        const isSelf = user.is_current_user;
                        const selfBadge = isSelf ? `<span class="badge bg-secondary ms-1">You</span>` : '';
                        
                        const roleBadge = user.role_id === 1 
                            ? `<span class="badge bg-primary-light text-primary" style="font-weight:600;"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>`
                            : `<span class="badge bg-light text-secondary" style="font-weight:600;"><i class="bi bi-person-fill me-1"></i>Instructor</span>`;
                        
                        let statusBadge = '';
                        if (user.status === 'active') {
                            statusBadge = `<span class="badge-active"><i class="bi bi-check-circle-fill me-1"></i>Active</span>`;
                        } else if (user.status === 'locked') {
                            statusBadge = `<span class="badge-faulty text-danger" style="background:#FEE2E2;"><i class="bi bi-lock-fill me-1"></i>Locked</span>`;
                        } else {
                            statusBadge = `<span class="badge-inactive"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>`;
                        }

                        const failedAttemptsClass = user.failed_attempts > 0 ? 'text-danger' : 'text-muted';
                        
                        let forceBadge = '';
                        if (parseInt(user.force_password_change) === 1) {
                            forceBadge = `<span class="text-warning small" style="font-size:0.75rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Force Reset Pending</span>`;
                        }

                        let statusAction = '';
                        if (!isSelf) {
                            if (user.status === 'active') {
                                statusAction = `
                                    <li><a class="dropdown-item text-warning" href="<?php echo URLROOT; ?>user/deactivate/${user.id}"><i class="bi bi-dash-circle me-2"></i>Deactivate</a></li>
                                    <li><a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>user/lock/${user.id}"><i class="bi bi-lock-fill me-2"></i>Lock Account</a></li>
                                `;
                            } else if (user.status === 'locked') {
                                statusAction = `
                                    <li><a class="dropdown-item text-success" href="<?php echo URLROOT; ?>user/unlock/${user.id}"><i class="bi bi-unlock-fill me-2"></i>Unlock Account</a></li>
                                `;
                            } else {
                                statusAction = `
                                    <li><a class="dropdown-item text-success" href="<?php echo URLROOT; ?>user/activate/${user.id}"><i class="bi bi-check-circle me-2"></i>Activate</a></li>
                                `;
                            }
                        }

                        html += `
                            <tr id="user-row-${user.id}">
                                <td>#${user.id}</td>
                                <td>
                                    <span class="fw-semibold text-capitalize">${escapeHtml(user.username)}</span>
                                    ${selfBadge}
                                </td>
                                <td>${roleBadge}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center fw-bold ${failedAttemptsClass}">${user.failed_attempts}</td>
                                <td><small class="text-muted">${user.last_login}</small></td>
                                <td><small class="text-muted">${user.last_password_change}</small></td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        ${forceBadge}
                                        <span class="text-muted small" style="font-size:0.75rem;">Expires: ${user.password_expiry_days} days</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm p-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item edit-user-btn" href="#" 
                                                   data-id="${user.id}"
                                                   data-username="${escapeHtml(user.username)}"
                                                   data-role="${user.role_id}"
                                                   data-status="${user.status}"
                                                   data-force="${user.force_password_change}"
                                                   data-expiry="${user.password_expiry_days}"
                                                   data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                    <i class="bi bi-pencil-fill me-2 text-secondary"></i>Edit Account
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item reset-password-btn" href="#" 
                                                   data-id="${user.id}"
                                                   data-username="${escapeHtml(user.username)}"
                                                   data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                                    <i class="bi bi-key-fill me-2 text-secondary"></i>Reset Password
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item history-user-btn" href="#" 
                                                   data-id="${user.id}"
                                                   data-bs-toggle="modal" data-bs-target="#historyUserModal">
                                                    <i class="bi bi-clock-history me-2 text-secondary"></i>Login History
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            ${statusAction}
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                    rebindModalButtons();
                } else {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted py-4">No user accounts found.</td></tr>`;
                }
            })
            .catch(err => {
                console.error('AJAX search failed:', err);
            });
    }

    function rebindModalButtons() {
        // Rebind Edit Modal clicks
        document.querySelectorAll('.edit-user-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        document.querySelectorAll('.edit-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const username = this.getAttribute('data-username');
                const role = this.getAttribute('data-role');
                const status = this.getAttribute('data-status');
                const force = this.getAttribute('data-force');
                const expiry = this.getAttribute('data-expiry');

                document.getElementById('edit-user-title').innerText = username;
                document.getElementById('editUserForm').action = '<?php echo URLROOT; ?>user/update/' + id;
                document.getElementById('edit_role_id').value = role;
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_expiry').value = expiry;
                document.getElementById('edit_force').checked = (parseInt(force) === 1);

                const isSelf = (parseInt(id) === <?php echo (int)$_SESSION['user_id']; ?>);
                if (isSelf) {
                    document.getElementById('edit-role-container').style.display = 'none';
                    document.getElementById('edit-status-container').style.display = 'none';
                    document.getElementById('edit-self-warning').classList.remove('d-none');
                } else {
                    document.getElementById('edit-role-container').style.display = 'block';
                    document.getElementById('edit-status-container').style.display = 'block';
                    document.getElementById('edit-self-warning').classList.add('d-none');
                }
            });
        });

        // Rebind Reset clicks
        document.querySelectorAll('.reset-password-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        document.querySelectorAll('.reset-password-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const username = this.getAttribute('data-username');
                document.getElementById('reset-user-title').innerText = username;
                document.getElementById('resetPasswordForm').action = '<?php echo URLROOT; ?>user/resetPassword/' + id;
                document.getElementById('reset_new_password').value = '';
                document.getElementById('reset_force').checked = true;
            });
        });

        // Rebind History clicks
        document.querySelectorAll('.history-user-btn').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        document.querySelectorAll('.history-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('export-history-pdf-btn').href = '<?php echo URLROOT; ?>user/loginHistoryReport/' + id;
                const tbodyHistory = document.getElementById('history-table-body');
                
                tbodyHistory.innerHTML = `<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>Loading logs...</td></tr>`;
                document.getElementById('history-user-title').innerText = '...';

                fetch('<?php echo URLROOT; ?>user/loginHistory/' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('history-user-title').innerText = data.username;
                        
                        if (data.history && data.history.length > 0) {
                            let html = '';
                            data.history.forEach(log => {
                                let badgeClass = 'bg-secondary';
                                if (log.action === 'LOGIN') badgeClass = 'bg-success';
                                if (log.action === 'LOGOUT') badgeClass = 'bg-info';
                                if (log.action === 'ACCOUNT_LOCKED') badgeClass = 'bg-danger';
                                if (log.action === 'PASSWORD_CHANGED') badgeClass = 'bg-warning text-dark';

                                html += `
                                    <tr>
                                        <td><small class="text-muted">${log.created_at}</small></td>
                                        <td><span class="badge ${badgeClass}">${log.action}</span></td>
                                        <td><code>${log.ip_address}</code></td>
                                        <td><span class="small">${log.details || ''}</span></td>
                                    </tr>
                                `;
                            });
                            tbodyHistory.innerHTML = html;
                        } else {
                            tbodyHistory.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">No audit logs found for this user.</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching audit logs:', error);
                        tbodyHistory.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4"><i class="bi bi-x-circle me-1"></i>Failed to load logs.</td></tr>`;
                    });
            });
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    searchInput.addEventListener('input', performSearch);
    roleFilter.addEventListener('change', performSearch);
});
</script>
