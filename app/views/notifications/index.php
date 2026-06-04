<!-- Notifications Feed View -->
<div class="card-clms">
    <div class="card-clms-header">
        <h5 class="fw-bold m-0"><i class="bi bi-bell-fill text-primary me-2"></i> Notifications Inbox</h5>
        <?php if(!empty($data['notifications'])): ?>
            <a href="<?php echo URLROOT; ?>notification/markAllRead" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-check-all me-1"></i> Mark All Read
            </a>
        <?php endif; ?>
    </div>

    <div class="list-group list-group-flush">
        <?php if(!empty($data['notifications'])): ?>
            <?php foreach($data['notifications'] as $n): 
                $bgClass = $n->is_read ? '' : 'bg-light-subtle fw-semibold border-start border-primary border-3';
                
                // Determine icon based on category type
                $icon = 'bi-bell-fill text-primary';
                if($n->type === 'schedule') $icon = 'bi-calendar-event text-info';
                elseif($n->type === 'request_update') $icon = 'bi-arrow-left-right text-warning';
                elseif($n->type === 'cancellation') $icon = 'bi-calendar-x text-danger';
                elseif($n->type === 'fault_update') $icon = 'bi-wrench text-danger';
            ?>
                <div class="list-group-item d-flex align-items-center justify-content-between p-3 <?php echo $bgClass; ?>">
                    <div class="d-flex align-items-center">
                        <div class="me-3 fs-4">
                            <i class="bi <?php echo $icon; ?>"></i>
                        </div>
                        <div>
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                <?php echo date('d M Y \a\t H:i', strtotime($n->created_at)); ?>
                            </div>
                            <div class="text-dark small mt-1"><?php echo e($n->message); ?></div>
                        </div>
                    </div>
                    <div>
                        <?php if(!$n->is_read): ?>
                            <a href="<?php echo URLROOT; ?>notification/markAsRead/<?php echo $n->id; ?>" class="btn btn-sm btn-link text-decoration-none small text-muted" title="Mark as read">
                                <i class="bi bi-circle-fill text-primary small"></i> Mark Read
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 text-secondary mb-3 d-block"></i>
                You have no notifications in your inbox.
            </div>
        <?php endif; ?>
    </div>
</div>
