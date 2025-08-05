<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-plus"></i> Patient Registration</h1>
                </div>
                <div>

                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> New Patient Information</h5>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="messageContainer"></div>

                    <form id="createPatientForm" novalidate>
                        <!-- Personal Information Section -->
                        <div class="border-bottom pb-4 mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h6>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="HoTen" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="HoTen" name="HoTen"
                                        placeholder="Enter patient's full name" required>
                                    <div class="invalid-feedback">Please provide a valid full name.</div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="GioiTinh" class="form-label">Gender *</label>
                                    <select class="form-select" id="GioiTinh" name="GioiTinh" required>
                                        <option value="">Select Gender</option>
                                        <option value="Nam">Male (Nam)</option>
                                        <option value="Nữ">Female (Nữ)</option>
                                        <option value="Khác">Other (Khác)</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a gender.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="NgaySinh" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" required>
                                    <div class="invalid-feedback">Please provide a valid date of birth.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="SoDinhDanh" class="form-label">ID Number (CCCD) *</label>
                                    <input type="text" class="form-control" id="SoDinhDanh" name="SoDinhDanh"
                                        pattern="[0-9]{12}" placeholder="123456789012" maxlength="12" required>
                                    <div class="invalid-feedback">Please provide a valid 12-digit ID number.</div>
                                    <small class="text-muted">12-digit National ID (CCCD)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="border-bottom pb-4 mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="SoDienThoai" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai"
                                        pattern="[0-9]{10,11}" placeholder="0123456789" required>
                                    <div class="invalid-feedback">Please provide a valid 10-11 digit phone number.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="Email" name="Email"
                                        placeholder="patient@example.com" required>
                                    <div class="invalid-feedback">Please provide a valid email address.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="DiaChi" class="form-label">Address</label>
                                <textarea class="form-control" id="DiaChi" name="DiaChi" rows="3"
                                    placeholder="Enter patient's full address (optional)"></textarea>
                                <small class="text-muted">Optional - Patient's residential address</small>
                            </div>
                        </div>

                        <!-- Insurance & Medical Information Section -->
                        <div class="border-bottom pb-4 mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-shield-alt"></i> Insurance & Medical Information</h6>

                            <div class="mb-3">
                                <label for="BaoHiemYTe" class="form-label">Health Insurance Number</label>
                                <input type="text" class="form-control" id="BaoHiemYTe" name="BaoHiemYTe"
                                    placeholder="Enter health insurance number (optional)">
                                <small class="text-muted">Optional - Social health insurance number</small>
                            </div>

                            <div class="mb-3">
                                <label for="TienSu" class="form-label">Medical History</label>
                                <input type="text" class="form-control" id="TienSu" name="TienSu"
                                    placeholder="Enter medical history ">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Patient Status</strong>
                                </label>
                                <small class="text-muted d-block">Check to set patient as active (recommended for new registrations)</small>
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
                                            minlength="6" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">Password must be at least 6 characters.</div>
                                    <small class="text-muted">Minimum 6 characters for patient portal access</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ConfirmPassword" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="ConfirmPassword" name="ConfirmPassword" required>
                                    <div class="invalid-feedback">Passwords do not match.</div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Patient Portal Access:</strong> The system will automatically generate a patient ID (BN + number) for portal login.
                                The patient can use this ID and password to access their medical records online.
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="previewPatient()">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-user-plus"></i> Register Patient
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
                <h5 class="modal-title"><i class="fas fa-eye"></i> Patient Information Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitFormFromPreview()">
                    <i class="fas fa-user-plus"></i> Confirm & Register
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('createPatientForm');
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

        // ID Number validation (only numbers)
        document.getElementById('SoDinhDanh').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
        });

        // Phone number validation (only numbers)
        document.getElementById('SoDienThoai').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';

            const formData = new FormData(form);

            // Convert is_active checkbox to boolean
            if (formData.has('is_active')) {
                formData.set('is_active', 'true');
            } else {
                formData.set('is_active', 'false');
            }

            // Remove confirm password
            formData.delete('ConfirmPassword');

            console.log('Submitting patient data...');
            // for (let pair of formData.entries()) {
            //     console.log(pair[0] + ': ' + pair[1]);
            // }

            console.log("form data", formData)

            fetch('<?php echo url("deskStaff/submitCreatePatient"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log("Response status:", response.status);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);

                    if (data && data.success) {
                        showMessage('success', 'Patient registered successfully! Patient ID: ' + (data.patient_id || 'Generated'));
                        form.reset();
                        form.classList.remove('was-validated');

                        // Redirect after 3 seconds
                        setTimeout(() => {
                            window.location.href = '<?php echo url("dashboard"); ?>';
                        }, 3000);
                    } else {
                        showMessage('danger', 'Error: ' + (data?.message || 'Failed to register patient'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Network error occurred. Please try again: ' + error.message);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Register Patient';
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

            // Auto-hide after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(() => {
                    const alert = messageContainer.querySelector('.alert');
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            }
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

        window.previewPatient = function() {
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);

            const previewContent = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Personal Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Full Name:</strong></td><td>${formData.get('HoTen')}</td></tr>
                        <tr><td><strong>Gender:</strong></td><td>${formData.get('GioiTinh')}</td></tr>
                        <tr><td><strong>Date of Birth:</strong></td><td>${new Date(formData.get('NgaySinh')).toLocaleDateString()}</td></tr>
                        <tr><td><strong>ID Number:</strong></td><td>${formData.get('SoDinhDanh')}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Contact Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Phone:</strong></td><td>${formData.get('SoDienThoai')}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${formData.get('Email')}</td></tr>
                        <tr><td><strong>Address:</strong></td><td>${formData.get('DiaChi') || 'Not provided'}</td></tr>
                        <tr><td><strong>Insurance:</strong></td><td>${formData.get('BaoHiemYTe') || 'Not provided'}</td></tr>
                    </table>
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <strong>Note:</strong> A patient ID will be automatically generated (BN + number) for portal access.
            </div>
        `;

            document.getElementById('previewContent').innerHTML = previewContent;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        };
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
$title = 'Create Patient - Desk Staff';
include __DIR__ . '/../layouts/main.php';
?>