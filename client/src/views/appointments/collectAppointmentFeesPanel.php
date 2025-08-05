<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
echo '<pre>';
// print_r($user);
print_r($paid_appointments);
echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-money-bill-wave"></i> Appointment Fee Collection</h1>
                    <p class="text-muted mb-0">Collect fees for patient appointments</p>
                    <!-- <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('desk-staff/dashboard'); ?>">Desk Staff</a></li>
                            <li class="breadcrumb-item active">Fee Collection</li>
                        </ol>
                    </nav> -->
                </div>
                <div>
                    <a href="<?php echo url('dashboard'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer"></div>

    <?php
    // Process appointments data
    $appointmentsList = [];
    $totalAppointments = 0;
    $totalFees = 0;
    $paidCount = 0;
    $unpaidCount = 0;

    if (isset($appointments) && is_array($appointments)) {
        $appointmentsList = $appointments;
        $totalAppointments = count($appointmentsList);

        foreach ($appointmentsList as $appointment) {
            // Since PhiKham is not in your data, we'll set a default fee
            // You can modify this based on your business logic
            $defaultFee = 200000; // 200,000 VND default fee
            $totalFees += $defaultFee;

            // Since TrangThaiThanhToan is not in your data, we'll assume all are unpaid initially
            // You can add payment status logic here
            $unpaidCount++;
        }
    }

    if (isset($paid_appointments) && is_array($paid_appointments)) {
        $paidAppointmentsList = $paid_appointments;
        $paidCount = count($paidAppointmentsList);
    }
    ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <h4><?php echo $totalAppointments; ?></h4>
                    <small>Total Appointments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4><?php echo $paidCount; ?></h4>
                    <small>Fees Collected</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4><?php echo $unpaidCount; ?></h4>
                    <small>Pending Payment</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h4><?php echo number_format($totalFees, 0); ?> VND</h4>
                    <small>Total Fees</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <?php if (!empty($appointmentsList)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list"></i> Appointments List
                            </h5>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" onclick="refreshAppointments()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                                <!-- <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">Appointment ID</th>
                                        <th width="15%">Patient ID</th>
                                        <th width="15%">Doctor ID</th>
                                        <th width="15%">Date & Time</th>
                                        <th width="12%">Appointment Type</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Fee Amount</th>
                                        <th width="13%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointmentsList as $index => $appointment): ?>
                                        <?php
                                        // Map your actual data fields
                                        $appointmentId = $appointment['MaLichHen'];
                                        $patientId = $appointment['MaBenhNhan'];
                                        $doctorId = $appointment['MaBacSi'];
                                        $appointmentDate = $appointment['Ngay'];
                                        $appointmentTime = $appointment['Gio'];
                                        $appointmentType = $appointment['LoaiLichHen'];
                                        $status = $appointment['TrangThai'];

                                        // Set default fee (you can modify this logic)
                                        $fee = 200000; // 200,000 VND

                                        // Assume all appointments are unpaid initially
                                        // You can add logic to check payment status from another source
                                        $isPaid = false;

                                        // Status mapping for better display
                                        $statusDisplay = [
                                            'DaXacNhan' => 'Confirmed',
                                            'ChoXacNhan' => 'Pending',
                                            'DaHuy' => 'Cancelled',
                                            'HoanThanh' => 'Completed'
                                        ];

                                        $statusClass = [
                                            'DaXacNhan' => 'success',
                                            'ChoXacNhan' => 'warning',
                                            'DaHuy' => 'danger',
                                            'HoanThanh' => 'info'
                                        ];

                                        // Appointment type mapping
                                        $typeDisplay = [
                                            'KhamMoi' => 'New Examination',
                                            'TaiKham' => 'Follow-up',
                                            'KhanCap' => 'Emergency'
                                        ];
                                        ?>
                                        <tr class="appointment-row" data-appointment-id="<?php echo $appointmentId; ?>">
                                            <td>
                                                <span class="badge bg-primary">#<?php echo htmlspecialchars($appointmentId); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">BN<?php echo htmlspecialchars($patientId); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">DR<?php echo htmlspecialchars($doctorId); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $dateTime = new DateTime($appointmentDate . ' ' . $appointmentTime);
                                                ?>
                                                <div>
                                                    <strong><?php echo $dateTime->format('M j, Y'); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo $dateTime->format('g:i A'); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo $typeDisplay[$appointmentType] ?? $appointmentType; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusClass[$status] ?? 'secondary'; ?>">
                                                    <?php echo $statusDisplay[$status] ?? $status; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success"><?php echo number_format($fee, 0); ?> VND</strong>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <?php if (!$isPaid && $status === 'DaXacNhan'): ?>
                                                        <button class="btn btn-success btn-sm mb-1"
                                                            onclick="collectFee('<?php echo $appointmentId; ?>', '<?php echo $fee; ?>', 'BN<?php echo $patientId; ?>')"
                                                            id="collect-btn-<?php echo $appointmentId; ?>">
                                                            <i class="fas fa-money-bill"></i> Collect
                                                        </button>
                                                    <?php elseif ($isPaid): ?>
                                                        <button class="btn btn-outline-success btn-sm mb-1" disabled>
                                                            <i class="fas fa-check"></i> Paid
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline-secondary btn-sm mb-1" disabled>
                                                            <i class="fas fa-ban"></i> N/A
                                                        </button>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- No Appointments Message -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Appointments Found</h4>
                        <p class="text-muted">There are no appointments available for fee collection at this time.</p>
                        <!-- <div class="mt-3">
                            <a href="<?php echo url('appointments/list'); ?>" class="btn btn-primary">
                                <i class="fas fa-calendar"></i> View All Appointments
                            </a>
                            <a href="<?php echo url('lookup/patient'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Search Patient
                            </a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Paid Appointments Section -->
    <?php if (!empty($paid_appointments)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle"></i> Paid Appointments
                            <span class="badge bg-light text-success ms-2"><?php echo count($paid_appointments); ?> paid</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">Appointment ID</th>
                                        <th width="15%">Patient ID</th>
                                        <th width="15%">Doctor ID</th>
                                        <th width="15%">Date & Time</th>
                                        <th width="12%">Appointment Type</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Fee Amount</th>
                                        <th width="13%">Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paid_appointments as $index => $appointment): ?>
                                        <?php
                                        // Map your actual data fields for paid appointments
                                        $appointmentId = $appointment['MaLichHen'];
                                        $patientId = $appointment['MaBenhNhan'];
                                        $doctorId = $appointment['MaBacSi'];
                                        $appointmentDate = $appointment['Ngay'];
                                        $appointmentTime = $appointment['Gio'];
                                        $appointmentType = $appointment['LoaiLichHen'];
                                        $status = $appointment['TrangThai'];

                                        // Set fee amount (same logic as unpaid)
                                        $fee = 200000; // 200,000 VND

                                        // Status mapping for better display
                                        $statusDisplay = [
                                            'DaThuTien' => 'Fee Collected',
                                            'DaXacNhan' => 'Confirmed',
                                            'HoanThanh' => 'Completed'
                                        ];

                                        $statusClass = [
                                            'DaThuTien' => 'success',
                                            'DaXacNhan' => 'info',
                                            'HoanThanh' => 'primary'
                                        ];

                                        // Appointment type mapping
                                        $typeDisplay = [
                                            'KhamMoi' => 'New Examination',
                                            'TaiKham' => 'Follow-up',
                                            'KhanCap' => 'Emergency'
                                        ];
                                        ?>
                                        <tr class="paid-appointment-row">
                                            <td>
                                                <span class="badge bg-success">#<?php echo htmlspecialchars($appointmentId); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">BN<?php echo htmlspecialchars($patientId); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">DR<?php echo htmlspecialchars($doctorId); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $dateTime = new DateTime($appointmentDate . ' ' . $appointmentTime);
                                                ?>
                                                <div>
                                                    <strong><?php echo $dateTime->format('M j, Y'); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo $dateTime->format('g:i A'); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo $typeDisplay[$appointmentType] ?? $appointmentType; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusClass[$status] ?? 'success'; ?>">
                                                    <?php echo $statusDisplay[$status] ?? $status; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    <?php echo number_format($fee, 0); ?> VND
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Paid
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="text-success">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                    <h6 class="mt-1 mb-0"><?php echo count($paid_appointments); ?></h6>
                                    <small class="text-muted">Paid Appointments</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-success">
                                    <i class="fas fa-money-bill-wave fa-lg"></i>
                                    <h6 class="mt-1 mb-0"><?php echo number_format(count($paid_appointments) * 200000, 0); ?> VND</h6>
                                    <small class="text-muted">Total Collected</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-info">
                                    <i class="fas fa-calendar-day fa-lg"></i>
                                    <h6 class="mt-1 mb-0"><?php echo date('M j, Y'); ?></h6>
                                    <small class="text-muted">Collection Date</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- No Paid Appointments Message -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-muted">
                            <i class="fas fa-info-circle"></i> Paid Appointments
                        </h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Fees Collected Yet</h6>
                        <p class="text-muted mb-0">Paid appointments will appear here after fee collection.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Fee Collection Modal -->
<div class="modal fade" id="feeCollectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave"></i> Collect Appointment Fee
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
                    <h5 id="modalPatientName">Patient Name</h5>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Appointment ID:</strong>
                        <p id="modalAppointmentId" class="text-primary"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Fee Amount:</strong>
                        <p id="modalFeeAmount" class="text-success fs-4"></p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Confirm Payment Collection</strong><br>
                    Please confirm that you have received the appointment fee from the patient.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmCollectBtn">
                    <i class="fas fa-check"></i> Confirm Collection
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAppointmentId = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips if using Bootstrap
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    function collectFee(appointmentId, feeAmount, patientName) {
        currentAppointmentId = appointmentId;

        // Update modal content
        document.getElementById('modalPatientName').textContent = patientName;
        document.getElementById('modalAppointmentId').textContent = '#' + appointmentId;
        document.getElementById('modalFeeAmount').textContent = new Intl.NumberFormat('vi-VN').format(feeAmount) + ' VND';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('feeCollectionModal'));
        modal.show();
    }

    document.getElementById('confirmCollectBtn').addEventListener('click', function() {
        if (!currentAppointmentId) return;

        const confirmBtn = this;
        const originalText = confirmBtn.innerHTML;

        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        // TODO: Replace this URL with your actual API endpoint
        const apiUrl = "<?php echo url('appointments/collectFees/'); ?>" + currentAppointmentId
        console.log("API url: ", apiUrl)

        fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    appointmentId: currentAppointmentId,
                    // collected_by: '<?php echo $_SESSION['user']['user_id'] ?? ''; ?>',
                    // collection_time: new Date().toISOString()
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("DATA :", data)
                if (data.status == 200) {
                    // Update UI
                    updateAppointmentStatus(currentAppointmentId, true);
                    showMessage('success', 'Fee collected successfully!');

                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('feeCollectionModal'));
                    modal.hide();
                } else {
                    throw new Error(data.message || 'Failed to collect fee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('danger', 'Error: ' + error.message);
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;
                currentAppointmentId = null;
            });
    });

    function updateAppointmentStatus(appointmentId, isPaid) {
        const row = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
        if (row) {
            // Update status badge
            const statusCell = row.cells[6];
            statusCell.innerHTML = isPaid ?
                '<span class="badge bg-success"><i class="fas fa-check"></i> Paid</span>' :
                '<span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>';

            // Update action button
            const actionCell = row.cells[7];
            if (isPaid) {
                const collectBtn = actionCell.querySelector('button[id^="collect-btn"]');
                if (collectBtn) {
                    collectBtn.outerHTML = '<button class="btn btn-outline-success btn-sm" disabled><i class="fas fa-check"></i> Paid</button>';
                }
            }

            // Add success animation
            row.classList.add('table-success');
            setTimeout(() => {
                row.classList.remove('table-success');
            }, 2000);
        }
    }

    function viewAppointmentDetails(appointmentId) {
        // TODO: Implement appointment details view
        window.open(`<?php echo url('appointments/view/'); ?>${appointmentId}`, '_blank');
    }

    function printReceipt(appointmentId) {
        // TODO: Implement receipt printing
        window.open(`<?php echo url('appointments/receipt/'); ?>${appointmentId}`, '_blank');
    }

    function refreshAppointments() {
        window.location.reload();
    }

    function showMessage(type, message) {
        const messageContainer = document.getElementById('messageContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        messageContainer.appendChild(alertDiv);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }
        }, 5000);
    }
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .appointment-row.table-success {
        background-color: rgba(25, 135, 84, 0.1) !important;
        transition: background-color 0.5s ease;
    }

    .paid-appointment-row {
        background-color: rgba(25, 135, 84, 0.05);
    }

    .paid-appointment-row:hover {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            margin-bottom: 2px;
            margin-right: 0;
        }
    }

    @media print {

        .btn,
        .modal,
        .no-print {
            display: none !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Collect Appointment Fees - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>