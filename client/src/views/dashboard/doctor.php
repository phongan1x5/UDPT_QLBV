<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// Debug - you can remove this later
echo '<pre>';
print_r($user);
echo '</pre>';
// Extract doctor data from the wrapped response
$doctorData = null;
if ($doctor && $doctor['status'] === 200 && isset($doctor['data'][0])) {
    $doctorData = $doctor['data'][0];
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-md"></i> Doctor Dashboard</h1>
            <?php if (!empty($user)): ?>
                <div class="text-muted">
                    Welcome back, Dr. <?php echo htmlspecialchars($doctorData['HoTen'] ?? $user['user_id']); ?>!
                    <span class="badge bg-success">
                        <i class="fas fa-stethoscope"></i> Doctor
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

<?php if (!empty($user) && $doctorData): ?>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Pending Confirmations Section - NEW SECTION -->
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> Pending Confirmations
                    </h5>
                    <span class="badge bg-dark">
                        Requires Your Action
                    </span>
                </div>
                <div class="card-body">
                    <?php
                    // Extract pending appointments for this doctor
                    $pendingConfirmations = [];

                    if ($appointments && $appointments['status'] === 200 && isset($appointments['data'][0]) && is_array($appointments['data'][0])) {
                        foreach ($appointments['data'][0] as $appointment) {
                            // Filter appointments that are pending confirmation for this doctor
                            if (($appointment['TrangThai'] === 'ChoXacNhan') && $appointment['MaBacSi'] == $doctorData['MaNhanVien']) {
                                $pendingConfirmations[] = $appointment;
                            }
                        }

                        // Sort by date and time (earliest first)
                        usort($pendingConfirmations, function ($a, $b) {
                            $dateCompare = strtotime($a['Ngay']) - strtotime($b['Ngay']);
                            if ($dateCompare === 0) {
                                return strtotime($a['Gio']) - strtotime($b['Gio']);
                            }
                            return $dateCompare;
                        });
                    }
                    ?>

                    <?php if (!empty($pendingConfirmations)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Booked</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingConfirmations as $appointment): ?>
                                        <tr class="appointment-row" data-appointment-id="<?php echo $appointment['MaLichHen']; ?>">
                                            <td>
                                                <div>
                                                    <strong class="text-primary">
                                                        <?php
                                                        $date = new DateTime($appointment['Ngay']);
                                                        $time = new DateTime($appointment['Gio']);
                                                        echo $date->format('M j, Y');
                                                        ?>
                                                    </strong>
                                                    <br>
                                                    <span class="text-muted">
                                                        <?php echo $time->format('g:i A'); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-user text-muted"></i>
                                                <strong>Patient ID: <?php echo htmlspecialchars($appointment['MaBenhNhan']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php
                                                    $type = $appointment['LoaiLichHen'];
                                                    switch ($type) {
                                                        case 'KhamMoi':
                                                            echo 'New Checkup';
                                                            break;
                                                        case 'TaiKham':
                                                            echo 'Follow-up';
                                                            break;
                                                        case 'KhamChuyenKhoa':
                                                            echo 'Specialist';
                                                            break;
                                                        default:
                                                            echo htmlspecialchars($type);
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php
                                                    // Calculate how long ago this was booked (you can add booking timestamp later)
                                                    $bookingDate = new DateTime($appointment['Ngay']); // Using appointment date as placeholder
                                                    $now = new DateTime();
                                                    echo 'Recently';
                                                    ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-success btn-sm confirm-appointment"
                                                        data-appointment-id="<?php echo $appointment['MaLichHen']; ?>"
                                                        title="Confirm Appointment">
                                                        <i class="fas fa-check"></i> Confirm
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm cancel-appointment"
                                                        data-appointment-id="<?php echo $appointment['MaLichHen']; ?>"
                                                        title="Cancel Appointment">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                    <button class="btn btn-outline-info btn-sm view-patient"
                                                        data-patient-id="<?php echo $appointment['MaBenhNhan']; ?>"
                                                        title="View Patient Details">
                                                        <i class="fas fa-user"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Please review and confirm these appointments to complete the booking process for your patients.
                        </div>

                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">All Caught Up!</h5>
                            <p class="text-muted">You have no pending appointment confirmations at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Today's Appointments Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day"></i> Today's Appointments
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-info">
                            <?php echo date('l, F j, Y'); ?>
                        </span>
                        <a href="/appointments" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-calendar-alt"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    // Extract appointments for this doctor
                    $todayAppointments = [];
                    $todayDate = date('Y-m-d');

                    if ($appointments && $appointments['status'] === 200 && isset($appointments['data'][0]) && is_array($appointments['data'][0])) {
                        foreach ($appointments['data'][0] as $appointment) {
                            // Filter appointments for today and this doctor
                            if ($appointment['Ngay'] === $todayDate && $appointment['MaBacSi'] == $doctorData['MaNhanVien']) {
                                $todayAppointments[] = $appointment;
                            }
                        }

                        // Sort by time
                        usort($todayAppointments, function ($a, $b) {
                            return strtotime($a['Gio']) - strtotime($b['Gio']);
                        });
                    }
                    ?>

                    <?php if (!empty($todayAppointments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todayAppointments as $appointment): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary">
                                                    <?php
                                                    $time = new DateTime($appointment['Gio']);
                                                    echo $time->format('g:i A');
                                                    ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-user"></i>
                                                Patient ID: <?php echo htmlspecialchars($appointment['MaBenhNhan']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php
                                                    $type = $appointment['LoaiLichHen'];
                                                    $typeDisplay = '';
                                                    switch ($type) {
                                                        case 'KhamMoi':
                                                            $typeDisplay = 'New Checkup';
                                                            break;
                                                        case 'TaiKham':
                                                            $typeDisplay = 'Follow-up';
                                                            break;
                                                        case 'KhamChuyenKhoa':
                                                            $typeDisplay = 'Specialist';
                                                            break;
                                                        default:
                                                            $typeDisplay = htmlspecialchars($type);
                                                    }
                                                    echo $typeDisplay;
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
                                                    case 'DaXacNhan':
                                                        $badgeClass = 'bg-success';
                                                        $statusText = 'Confirmed';
                                                        break;
                                                    case 'DaThuTien':
                                                        $badgeClass = 'bg-primary';
                                                        $statusText = 'Fees Collected';
                                                        break;
                                                    case 'DaKham':
                                                        $badgeClass = 'bg-primary';
                                                        $statusText = 'Completed';
                                                        break;
                                                    case 'DaHuy':
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
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- <button class="btn btn-outline-primary btn-sm" title="View Patient">
                                                        <i class="fas fa-user"></i>
                                                    </button> -->
                                                    <?php if ($appointment['TrangThai'] === 'DaThuTien'): ?>
                                                        <a href="<?php echo url('consultation/' . $appointment['MaLichHen']); ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fa-solid fa-stethoscope"></i> Start Consulting
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Appointments Today</h5>
                            <p class="text-muted">You have no scheduled appointments for today.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week"></i> Upcoming Appointments
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get upcoming appointments (next 7 days)
                    $upcomingAppointments = [];
                    $today = new DateTime();
                    $nextWeek = new DateTime('+7 days');

                    if ($appointments && $appointments['status'] === 200 && isset($appointments['data'][0]) && is_array($appointments['data'][0])) {
                        foreach ($appointments['data'][0] as $appointment) {
                            $appointmentDate = new DateTime($appointment['Ngay']);
                            // Filter appointments for this doctor and next 7 days (excluding today)
                            if (
                                $appointment['MaBacSi'] == $doctorData['MaNhanVien'] &&
                                $appointmentDate > $today &&
                                $appointmentDate <= $nextWeek
                            ) {
                                $upcomingAppointments[] = $appointment;
                            }
                        }

                        // Sort by date and time
                        usort($upcomingAppointments, function ($a, $b) {
                            $dateCompare = strtotime($a['Ngay']) - strtotime($b['Ngay']);
                            if ($dateCompare === 0) {
                                return strtotime($a['Gio']) - strtotime($b['Gio']);
                            }
                            return $dateCompare;
                        });

                        // Take only top 5
                        $upcomingAppointments = array_slice($upcomingAppointments, 0, 5);
                    }
                    ?>

                    <?php if (!empty($upcomingAppointments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php
                                                $date = new DateTime($appointment['Ngay']);
                                                $time = new DateTime($appointment['Gio']);
                                                echo $date->format('M j, Y') . ' at ' . $time->format('g:i A');
                                                ?>
                                            </h6>
                                            <p class="mb-1">
                                                <i class="fas fa-user"></i> Patient ID: <?php echo $appointment['MaBenhNhan']; ?>
                                                <span class="badge bg-light text-dark ms-2"><?php echo $appointment['LoaiLichHen']; ?></span>
                                            </p>
                                        </div>

                                        <?php
                                        $statusMap = [
                                            'ChoXacNhan' => ['label' => 'Pending', 'color' => 'warning'],
                                            'DaXacNhan' => ['label' => 'Confirmed', 'color' => 'primary'],
                                            'DaThuTien' => ['label' => 'Fees collected', 'color' => 'info'],
                                            'DaKham'     => ['label' => 'Completed', 'color' => 'success'],
                                            'DaHuy'     => ['label' => 'Canceled', 'color' => 'warning'],
                                        ];

                                        $currentStatus = $appointment['TrangThai'];
                                        $label = $statusMap[$currentStatus]['label'] ?? 'Unknown';
                                        $color = $statusMap[$currentStatus]['color'] ?? 'secondary';
                                        ?>

                                        <span class="badge bg-<?php echo $color; ?>">
                                            <?php echo $label; ?>
                                        </span>


                                        <!-- <span class="badge bg-<?php echo $appointment['TrangThai'] === 'DaXacNhan' ? 'success' : 'warning'; ?>">
                                            <?php echo $appointment['TrangThai'] === 'DaXacNhan' ? 'Confirmed' : 'Pending'; ?>

                                        </span> -->
                                        <?php if ($appointment['TrangThai'] === 'DaThuTien'): ?>
                                            <a href="<?php echo url('consultation/' . $appointment['MaLichHen']); ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-stethoscope"></i> Start Consulting
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No upcoming appointments in the next 7 days.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Medical Records Created
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical"></i> Recent Medical Records Created
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Medical Records Management</h5>
                        <p class="text-muted">Create and manage medical records for your patients.</p>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Medical Record
                        </button>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Doctor Profile Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-md"></i> Doctor Profile</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-success text-white mb-2">
                            <i class="fas fa-user-md fa-2x"></i>
                        </div>
                        <h6><?php echo htmlspecialchars($doctorData['HoTen']); ?></h6>
                        <p class="text-muted"><?php echo htmlspecialchars($doctorData['ChuyenKhoa']); ?></p>
                    </div>

                    <hr>

                    <div class="row mb-2">
                        <div class="col-5"><strong>ID:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($user['user_id']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Staff ID:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($doctorData['MaNhanVien']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Specialty:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($doctorData['ChuyenKhoa']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Department:</strong></div>
                        <div class="col-7">Dept. <?php echo htmlspecialchars($doctorData['IDPhongBan']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Phone:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($doctorData['SoDienThoai']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions for Doctors -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- <button class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> View My Schedule
                        </button> -->

                        <a href="<?php echo url('lookup/patientMedicalHistory'); ?>" class="btn btn-success"">
                            <i class=" fas fa-user-plus"></i> Patient Lookup
                        </a>

                        <a href="<?php echo url('medicalRecords/doctorRecents'); ?>" class="btn btn-info">
                            <i class="fa-solid fa-file"></i> Recent Medical Record
                        </a>

                        <a href="<?php echo url('appointments/doctorSchedule'); ?>" class="btn btn-primary">
                            <i class="fa-solid fa-calendar-check"></i> My Schedule
                        </a>

                        <!-- <button class="btn btn-outline-info">
                            <i class="fas fa-file-medical-alt"></i> Create Medical Record
                        </button>
                        <button class="btn btn-outline-warning">
                            <i class="fas fa-prescription"></i> Write Prescription
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-flask"></i> Order Lab Tests
                        </button> -->
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Statistics</h5>
                </div>
                <div class="card-body">
                    <?php
                    $totalDoctorAppointments = 0;
                    $pendingAppointments = 0;
                    $completedToday = 0;
                    $todayDate = date('Y-m-d');

                    // Calculate doctor's appointment stats
                    if ($appointments && $appointments['status'] === 200 && isset($appointments['data'][0])) {
                        foreach ($appointments['data'][0] as $apt) {
                            if ($apt['MaBacSi'] == $doctorData['MaNhanVien']) {
                                $totalDoctorAppointments++;

                                if ($apt['TrangThai'] === 'ChoXacNhan') {
                                    $pendingAppointments++;
                                }

                                if ($apt['Ngay'] === $todayDate && $apt['TrangThai'] === 'HoanThanh') {
                                    $completedToday++;
                                }
                            }
                        }
                    }
                    ?>

                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary"><?php echo count($todayAppointments); ?></h4>
                            <small class="text-muted">Today</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning"><?php echo $pendingAppointments; ?></h4>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success"><?php echo $completedToday; ?></h4>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-info"><?php echo $totalDoctorAppointments; ?></h5>
                            <small class="text-muted">Total Appointments</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success"><?php echo count($upcomingAppointments); ?></h5>
                            <small class="text-muted">Next 7 Days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Unable to load doctor information. Please contact administrator.
    </div>
<?php endif; ?>

<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    .list-group-item {
        border-left: none;
        border-right: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle appointment confirmation
        document.querySelectorAll('.confirm-appointment').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.dataset.appointmentId;
                const row = this.closest('.appointment-row');

                if (confirm('Are you sure you want to confirm this appointment?')) {
                    confirmAppointment(appointmentId, row);
                }
            });
        });

        // Handle appointment cancellation
        document.querySelectorAll('.cancel-appointment').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.dataset.appointmentId;
                const row = this.closest('.appointment-row');

                if (confirm('Are you sure you want to cancel this appointment? The patient will be notified.')) {
                    cancelAppointment(appointmentId, row);
                }
            });
        });

        // Handle view patient details
        document.querySelectorAll('.view-patient').forEach(button => {
            button.addEventListener('click', function() {
                const patientId = this.dataset.patientId;
                // You can implement this later to show patient details modal
                alert('Patient details feature coming soon! Patient ID: ' + patientId);
            });
        });
    });

    function confirmAppointment(appointmentId, row) {
        // Disable buttons and show loading
        const confirmBtn = row.querySelector('.confirm-appointment');
        const cancelBtn = row.querySelector('.cancel-appointment');

        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirming...';

        // Make API call to confirm appointment
        fetch(`<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/appointments/confirm/${appointmentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 200) {
                    // Success - remove the row with animation
                    row.style.transition = 'all 0.3s ease';
                    row.style.backgroundColor = '#d4edda';
                    row.style.opacity = '0.7';

                    setTimeout(() => {
                        row.remove();

                        // Check if table is empty and show success message
                        const tbody = row.closest('tbody');
                        if (tbody && tbody.children.length === 0) {
                            const tableContainer = tbody.closest('.table-responsive');
                            tableContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">All Caught Up!</h5>
                            <p class="text-muted">You have no pending appointment confirmations at this time.</p>
                        </div>
                    `;
                        }

                        // Show success message
                        showAlert('success', 'Appointment confirmed successfully!');

                        // Optionally reload the page after a delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }, 300);
                } else {
                    throw new Error(data.detail || 'Failed to confirm appointment');
                }
            })
            .catch(error => {
                console.error('Error confirming appointment:', error);
                showAlert('danger', 'Failed to confirm appointment: ' + error.message);

                // Re-enable buttons
                confirmBtn.disabled = false;
                cancelBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm';
            });
    }

    function cancelAppointment(appointmentId, row) {
        // Disable buttons and show loading
        const confirmBtn = row.querySelector('.confirm-appointment');
        const cancelBtn = row.querySelector('.cancel-appointment');

        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
        cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
        const baseUrl = "<?php echo url('/appointments/cancel'); ?>";
        const fullUrl = `${baseUrl}/${appointmentId}`;
        console.log(fullUrl)

        // Make API call to cancel appointment
        fetch(fullUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 200) {
                    // Success - remove the row with animation
                    row.style.transition = 'all 0.3s ease';
                    row.style.backgroundColor = '#f8d7da';
                    row.style.opacity = '0.7';

                    setTimeout(() => {
                        row.remove();

                        // Check if table is empty and show success message
                        const tbody = row.closest('tbody');
                        if (tbody && tbody.children.length === 0) {
                            const tableContainer = tbody.closest('.table-responsive');
                            tableContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">All Caught Up!</h5>
                            <p class="text-muted">You have no pending appointment confirmations at this time.</p>
                        </div>
                    `;
                        }

                        // Show success message
                        showAlert('warning', 'Appointment cancelled successfully.');

                        // Optionally reload the page after a delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }, 300);
                } else {
                    throw new Error(data.detail || 'Failed to cancel appointment');
                }
            })
            .catch(error => {
                console.error('Error cancelling appointment:', error);
                showAlert('danger', 'Failed to cancel appointment: ' + error.message);

                // Re-enable buttons
                confirmBtn.disabled = false;
                cancelBtn.disabled = false;
                cancelBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
            });
    }

    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';

        alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Doctor Dashboard - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>