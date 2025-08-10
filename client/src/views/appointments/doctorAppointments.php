<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h3><i class="fas fa-calendar-check"></i> Doctor Appointments</h3>
            <p class="text-muted">Manage doctor appointments</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer"></div>

    <!-- Appointments Table -->
    <?php if (!empty($appointments)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Appointments List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment):
                                        $status = $appointment['TrangThai'];
                                        $canCancel = in_array($status, ['ChoXacNhan', 'DaXacNhan']);
                                    ?>
                                        <tr data-appointment-id="<?php echo $appointment['MaLichHen']; ?>">
                                            <td><?php echo $appointment['MaLichHen']; ?></td>
                                            <td>BN<?php echo $appointment['MaBenhNhan']; ?></td>
                                            <td>DR<?php echo $appointment['MaBacSi']; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($appointment['Ngay'])); ?></td>
                                            <td><?php echo date('g:i A', strtotime($appointment['Gio'])); ?></td>
                                            <td>
                                                <?php
                                                $types = [
                                                    'KhamMoi' => 'New Exam',
                                                    'TaiKham' => 'Follow-up',
                                                    'KhanCap' => 'Emergency'
                                                ];
                                                echo $types[$appointment['LoaiLichHen']] ?? $appointment['LoaiLichHen'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusLabels = [
                                                    'ChoXacNhan' => '<span class="badge bg-warning">Pending</span>',
                                                    'DaXacNhan' => '<span class="badge bg-primary">Confirmed</span>',
                                                    'DaKham' => '<span class="badge bg-success">Examined</span>',
                                                    'DaThuTien' => '<span class="badge bg-info">Fee Collected</span>',
                                                    'HoanThanh' => '<span class="badge bg-success">Completed</span>',
                                                    'DaHuy' => '<span class="badge bg-danger">Cancelled</span>'
                                                ];
                                                echo $statusLabels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($canCancel): ?>
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="cancelAppointment(<?php echo $appointment['MaLichHen']; ?>)"
                                                        id="cancel-btn-<?php echo $appointment['MaLichHen']; ?>">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">No actions</span>
                                                <?php endif; ?>
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
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No appointments found.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function cancelAppointment(appointmentId) {
        if (!confirm('Are you sure you want to cancel this appointment?')) {
            return;
        }

        const button = document.getElementById(`cancel-btn-${appointmentId}`);
        const originalText = button.innerHTML;

        // Show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';

        const baseUrl = "<?php echo url('/appointments/cancel'); ?>";
        const fullUrl = `${baseUrl}/${appointmentId}`;

        // Make API call
        fetch(fullUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 || data.success) {
                    // Update UI
                    const row = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
                    const statusCell = row.cells[6];
                    const actionCell = row.cells[7];

                    statusCell.innerHTML = '<span class="badge bg-danger">Cancelled</span>';
                    actionCell.innerHTML = '<span class="text-muted">Cancelled</span>';

                    showMessage('Appointment cancelled successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to cancel appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Failed to cancel appointment. Please try again.', 'danger');

                // Restore button
                button.disabled = false;
                button.innerHTML = originalText;
            });
    }

    function showMessage(message, type) {
        const container = document.getElementById('messageContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
        container.appendChild(alert);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Doctor Appointments - Hospital Management';
include __DIR__ . '/../layouts/main.php';
?>