<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($departments);
// echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-edit"></i> Update Patient Information</h1>
                </div>
                <div>
                    <a href="<?php echo url('admin/patients'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Patient List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Patient Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-search"></i> Find Patient</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="patientIdSearch" class="form-label">Patient ID</label>
                            <input type="number" id="patientIdSearch" class="form-control" placeholder="Enter patient ID (e.g., 1, 2, 3...)">
                            <div class="form-text">Enter the numeric patient ID to search for the patient.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="searchPatientBtn">
                                <i class="fas fa-search"></i> Search Patient
                            </button>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Form Section (Hidden Initially) -->
    <div class="row" id="updateFormSection" style="display: none;">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Update Patient Information</h5>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="messageContainer"></div>

                    <form id="updatePatientForm" novalidate>
                        <input type="hidden" id="patientId" name="patientId">

                        <div class="row">
                            <!-- Personal Information -->
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

                            <div class="col-md-6 mb-3">
                                <label for="GioiTinh" class="form-label">Gender *</label>
                                <select class="form-select" id="GioiTinh" name="GioiTinh" required>
                                    <option value="">Select Gender</option>
                                    <option value="Nam">Male (Nam)</option>
                                    <option value="Nữ">Female (Nữ)</option>
                                    <option value="Khác">Other (Khác)</option>
                                </select>
                                <div class="invalid-feedback">Please select a gender.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="SoDienThoai" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai" required
                                    pattern="[0-9]{10,11}" placeholder="0123456789">
                                <div class="invalid-feedback">Please provide a valid phone number (10-11 digits).</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="SoDinhDanh" class="form-label">National ID Number *</label>
                                <input type="text" class="form-control" id="SoDinhDanh" name="SoDinhDanh" required
                                    pattern="[0-9]{9,12}" placeholder="123456789012">
                                <div class="invalid-feedback">Please provide a valid national ID number (9-12 digits).</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="Email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="Email" name="Email" required
                                    placeholder="patient@example.com">
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="DiaChi" class="form-label">Address</label>
                                <textarea class="form-control" id="DiaChi" name="DiaChi" rows="3"
                                    placeholder="Enter full address (optional)"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="BaoHiemYTe" class="form-label">Health Insurance Number</label>
                                <input type="text" class="form-control" id="BaoHiemYTe" name="BaoHiemYTe"
                                    placeholder="Health insurance number (optional)">
                                <div class="form-text">Optional field for health insurance information.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">Status *</label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback">Please select patient status.</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" id="cancelUpdateBtn">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-info me-2" id="previewUpdateBtn">
                                            <i class="fas fa-eye"></i> Preview Changes
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitUpdateBtn">
                                            <i class="fas fa-save"></i> Update Patient
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Changes Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Preview Patient Updates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmUpdateBtn">
                    <i class="fas fa-save"></i> Confirm Update
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchPatientBtn');
        const patientIdSearch = document.getElementById('patientIdSearch');
        const searchResults = document.getElementById('searchResults');
        const updateFormSection = document.getElementById('updateFormSection');
        const updateForm = document.getElementById('updatePatientForm');
        const cancelBtn = document.getElementById('cancelUpdateBtn');
        const previewBtn = document.getElementById('previewUpdateBtn');
        const submitBtn = document.getElementById('submitUpdateBtn');

        let currentPatientData = null;

        // Search for patient
        searchBtn.addEventListener('click', searchPatient);
        patientIdSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPatient();
            }
        });

        function searchPatient() {
            const patientId = patientIdSearch.value.trim();

            if (!patientId) {
                showMessage('warning', 'Please enter a patient ID.', searchResults);
                return;
            }

            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

            fetch(`<?php echo url('admin/findPatient'); ?>/${patientId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.patient) {
                        console.log(data.patient)
                        data.patient = data.patient[0]
                        currentPatientData = data.patient;
                        displayPatientFound(data.patient);
                        populateUpdateForm(data.patient);
                    } else {
                        showMessage('danger', `Patient with ID ${patientId} not found.`, searchResults);
                        hideUpdateForm();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Error searching for patient. Please try again.', searchResults);
                    hideUpdateForm();
                })
                .finally(() => {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = '<i class="fas fa-search"></i> Search Patient';
                });
        }

        function displayPatientFound(patient) {
            const statusBadge = patient.is_active ?
                '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-danger">Inactive</span>';

            const patientCard = `
            <div class="alert alert-success mt-3">
                <h6 class="alert-heading"><i class="fas fa-check-circle"></i> Patient Found!</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong> ${patient.HoTen}<br>
                        <strong>Gender:</strong> ${patient.GioiTinh}<br>
                        <strong>Phone:</strong> ${patient.SoDienThoai}<br>
                        <strong>Email:</strong> ${patient.Email}
                    </div>
                    <div class="col-md-6">
                        <strong>Birth Date:</strong> ${new Date(patient.NgaySinh).toLocaleDateString()}<br>
                        <strong>ID Number:</strong> ${patient.SoDinhDanh}<br>
                        <strong>Insurance:</strong> ${patient.BaoHiemYTe || 'Not provided'}<br>
                        <strong>Status:</strong> ${statusBadge}
                    </div>
                </div>
                <hr>
                <p class="mb-0">You can now update this patient's information below.</p>
            </div>
        `;

            searchResults.innerHTML = patientCard;
            updateFormSection.style.display = 'block';
            updateFormSection.scrollIntoView({
                behavior: 'smooth'
            });
        }

        function populateUpdateForm(patient) {
            document.getElementById('patientId').value = patient.id;
            document.getElementById('HoTen').value = patient.HoTen || '';
            document.getElementById('NgaySinh').value = patient.NgaySinh || '';
            document.getElementById('GioiTinh').value = patient.GioiTinh || '';
            document.getElementById('SoDienThoai').value = patient.SoDienThoai || '';
            document.getElementById('SoDinhDanh').value = patient.SoDinhDanh || '';
            document.getElementById('Email').value = patient.Email || '';
            document.getElementById('DiaChi').value = patient.DiaChi || '';
            document.getElementById('BaoHiemYTe').value = patient.BaoHiemYTe || '';
            document.getElementById('is_active').value = patient.is_active ? '1' : '0';
        }

        function hideUpdateForm() {
            updateFormSection.style.display = 'none';
            currentPatientData = null;
        }

        // Cancel update
        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
                hideUpdateForm();
                searchResults.innerHTML = '';
                patientIdSearch.value = '';
                updateForm.reset();
                updateForm.classList.remove('was-validated');
            }
        });

        // Preview changes
        previewBtn.addEventListener('click', function() {
            if (!updateForm.checkValidity()) {
                updateForm.classList.add('was-validated');
                return;
            }

            const formData = new FormData(updateForm);
            const newData = Object.fromEntries(formData.entries());

            showPreview(currentPatientData, newData);
        });

        function showPreview(oldData, newData) {
            const previewContent = `
            <div class="row">
                <div class="col-12">
                    <h6 class="text-primary">Patient Update Preview</h6>
                    <p class="text-muted">Review the changes before confirming the update.</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success">Current Information</h6>
                    <table class="table table-sm table-striped">
                        <tr><td><strong>Full Name:</strong></td><td>${oldData.HoTen}</td></tr>
                        <tr><td><strong>Date of Birth:</strong></td><td>${oldData.NgaySinh}</td></tr>
                        <tr><td><strong>Gender:</strong></td><td>${oldData.GioiTinh}</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>${oldData.SoDienThoai}</td></tr>
                        <tr><td><strong>ID Number:</strong></td><td>${oldData.SoDinhDanh}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${oldData.Email}</td></tr>
                        <tr><td><strong>Address:</strong></td><td>${oldData.DiaChi || 'Not provided'}</td></tr>
                        <tr><td><strong>Insurance:</strong></td><td>${oldData.BaoHiemYTe || 'Not provided'}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>${oldData.is_active ? 'Active' : 'Inactive'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-warning">New Information</h6>
                    <table class="table table-sm table-striped">
                        <tr><td><strong>Full Name:</strong></td><td>${newData.HoTen}</td></tr>
                        <tr><td><strong>Date of Birth:</strong></td><td>${newData.NgaySinh}</td></tr>
                        <tr><td><strong>Gender:</strong></td><td>${newData.GioiTinh}</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>${newData.SoDienThoai}</td></tr>
                        <tr><td><strong>ID Number:</strong></td><td>${newData.SoDinhDanh}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${newData.Email}</td></tr>
                        <tr><td><strong>Address:</strong></td><td>${newData.DiaChi || 'Not provided'}</td></tr>
                        <tr><td><strong>Insurance:</strong></td><td>${newData.BaoHiemYTe || 'Not provided'}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>${newData.is_active === '1' ? 'Active' : 'Inactive'}</td></tr>
                    </table>
                </div>
            </div>
        `;

            document.getElementById('previewContent').innerHTML = previewContent;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }

        // Confirm update from modal
        document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
            bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
            submitUpdate();
        });

        // Form submission
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (updateForm.checkValidity()) {
                submitUpdate();
            } else {
                updateForm.classList.add('was-validated');
            }
        });

        function submitUpdate() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

            const formData = new FormData(updateForm);
            const patientId = formData.get('patientId');
            console.log("check patient_id: ", patientId)

            // Remove patientId from form data as it shouldn't be in the update payload
            formData.delete('patientId');
            const updateData = Object.fromEntries(formData.entries());

            // Convert is_active to boolean for the API
            updateData.is_active = updateData.is_active === '1';

            fetch(`<?php echo url('admin/updatePatient'); ?>/${patientId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(updateData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', `
                    <strong>Patient updated successfully!</strong><br>
                    <strong>Patient ID:</strong> ${patientId}<br>
                    <strong>Name:</strong> ${updateData.HoTen}<br>
                    <strong>Status:</strong> ${updateData.is_active ? 'Active' : 'Inactive'}
                `, document.getElementById('messageContainer'));

                        // Scroll to success message
                        document.getElementById('messageContainer').scrollIntoView({
                            behavior: 'smooth'
                        });

                        // Reset form after delay
                        setTimeout(() => {
                            hideUpdateForm();
                            searchResults.innerHTML = '';
                            patientIdSearch.value = '';
                            updateForm.reset();
                            updateForm.classList.remove('was-validated');
                            document.getElementById('messageContainer').innerHTML = '';
                        }, 3000);
                    } else {
                        showMessage('danger', 'Error: ' + (data.message || 'Failed to update patient'), document.getElementById('messageContainer'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Network error occurred. Please try again.', document.getElementById('messageContainer'));
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Patient';
                });
        }

        // Utility function to show messages
        function showMessage(type, message, container) {
            const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            container.innerHTML = alertHtml;
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

    .btn-group .btn {
        border: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }

        .d-flex.justify-content-between>div {
            width: 100%;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Update Patient - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>