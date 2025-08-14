<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// print_r($user['token']);
// print_r($departments);
// // print_r($admin);
// echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-plus"></i> Create New Staff Member</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('admin/staff'); ?>">Staff Management</a></li>
                            <li class="breadcrumb-item active">Create Staff</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo url('admin/staff'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Staff List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> Staff Information</h5>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="messageContainer"></div>

                    <form id="createStaffForm" novalidate>
                        <!-- Personal Information Section -->
                        <div class="border-bottom pb-4 mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="HoTen" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="HoTen" name="HoTen" required>
                                    <div class="invalid-feedback">Please provide a valid full name.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="NgaySinh" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" required>
                                    <div class="invalid-feedback">Please provide a valid date of birth.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="SoDienThoai" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai"
                                        pattern="[0-9]{10,11}" placeholder="0123456789" required>
                                    <div class="invalid-feedback">Please provide a valid 10-11 digit phone number.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="SoDinhDanh" class="form-label">ID Number (CCCD) *</label>
                                    <input type="text" class="form-control" id="SoDinhDanh" name="SoDinhDanh"
                                        pattern="[0-9]{12}" placeholder="123456789012" maxlength="12" required>
                                    <div class="invalid-feedback">Please provide a valid 12-digit ID number.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="DiaChi" class="form-label">Address *</label>
                                <textarea class="form-control" id="DiaChi" name="DiaChi" rows="3"
                                    placeholder="Enter full address..." required></textarea>
                                <div class="invalid-feedback">Please provide a valid address.</div>
                            </div>
                        </div>

                        <!-- Employment Information Section -->
                        <div class="border-bottom pb-4 mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-briefcase"></i> Employment Information</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="IDPhongBan" class="form-label">Department *</label>
                                    <select class="form-select" id="IDPhongBan" name="IDPhongBan" required>
                                        <option value="">Select Department</option>
                                        <?php if (!empty($departments)): ?>
                                            <?php foreach ($departments as $department): ?>
                                                <option value="<?php echo htmlspecialchars($department['IDPhongBan']); ?>">
                                                    <?php echo htmlspecialchars($department['TenPhongBan']); ?>
                                                    <?php if (!empty($department['IDTruongPhongBan'])): ?>
                                                        (Manager ID: <?php echo htmlspecialchars($department['IDTruongPhongBan']); ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No departments available</option>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a department.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="LoaiNhanVien" class="form-label">Staff Type *</label>
                                    <select class="form-select" id="LoaiNhanVien" name="LoaiNhanVien" required>
                                        <option value="">Select Staff Type</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="desk_staff">Desk Staff</option>
                                        <option value="lab_staff">Lab Staff</option>
                                        <option value="pharmacist">Pharmacist</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a staff type.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ChuyenKhoa" class="form-label">Specialization</label>
                                <input type="text" class="form-control" id="ChuyenKhoa" name="ChuyenKhoa"
                                    placeholder="e.g., Cardiology, Internal Medicine, etc.">
                                <small class="text-muted">Optional - Enter medical specialization for doctors or area of expertise for other staff</small>
                            </div>
                        </div>

                        <!-- Login Information Section -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-key"></i> Login Information</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="Password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="Password" name="Password"
                                            minlength="6" pattern="^(?=.*[a-z])(?=.*\d).{8,}$" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">Password must be at least 8 characters with uppercase, lowercase, and number.</div>
                                    <small class="text-muted">Must contain at least 8 characters, at least 1 number</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ConfirmPassword" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="ConfirmPassword" name="ConfirmPassword" required>
                                    <div class="invalid-feedback">Passwords do not match.</div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Auto-generated User ID:</strong> The system will automatically generate a user ID based on staff type:
                                <ul class="mb-0 mt-2">
                                    <li><strong>Doctor:</strong> DR + Staff ID (e.g., DR15)</li>
                                    <li><strong>Desk Staff:</strong> DS + Staff ID (e.g., DS15)</li>
                                    <li><strong>Lab Staff:</strong> LS + Staff ID (e.g., LS15)</li>
                                    <li><strong>Pharmacist:</strong> PH + Staff ID (e.g., PH15)</li>
                                    <li><strong>Administrator:</strong> AD + Staff ID (e.g., AD15)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="previewStaff()">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Create Staff Member
                                </button>
                            </div>
                        </div>
                    </form>
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
                <h5 class="modal-title"><i class="fas fa-eye"></i> Staff Information Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitFormFromPreview()">
                    <i class="fas fa-save"></i> Confirm & Create
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('createStaffForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageContainer = document.getElementById('messageContainer');

        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('Password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Password confirmation validation
        document.getElementById('ConfirmPassword').addEventListener('input', function() {
            const password = document.getElementById('Password').value;
            const confirmPassword = this.value;

            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            submitForm();
        });

        function submitForm() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

            const formData = new FormData(form);
            const staffData = Object.fromEntries(formData.entries());

            // Remove confirm password from data
            delete staffData.ConfirmPassword;

            // Convert IDPhongBan to integer
            staffData.IDPhongBan = parseInt(staffData.IDPhongBan);
            console.log(formData)

            fetch('<?php echo url("admin/submitCreateStaff"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log("test", response)
                    console.log("Response status:", response.status);
                    console.log("Response ok:", response.ok);
                    return response.json()
                })
                .then(data => {
                    console.log(data)
                    if (data.success) {
                        showMessage('success', 'Staff member created successfully! User ID: ' + data.user_id);
                        form.reset();
                        form.classList.remove('was-validated');

                        // Redirect after 2 seconds
                        // setTimeout(() => {
                        //     window.location.href = '<?php echo url("admin/staff"); ?>';
                        // }, 2000);
                    } else {
                        showMessage('danger', 'Error: ' + (data.message || 'Failed to create staff member'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Network error occurred. Please try again.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Create Staff Member';
                });
        }

        function showMessage(type, message) {
            messageContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

            // Auto-hide after 5 seconds
            setTimeout(() => {
                const alert = messageContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Make functions global
        window.submitFormFromPreview = function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
            modal.hide();
            submitForm();
        };

        window.resetForm = function() {
            form.reset();
            form.classList.remove('was-validated');
            messageContainer.innerHTML = '';
        };

        window.previewStaff = function() {
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);
            const staffData = Object.fromEntries(formData.entries());

            // ✅ UPDATED: Get selected department text dynamically
            const selectedDepartment = document.getElementById('IDPhongBan');
            const departmentText = selectedDepartment.selectedOptions[0] ?
                selectedDepartment.selectedOptions[0].text : 'Not selected';

            const selectedStaffType = document.getElementById('LoaiNhanVien');
            const staffTypeText = selectedStaffType.selectedOptions[0] ?
                selectedStaffType.selectedOptions[0].text : 'Not selected';

            const previewContent = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Personal Information</h6>
                        <table class="table table-sm table-striped">
                            <tr><td><strong>Full Name:</strong></td><td>${staffData.HoTen}</td></tr>
                            <tr><td><strong>Date of Birth:</strong></td><td>${new Date(staffData.NgaySinh).toLocaleDateString()}</td></tr>
                            <tr><td><strong>Phone:</strong></td><td>${staffData.SoDienThoai}</td></tr>
                            <tr><td><strong>ID Number:</strong></td><td>${staffData.SoDinhDanh}</td></tr>
                            <tr><td><strong>Address:</strong></td><td>${staffData.DiaChi}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Employment Information</h6>
                        <table class="table table-sm table-striped">
                            <tr><td><strong>Department:</strong></td><td>${departmentText}</td></tr>
                            <tr><td><strong>Staff Type:</strong></td><td>${staffTypeText}</td></tr>
                            <tr><td><strong>Specialization:</strong></td><td>${staffData.ChuyenKhoa || 'Not specified'}</td></tr>
                            <tr><td><strong>Will generate User ID:</strong></td><td class="text-primary fw-bold">${getExpectedUserId(staffData.LoaiNhanVien)}</td></tr>
                        </table>
                    </div>
                </div>
                
                <!-- ✅ NEW: Department Details -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-building"></i> Department Assignment</h6>
                            <p class="mb-0">
                                <strong>Department ID:</strong> ${staffData.IDPhongBan} | 
                                <strong>Department Name:</strong> ${departmentText.split('(')[0].trim()}
                            </p>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('previewContent').innerHTML = previewContent;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        };

        function getExpectedUserId(staffType) {
            const prefixes = {
                'doctor': 'DR',
                'desk_staff': 'DS',
                'lab_staff': 'LS',
                'pharmacist': 'PH',
                'admin': 'AD'
            };
            return `${prefixes[staffType] || ''}[NextID]`;
        }
    });
</script>

<style>
    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .was-validated .form-control:valid {
        border-color: #198754;
    }

    .was-validated .form-control:invalid {
        border-color: #dc3545;
    }

    .alert {
        border: none;
        border-radius: 8px;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .border-bottom {
        border-color: #e9ecef !important;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Create Staff Member - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>