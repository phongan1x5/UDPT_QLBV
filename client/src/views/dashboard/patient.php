<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Debug - you can remove this later
echo '<pre>';
print_r($user['token']);
echo '</pre>';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-hospital"></i> Hospital Management Dashboard</h1>
            <?php if (!empty($user)): ?>
                <div class="text-muted">
                    Welcome back, <?php echo htmlspecialchars($user['user_id']); ?>!
                    <span class="badge bg-<?php echo $user['user_role'] === 'admin' ? 'danger' : ($user['user_role'] === 'doctor' ? 'success' : 'primary'); ?>">
                        <?php echo ucfirst($user['user_role']); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="text-muted">
                    Welcome, Guest
                    <a href="/login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($user)): ?>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Appointments Section (keep existing code) -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check"></i> My Appointments
                    </h5>
                    <a href="/appointments" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    // Extract appointments from the data structure
                    $appointmentList = [];
                    if ($appointments && $appointments['status'] === 200 && isset($appointments['data'][0]) && is_array($appointments['data'][0])) {
                        $appointmentList = array_slice($appointments['data'][0], 0, 5); // Get top 5 appointments
                    }
                    ?>

                    <?php if (!empty($appointmentList)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Doctor ID</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointmentList as $appointment): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $date = new DateTime($appointment['Ngay']);
                                                echo $date->format('M j, Y');
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $time = new DateTime($appointment['Gio']);
                                                echo $time->format('g:i A');
                                                ?>
                                            </td>
                                            <td>Dr. <?php echo htmlspecialchars($appointment['MaBacSi']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php
                                                    $type = $appointment['LoaiLichHen'];
                                                    echo $type === 'KhamMoi' ? 'New Checkup' : htmlspecialchars($type);
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $appointment['TrangThai'];
                                                $badgeClass = '';
                                                $statusText = '';

                                                switch ($status) {
                                                    case 'ChoXacNhan':
                                                        $badgeClass = 'bg-warning';
                                                        $statusText = 'Pending';
                                                        break;
                                                    case 'XacNhan':
                                                        $badgeClass = 'bg-success';
                                                        $statusText = 'Confirmed';
                                                        break;
                                                    case 'HoanThanh':
                                                        $badgeClass = 'bg-primary';
                                                        $statusText = 'Completed';
                                                        break;
                                                    case 'Huy':
                                                        $badgeClass = 'bg-danger';
                                                        $statusText = 'Cancelled';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-secondary';
                                                        $statusText = htmlspecialchars($status);
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span>
                                            </td>
                                            <!-- <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="/appointments/view/<?php echo $appointment['MaLichHen']; ?>"
                                                        class="btn btn-outline-primary btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <//?php if ($appointment['TrangThai'] === 'ChoXacNhan'): ?>
                                                        <a href="/appointments/edit/<?php echo $appointment['MaLichHen']; ?>"
                                                            class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="/appointments/cancel/<?php echo $appointment['MaLichHen']; ?>"
                                                            class="btn btn-outline-danger btn-sm" title="Cancel"
                                                            onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <//?php endif; ?>
                                                </div>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (count($appointments['data'][0]) > 5): ?>
                            <div class="text-center mt-3">
                                <p class="text-muted">Showing 5 of <?php echo count($appointments['data'][0]); ?> appointments</p>
                                <a href="/appointments" class="btn btn-primary">
                                    <i class="fas fa-calendar-alt"></i> View All Appointments
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Appointments Found</h5>
                            <p class="text-muted">You don't have any appointments scheduled yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Medical Records -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical"></i> Recent Medical Records
                    </h5>
                    <!-- <a href="/medicalRecords" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View All
                    </a> -->
                </div>
                <div class="card-body">
                    <?php
                    // Extract medical records from the data structure
                    $medicalRecords = [];
                    $medicalProfile = null;

                    if ($medicalHistory && $medicalHistory['status'] === 200 && isset($medicalHistory['data'][0])) {
                        $medicalData = $medicalHistory['data'][0];

                        // Get medical profile
                        if (isset($medicalData['MedicalProfile'])) {
                            $medicalProfile = $medicalData['MedicalProfile'];
                        }

                        // Get medical records (limit to 3 most recent)
                        if (isset($medicalData['MedicalRecords']) && is_array($medicalData['MedicalRecords'])) {
                            // Sort by date (most recent first) and take top 3
                            $sortedRecords = $medicalData['MedicalRecords'];
                            usort($sortedRecords, function ($a, $b) {
                                return strtotime($b['NgayKham']) - strtotime($a['NgayKham']);
                            });
                            $medicalRecords = array_slice($sortedRecords, 0, 3);
                        }
                    }
                    ?>

                    <?php if (!empty($medicalRecords)): ?>
                        <!-- Medical Profile Summary -->
                        <?php if ($medicalProfile): ?>
                            <div class="alert alert-info mb-4">
                                <h6><i class="fas fa-user-md"></i> Medical History Summary</h6>
                                <p class="mb-0"><?php echo htmlspecialchars($medicalProfile['TienSu']); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Recent Medical Records -->
                        <div class="row">
                            <?php foreach ($medicalRecords as $record): ?>
                                <div class="col-md-12 mb-3">
                                    <div class="card border-left-primary shadow-sm">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6 class="card-title text-primary">
                                                        <i class="fas fa-stethoscope"></i>
                                                        <?php echo htmlspecialchars($record['ChanDoan']); ?>
                                                    </h6>
                                                    <p class="card-text small text-muted mb-2">
                                                        <i class="fas fa-calendar"></i>
                                                        <?php
                                                        $date = new DateTime($record['NgayKham']);
                                                        echo $date->format('F j, Y');
                                                        ?>
                                                        &nbsp;&nbsp;
                                                        <i class="fas fa-user-md"></i>
                                                        Doctor ID: <?php echo htmlspecialchars($record['BacSi']); ?>
                                                        <?php if ($record['MaLichHen']): ?>
                                                            &nbsp;&nbsp;
                                                            <i class="fas fa-calendar-check"></i>
                                                            Appointment: <?php echo htmlspecialchars($record['MaLichHen']); ?>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p class="card-text">
                                                        <strong>Notes:</strong>
                                                        <?php echo htmlspecialchars($record['LuuY']); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <span class="badge bg-success mb-2">Completed</span>
                                                    <br>
                                                    <a href="/medical-records/view/<?php echo $record['MaGiayKhamBenh']; ?>"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($medicalData['MedicalRecords']) > 3): ?>
                            <div class="text-center mt-3">
                                <p class="text-muted">Showing 3 of <?php echo count($medicalData['MedicalRecords']); ?> medical records</p>
                                <a href="<?php echo url('medicalRecords'); ?>" class="btn btn-primary">
                                    <i class="fas fa-file-medical-alt"></i> View All Medical Records
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Medical Records</h5>
                            <p class="text-muted">Your medical records will appear here after your appointments.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Quick Actions (keep existing) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo url('appointments/book'); ?>" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </a>
                        <a href="<?php echo url('medicalRecords'); ?>" class="btn btn-outline-info">
                            <i class="fas fa-file-medical"></i> View Medical Records
                        </a>
                        <a href="<?php echo url('prescriptions'); ?>" class="btn btn-outline-success">
                            <i class="fas fa-pills"></i> My Prescriptions
                        </a>
                        <a href="<?php echo url('labResults'); ?>" class="btn btn-outline-warning">
                            <i class="fas fa-flask"></i> Lab Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile Card (keep existing) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>ID:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($user['user_id']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Role:</strong></div>
                        <div class="col-7">
                            <span class="badge bg-<?php echo $user['user_role'] === 'admin' ? 'danger' : ($user['user_role'] === 'doctor' ? 'success' : 'primary'); ?>">
                                <?php echo ucfirst($user['user_role']); ?>
                            </span>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-12">
                            <a href="/profile" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                        </div>
                    </div> -->
                </div>
            </div>

            <!-- Health Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-heartbeat"></i> Health Summary</h5>
                </div>
                <div class="card-body">
                    <?php
                    $totalAppointments = 0;
                    $pendingAppointments = 0;
                    $totalMedicalRecords = 0;

                    // Appointment stats
                    if ($appointments && $appointments['status'] === 200 && isset($appointments['data'][0])) {
                        $totalAppointments = count($appointments['data'][0]);
                        foreach ($appointments['data'][0] as $apt) {
                            if ($apt['TrangThai'] === 'ChoXacNhan') {
                                $pendingAppointments++;
                            }
                        }
                    }

                    // Medical records stats
                    if ($medicalHistory && $medicalHistory['status'] === 200 && isset($medicalHistory['data'][0]['MedicalRecords'])) {
                        $totalMedicalRecords = count($medicalHistory['data'][0]['MedicalRecords']);
                    }
                    ?>
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary"><?php echo $totalAppointments; ?></h4>
                            <small class="text-muted">Appointments</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning"><?php echo $pendingAppointments; ?></h4>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success"><?php echo $totalMedicalRecords; ?></h4>
                            <small class="text-muted">Records</small>
                        </div>
                    </div>

                    <?php if ($medicalProfile): ?>
                        <hr>
                        <div class="mt-3">
                            <h6 class="text-info"><i class="fas fa-info-circle"></i> Medical Status</h6>
                            <span class="badge bg-<?php echo $medicalProfile['is_active'] ? 'success' : 'secondary'; ?>">
                                <?php echo $medicalProfile['is_active'] ? 'Active Patient' : 'Inactive'; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Patient Dashboard - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>