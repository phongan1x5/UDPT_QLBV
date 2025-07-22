<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($appointments);
// echo '</pre>';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-calendar-alt"></i> My Appointments</h1>
                <div>
                    <a href="<?php echo url('appointments/book'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Book New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['success']);
            unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Upcoming Appointments -->
    <?php if (!empty($upcomingAppointments)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Upcoming Appointments</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-primary">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-primary mb-1">
                                                        <i class="fas fa-user-md"></i>
                                                        Dr. <?php echo htmlspecialchars($appointment['Doctor']['TenBacSi'] ?? 'Doctor #' . $appointment['MaBacSi']); ?>
                                                    </h6>
                                                    <p class="mb-1">
                                                        <i class="fas fa-calendar"></i>
                                                        <?php
                                                        $date = new DateTime($appointment['Ngay']);
                                                        echo $date->format('l, F j, Y');
                                                        ?>
                                                    </p>
                                                    <p class="mb-1">
                                                        <i class="fas fa-clock"></i>
                                                        <?php
                                                        $time = new DateTime($appointment['Gio']);
                                                        echo $time->format('g:i A');
                                                        ?>
                                                    </p>
                                                    <?php if (!empty($appointment['LyDo'])): ?>
                                                        <p class="mb-0 text-muted">
                                                            <i class="fas fa-comment"></i>
                                                            <?php echo htmlspecialchars($appointment['LyDo']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <span class="badge bg-success">
                                                        <?php echo ucfirst($appointment['TrangThai']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmCancel(<?php echo $appointment['MaLichHen']; ?>)">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- All Appointments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> All Appointments</h5>
                    <span class="badge bg-info">
                        <?php echo count($appointments); ?> Total
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($appointments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Date & Time</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-user-md text-primary"></i>
                                                Dr. <?php echo htmlspecialchars($appointment['Doctor']['TenBacSi'] ?? 'Doctor #' . $appointment['MaBacSi']); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $date = new DateTime($appointment['Ngay']);
                                                $time = new DateTime($appointment['Gio']);
                                                echo $date->format('M j, Y') . '<br>';
                                                echo '<small class="text-muted">' . $time->format('g:i A') . '</small>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($appointment['LyDo'])): ?>
                                                    <?php echo htmlspecialchars($appointment['LyDo']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No reason provided</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $appointment['TrangThai'];
                                                $badgeClass = match ($status) {
                                                    'scheduled' => 'bg-primary',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    'no-show' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($status === 'scheduled'): ?>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmCancel(<?php echo $appointment['MaLichHen']; ?>)">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                <?php endif; ?>
                                                <a href="/appointments/view/<?php echo $appointment['MaLichHen']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Appointments Found</h4>
                            <p class="text-muted">You haven't booked any appointments yet.</p>
                            <a href="/appointments/book" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Book Your First Appointment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
</style>

<script>
    function confirmCancel(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            window.location.href = '/appointments/cancel/' + appointmentId;
        }
    }
</script>

<?php
$content = ob_get_clean();
$title = 'My Appointments - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>