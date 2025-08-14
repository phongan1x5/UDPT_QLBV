<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Get staff statistics
$totalStaff = is_array($staffs) ? count($staffs) : 0;
$staffByType = [];
$staffWithEmail = 0;

if (is_array($staffs) && !empty($staffs)) {
    foreach ($staffs as $staff) {
        // Count by staff type
        $type = $staff['LoaiNhanVien'] ?? 'Unknown';
        $staffByType[$type] = ($staffByType[$type] ?? 0) + 1;

        // Count staff with email
        if (!empty($staff['Email'])) {
            $staffWithEmail++;
        }
    }
}
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-broadcast-tower"></i> Broadcast Notification to Staff</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $totalStaff; ?></h4>
                            <p class="mb-0">Total Staff</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $staffWithEmail; ?></h4>
                            <p class="mb-0">With Email</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo count($staffByType); ?></h4>
                            <p class="mb-0">Staff Types</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-briefcase fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo ($totalStaff - $staffWithEmail); ?></h4>
                            <p class="mb-0">Without Email</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Broadcast Form -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane"></i> Compose Broadcast Message</h5>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="messageContainer"></div>

                    <form id="broadcastForm" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sourceSystem" class="form-label">Source System *</label>
                                <input type="text" class="form-control" id="sourceSystem" name="sourceSystem"
                                    value="ADMIN" required readonly>
                                <div class="form-text">The system sending this notification.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="recipientCount" class="form-label">Recipients</label>
                                <input type="text" class="form-control" id="recipientCount"
                                    value="<?php echo $staffWithEmail; ?> staff members" readonly>
                                <div class="form-text">Only staff members with email addresses will receive notifications.</div>
                            </div>

                            <div class="col-12 mb-4">
                                <label for="message" class="form-label">Message Content *</label>
                                <textarea class="form-control" id="message" name="message" rows="6"
                                    placeholder="Enter your broadcast message here..." required></textarea>
                                <div class="invalid-feedback">Please provide a message to broadcast.</div>
                                <div class="form-text">
                                    <span id="charCount">0</span> / 500 characters
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmBroadcast" required>
                                    <label class="form-check-label" for="confirmBroadcast">
                                        I confirm that I want to send this message to <strong><?php echo $staffWithEmail; ?> staff members</strong>
                                    </label>
                                    <div class="invalid-feedback">Please confirm before broadcasting.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="previewBtn">
                                <i class="fas fa-eye"></i> Preview Message
                            </button>
                            <div>
                                <button type="button" class="btn btn-info me-2" id="testSingleBtn">
                                    <i class="fas fa-vial"></i> Test Send (Admin Only)
                                </button>
                                <button type="submit" class="btn btn-primary" id="broadcastBtn">
                                    <i class="fas fa-broadcast-tower"></i> Broadcast to All Staff
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Recipients Preview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Recipients Preview</h5>
                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse"
                        data-bs-target="#recipientsList" aria-expanded="false">
                        <i class="fas fa-eye"></i> Show/Hide Recipients
                    </button>
                </div>
                <div class="collapse" id="recipientsList">
                    <div class="card-body">
                        <?php if (empty($staffs)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No staff members found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Staff ID</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Email</th>
                                            <th>Will Receive</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staffs as $staff): ?>
                                            <?php
                                            $hasEmail = !empty($staff['Email']);
                                            $typeColors = [
                                                'admin' => 'danger',
                                                'doctor' => 'primary',
                                                'nurse' => 'info',
                                                'desk_staff' => 'warning',
                                                'lab_staff' => 'success'
                                            ];
                                            $typeColor = $typeColors[$staff['LoaiNhanVien']] ?? 'secondary';
                                            ?>
                                            <tr class="<?php echo $hasEmail ? '' : 'table-warning'; ?>">
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($staff['MaNhanVien']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($staff['HoTen']); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $typeColor; ?>">
                                                        <?php echo htmlspecialchars($staff['LoaiNhanVien']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($hasEmail): ?>
                                                        <i class="fas fa-envelope text-success me-1"></i>
                                                        <?php echo htmlspecialchars($staff['Email']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-envelope-slash me-1"></i>
                                                            No email
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($hasEmail): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Yes
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times"></i> No
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Message Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Email Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>From:</strong> ADMIN System<br>
                                <strong>To:</strong> <span id="previewRecipientCount"><?php echo $staffWithEmail; ?></span> staff members<br>
                                <strong>Subject:</strong> <span id="previewSubject">Notification from ADMIN</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Type:</strong> Broadcast Notification<br>
                                <strong>Priority:</strong> Normal<br>
                                <strong>Delivery:</strong> Asynchronous
                            </div>
                        </div>
                        <hr>
                        <div class="border p-3 bg-light rounded">
                            <div id="previewMessage">Your message will appear here...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                <button type="button" class="btn btn-primary" onclick="submitBroadcast()">
                    <i class="fas fa-paper-plane"></i> Send Broadcast
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Staff data for JavaScript
    const staffData = <?php echo json_encode($staffs); ?>;
    const staffWithEmailData = staffData.filter(staff => staff.Email && staff.Email.trim() !== '');

    // Staff type mapping
    const staffTypeMapping = {
        "doctor": "DR",
        "desk_staff": "DS",
        "lab_staff": "LS",
        "nurse": "NS",
        "pharmacist": "PH",
        "admin": "AD"
    };

    // Function to generate formatted UserID
    function generateUserId(staff) {
        const typeCode = staffTypeMapping[staff.LoaiNhanVien] || "UN"; // UN = Unknown
        const staffNumber = staff.MaNhanVien;
        return `${typeCode}${staffNumber}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        const broadcastForm = document.getElementById('broadcastForm');
        const previewBtn = document.getElementById('previewBtn');
        const testSingleBtn = document.getElementById('testSingleBtn');
        const broadcastBtn = document.getElementById('broadcastBtn');

        // Character counter
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            charCount.className = length > 400 ? 'text-danger' : length > 300 ? 'text-warning' : 'text-success';

            if (length > 500) {
                this.value = this.value.substring(0, 500);
                charCount.textContent = '500';
            }
        });

        // Preview message
        previewBtn.addEventListener('click', function() {
            const message = messageTextarea.value.trim();
            if (!message) {
                showMessage('warning', 'Please enter a message to preview.', document.getElementById('messageContainer'));
                return;
            }

            document.getElementById('previewMessage').innerHTML = message.replace(/\n/g, '<br>');
            document.getElementById('previewSubject').textContent = 'Notification from ADMIN';
            document.getElementById('previewRecipientCount').textContent = staffWithEmailData.length;

            new bootstrap.Modal(document.getElementById('previewModal')).show();
        });

        // Test single send (to admin only)
        testSingleBtn.addEventListener('click', function() {
            const message = messageTextarea.value.trim();
            if (!message) {
                showMessage('warning', 'Please enter a message to test.', document.getElementById('messageContainer'));
                return;
            }

            if (confirm('This will send a test notification to the admin email only. Continue?')) {
                const adminStaff = staffData.find(staff => staff.LoaiNhanVien === 'admin' && staff.Email);
                if (adminStaff) {
                    console.log('Test send to admin:', generateUserId(adminStaff)); // Debug log
                    sendBroadcast([adminStaff], 'TEST - ' + message);
                } else {
                    showMessage('danger', 'No admin email found for testing.', document.getElementById('messageContainer'));
                }
            }
        });

        // Form submission
        broadcastForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (broadcastForm.checkValidity()) {
                const message = messageTextarea.value.trim();

                if (confirm(`Are you sure you want to broadcast this message to ${staffWithEmailData.length} staff members?\n\nThis action cannot be undone.`)) {
                    submitBroadcast();
                }
            } else {
                broadcastForm.classList.add('was-validated');
            }
        });

        function submitBroadcast() {
            const message = messageTextarea.value.trim();
            sendBroadcast(staffWithEmailData, message);
        }

        function sendBroadcast(recipients, message) {
            broadcastBtn.disabled = true;
            broadcastBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Broadcasting...';

            // 🆕 Prepare users data with formatted UserID (role prefix + staff number)
            const users = recipients.map(staff => {
                const formattedUserId = generateUserId(staff);
                console.log(`Staff: ${staff.HoTen} (${staff.LoaiNhanVien}) - UserID: ${formattedUserId}`); // Debug log

                return {
                    userId: formattedUserId,
                    userEmail: staff.Email
                };
            });

            const broadcastData = {
                sourceSystem: 'ADMIN',
                users: users,
                message: message
            };

            console.log('Broadcasting to users:', users); // Debug log

            fetch('<?php echo url('admin/broadcast-notification'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(broadcastData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', `
                    <strong>Broadcast sent successfully!</strong><br>
                    <strong>Total Recipients:</strong> ${data.totalUsers}<br>
                    <strong>Successfully Queued:</strong> ${data.successfullyQueued}<br>
                    ${data.failedToQueue > 0 ? `<strong>Failed:</strong> ${data.failedToQueue}<br>` : ''}
                    <em>Notifications are being processed asynchronously.</em>
                `, document.getElementById('messageContainer'));

                        // Reset form after successful broadcast
                        setTimeout(() => {
                            messageTextarea.value = '';
                            document.getElementById('confirmBroadcast').checked = false;
                            broadcastForm.classList.remove('was-validated');
                            charCount.textContent = '0';
                        }, 2000);

                    } else {
                        showMessage('danger', 'Error: ' + (data.message || 'Failed to send broadcast'),
                            document.getElementById('messageContainer'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Network error occurred. Please try again.',
                        document.getElementById('messageContainer'));
                })
                .finally(() => {
                    broadcastBtn.disabled = false;
                    broadcastBtn.innerHTML = '<i class="fas fa-broadcast-tower"></i> Broadcast to All Staff';
                });
        }

        // Close any open modals after successful broadcast
        window.submitBroadcast = submitBroadcast;

        // Utility function to show messages
        function showMessage(type, message, container) {
            const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            container.innerHTML = alertHtml;
            container.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }

    #message {
        resize: vertical;
        min-height: 120px;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Broadcast Notification to Staff - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>