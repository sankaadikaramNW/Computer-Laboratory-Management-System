<!-- Instructor profile view -->
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card-clms">
            <div class="card-clms-header">
                <h5 class="fw-bold m-0"><i class="bi bi-person-gear text-primary me-2"></i> Update Contact Details</h5>
            </div>

            <!-- Profile Info Banner -->
            <div class="p-3 bg-light-subtle rounded border border-color mb-4" style="background-color: rgba(69, 110, 157, 0.03) !important;">
                <div class="d-flex align-items-center">
                    <?php if(!empty($data['instructor']->profile_photo)): 
                        $thumb = preg_replace('/(\.[a-zA-Z0-9]+)$/', '_thumb$1', $data['instructor']->profile_photo);
                    ?>
                        <img src="<?php echo URLROOT; ?>uploads/instructors/<?php echo $thumb; ?>" class="rounded-circle border border-primary me-3" style="width:60px; height:60px; object-fit:cover; cursor:pointer;" onclick="viewFullSizeSrc('<?php echo URLROOT; ?>uploads/instructors/<?php echo e($data['instructor']->profile_photo); ?>')">
                    <?php else: ?>
                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; color:#888;">
                            <i class="bi bi-person" style="font-size:2rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h6 class="mb-0 fw-bold"><?php echo e($data['instructor']->rank) . ' ' . e($data['instructor']->full_name); ?></h6>
                        <span class="small text-muted">Service No: <?php echo e($data['instructor']->service_no); ?> | Trade: <?php echo e($data['instructor']->trade); ?></span>
                    </div>
                </div>
            </div>

            <form action="<?php echo URLROOT; ?>instructor/profile" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <!-- Profile Photograph Area -->
                <div class="mb-4 text-center">
                    <label class="form-label d-block fw-semibold text-muted mb-2">Profile Photograph</label>
                    <div class="position-relative d-inline-block">
                        <div class="avatar-preview-container rounded-circle border border-3 border-primary shadow-sm overflow-hidden" style="width: 130px; height: 130px; background: #f8f9fa; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <?php if(!empty($data['instructor']->profile_photo)): ?>
                                <img id="profile-avatar-preview-img" src="<?php echo URLROOT; ?>uploads/instructors/<?php echo e($data['instructor']->profile_photo); ?>" alt="Avatar Preview" class="w-100 h-100 object-fit-cover">
                                <div id="profile-avatar-placeholder-svg" style="width: 100%; height: 100%;" class="d-none">
                            <?php else: ?>
                                <img id="profile-avatar-preview-img" src="" alt="Avatar Preview" class="w-100 h-100 object-fit-cover d-none">
                                <div id="profile-avatar-placeholder-svg" style="width: 100%; height: 100%;">
                            <?php endif; ?>
                                    <svg class="text-muted" width="100%" height="100%" viewBox="0 0 24 24" fill="currentColor" style="color: #dee2e6;">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('profile_photo').click();">
                                <i class="bi bi-camera me-1"></i> Change Photo
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary <?php echo !empty($data['instructor']->profile_photo) ? '' : 'd-none'; ?>" id="profile-btn-view-full" onclick="viewProfileFullSize();">
                                <i class="bi bi-eye me-1"></i> View Full
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger <?php echo !empty($data['instructor']->profile_photo) ? '' : 'd-none'; ?>" id="profile-btn-remove-photo" onclick="clearProfilePhotoSelection();">
                                <i class="bi bi-trash me-1"></i> Remove
                            </button>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept=".jpg,.jpeg,.png,.webp" onchange="previewProfilePhoto(this);">
                        <input type="hidden" name="remove_photo" id="profile_remove_photo" value="0">
                    </div>
                    <div class="small text-muted mt-2">
                        Formats: JPG, JPEG, PNG, WEBP (Max: 5MB, Recommended: 400x400px)
                    </div>
                </div>

                <!-- Locked Service Fields -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Service Number (Read-only)</label>
                        <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['instructor']->service_no); ?>" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Rank (Read-only)</label>
                        <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['instructor']->rank); ?>" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Trade (Read-only)</label>
                    <input type="text" class="form-control form-control-clms bg-light" value="<?php echo e($data['instructor']->trade); ?>" disabled>
                </div>

                <hr class="border-color my-4">

                <!-- Editable Fields -->
                <div class="mb-3">
                    <label for="contact_no" class="form-label small fw-bold">Contact Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone-fill text-secondary"></i></span>
                        <input type="text" name="contact_no" id="contact_no" class="form-control form-control-clms" value="<?php echo e($data['instructor']->contact_no); ?>" placeholder="e.g. 0771234567" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label small fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill text-secondary"></i></span>
                        <input type="email" name="email" id="email" class="form-control form-control-clms" value="<?php echo e($data['instructor']->email); ?>" placeholder="e.g. instructor@slaf.lk" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Contact details are visible to schedulers.</span>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Full Size Modal -->
<div class="modal fade" id="profileFullPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal-body text-center p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="profile-full-modal-img" src="" class="img-fluid rounded-3 shadow-lg" style="max-height: 80vh; max-width: 100%;">
            </div>
        </div>
    </div>
</div>

<script>
function previewProfilePhoto(input) {
    const file = input.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert("File size exceeds 5MB limit.");
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImg = document.getElementById('profile-avatar-preview-img');
            const placeholder = document.getElementById('profile-avatar-placeholder-svg');
            const removeBtn = document.getElementById('profile-btn-remove-photo');
            const viewFullBtn = document.getElementById('profile-btn-view-full');

            previewImg.src = e.target.result;
            previewImg.classList.remove('d-none');
            placeholder.classList.add('d-none');
            removeBtn.classList.remove('d-none');
            viewFullBtn.classList.remove('d-none');
            
            document.getElementById('profile_remove_photo').value = '0';
        }
        reader.readAsDataURL(file);
    }
}

function clearProfilePhotoSelection() {
    const fileInput = document.getElementById('profile_photo');
    const previewImg = document.getElementById('profile-avatar-preview-img');
    const placeholder = document.getElementById('profile-avatar-placeholder-svg');
    const removeBtn = document.getElementById('profile-btn-remove-photo');
    const viewFullBtn = document.getElementById('profile-btn-view-full');

    fileInput.value = '';
    previewImg.src = '';
    previewImg.classList.add('d-none');
    placeholder.classList.remove('d-none');
    removeBtn.classList.add('d-none');
    viewFullBtn.classList.add('d-none');

    document.getElementById('profile_remove_photo').value = '1';
}

function viewProfileFullSize() {
    const previewImg = document.getElementById('profile-avatar-preview-img');
    if (previewImg.src && !previewImg.classList.contains('d-none')) {
        viewFullSizeSrc(previewImg.src);
    }
}

function viewFullSizeSrc(src) {
    document.getElementById('profile-full-modal-img').src = src;
    const modal = new bootstrap.Modal(document.getElementById('profileFullPhotoModal'));
    modal.show();
}
</script>
