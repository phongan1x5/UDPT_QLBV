<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Extract notifications data (handle the [data, status_code] format)
$notificationsData = [];
if (isset($notifications[0]) && is_array($notifications[0])) {
    $notificationsData = $notifications[0];
} else {
    $notificationsData = $notifications;
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">🔔 Your Notifications</h5>
                            <p class="text-sm mb-0">
                                Stay updated with your hospital services
                            </p>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <button class="btn btn-outline-primary btn-sm mb-0" onclick="refreshNotifications()">
                                    <i class="fas fa-sync me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="table-responsive">
                        <?php if (empty($notificationsData)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No Notifications Yet</h5>
                                <p class="text-muted">Your notifications will appear here when you have lab results, appointments, or prescriptions.</p>
                            </div>
                        <?php else: ?>
                            <table class="table table-flush" id="notifications-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Type
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Message
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Date & Time
                                        </th>
                                        <!-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action
                                        </th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notificationsData as $notification): ?>
                                        <tr class="notification-row" data-id="<?php echo $notification['id']; ?>">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    // Determine icon and color based on source system
                                                    $iconClass = 'fas fa-bell';
                                                    $badgeClass = 'badge-info';
                                                    $sourceIcon = '🔔';

                                                    switch (strtolower($notification['sourceSystem'])) {
                                                        case 'laboratory':
                                                            $iconClass = 'fas fa-flask';
                                                            $badgeClass = 'badge-success';
                                                            $sourceIcon = '🧪';
                                                            break;
                                                        case 'appointment':
                                                            $iconClass = 'fas fa-calendar-check';
                                                            $badgeClass = 'badge-warning';
                                                            $sourceIcon = '📅';
                                                            break;
                                                        case 'pharmacy':
                                                            $iconClass = 'fas fa-pills';
                                                            $badgeClass = 'badge-primary';
                                                            $sourceIcon = '💊';
                                                            break;
                                                    }
                                                    ?>
                                                    <div class="icon icon-shape icon-sm border-radius-md bg-gradient-<?php echo str_replace('badge-', '', $badgeClass); ?> text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i class="<?php echo $iconClass; ?> text-white opacity-10"></i>
                                                    </div>
                                                    <span class="badge <?php echo $badgeClass; ?> badge-sm">
                                                        <?php echo $sourceIcon . ' ' . ucfirst($notification['sourceSystem']); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm notification-message">
                                                        <?php echo htmlspecialchars($notification['message']); ?>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        ID: #<?php echo $notification['id']; ?>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <?php
                                                    $date = new DateTime($notification['created_at']);
                                                    $now = new DateTime();
                                                    $diff = $now->diff($date);

                                                    // Format relative time
                                                    if ($diff->d > 0) {
                                                        $timeAgo = $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                                                    } elseif ($diff->h > 0) {
                                                        $timeAgo = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                                    } elseif ($diff->i > 0) {
                                                        $timeAgo = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                                    } else {
                                                        $timeAgo = 'Just now';
                                                    }
                                                    ?>
                                                    <h6 class="mb-0 text-sm"><?php echo $timeAgo; ?></h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <?php echo $date->format('M d, Y • g:i A'); ?>
                                                    </p>
                                                </div>
                                            </td>
                                            <!-- <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                                                <i class="fas fa-check me-2"></i> Mark as Read
                                                            </a>
                                                        </li>
                                                        <?php if ($notification['sourceSystem'] === 'Laboratory'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="/used-services">
                                                                    <i class="fas fa-flask me-2"></i> View Lab Results
                                                                </a>
                                                            </li>
                                                        <?php elseif ($notification['sourceSystem'] === 'Appointment'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="/appointments">
                                                                    <i class="fas fa-calendar me-2"></i> View Appointments
                                                                </a>
                                                            </li>
                                                        <?php elseif ($notification['sourceSystem'] === 'Pharmacy'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="/prescriptions">
                                                                    <i class="fas fa-pills me-2"></i> View Prescriptions
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                                                <i class="fas fa-trash me-2"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Lab Results</p>
                                <h5 class="font-weight-bolder">
                                    <?php echo count(array_filter($notificationsData, function ($n) {
                                        return $n['sourceSystem'] === 'Laboratory';
                                    })); ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fas fa-flask text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Appointments</p>
                                <h5 class="font-weight-bolder">
                                    <?php echo count(array_filter($notificationsData, function ($n) {
                                        return $n['sourceSystem'] === 'Appointment';
                                    })); ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fas fa-calendar-check text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Prescriptions</p>
                                <h5 class="font-weight-bolder">
                                    <?php echo count(array_filter($notificationsData, function ($n) {
                                        return $n['sourceSystem'] === 'Pharmacy';
                                    })); ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-pills text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total</p>
                                <h5 class="font-weight-bolder">
                                    <?php echo count($notificationsData); ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                <i class="fas fa-bell text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Interactions -->
<script>
    function refreshNotifications() {
        window.location.reload();
    }

    function markAsRead(notificationId) {
        // Add animation to show it's being processed
        const row = document.querySelector(`tr[data-id="${notificationId}"]`);
        if (row) {
            row.style.opacity = '0.5';
        }

        // Here you would make an API call to mark as read
        // For now, just show a success message
        setTimeout(() => {
            if (row) {
                row.style.backgroundColor = '#f8f9fa';
                row.style.opacity = '1';
            }
            showToast('Notification marked as read', 'success');
        }, 500);
    }

    function deleteNotification(notificationId) {
        if (confirm('Are you sure you want to delete this notification?')) {
            const row = document.querySelector(`tr[data-id="${notificationId}"]`);
            if (row) {
                row.remove();
                showToast('Notification deleted', 'success');
            }
        }
    }

    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Auto refresh every 30 seconds
    setInterval(() => {
        console.log('Auto-refreshing notifications...');
        // You could implement AJAX refresh here instead of full page reload
    }, 30000);
</script>

<style>
    .notification-row {
        transition: all 0.3s ease;
    }

    .notification-row:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-message {
        line-height: 1.4;
        max-width: 400px;
        word-wrap: break-word;
    }

    .icon-shape {
        width: 32px;
        height: 32px;
    }

    .badge {
        font-size: 0.75rem;
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Notifications - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>