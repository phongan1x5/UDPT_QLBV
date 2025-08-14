<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($departments);
// echo '</pre>';
// Get departments for the dropdown
$staffModel = new Staff();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-edit"></i> Update Staff Member</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('admin/staff'); ?>">Staff Management</a></li>
                            <li class="breadcrumb-item active">Update Staff</li>
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

    <!-- Search Staff Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-search"></i> Find Staff Member</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="staffIdSearch" class="form-label">Staff ID</label>
                            <input type="number" id="staffIdSearch" class="form-control" placeholder="Enter staff ID (e.g., 1, 2, 3...)">
                            <div class="form-text">Enter the numeric staff ID to search for the staff member.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="searchStaffBtn">
                                <i class="fas fa-search"></i> Search Staff
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
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Update Staff Information</h5>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="messageContainer"></div>

                    <form id="updateStaffForm" novalidate>
                        <input type="hidden" id="staffId" name="staffId">

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
                                <label for="SoDienThoai" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai" required
                                    pattern="[0-9]{10,11}" placeholder="0123456789">
                                <div class="invalid-feedback">Please provide a valid phone number (10-11 digits).</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="SoDinhDanh" class="form-label">ID Number *</label>
                                <input type="text" class="form-control" id="SoDinhDanh" name="SoDinhDanh" required
                                    pattern="[0-9]{9,12}" placeholder="123456789012">
                                <div class="invalid-feedback">Please provide a valid ID number (9-12 digits).</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="DiaChi" class="form-label">Address *</label>
                                <textarea class="form-control" id="DiaChi" name="DiaChi" rows="3" required
                                    placeholder="Enter full address"></textarea>
                                <div class="invalid-feedback">Please provide a valid address.</div>
                            </div>

                            <!-- Employment Information -->
                            <div class="col-md-6 mb-3">
                                <label for="IDPhongBan" class="form-label">Department *</label>
                                <select class="form-select" id="IDPhongBan" name="IDPhongBan" required>
                                    <option value="">Select Department</option>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $department): ?>
                                            <option value="<?php echo htmlspecialchars($department['IDPhongBan']); ?>">
                                                <?php echo htmlspecialchars($department['TenPhongBan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">Please select a department.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="LoaiNhanVien" class="form-label">Staff Type *</label>
                                <select class="form-select" id="LoaiNhanVien" name="LoaiNhanVien" required>
                                    <option value="">Select Staff Type</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="nurse">Nurse</option>
                                    <option value="desk_staff">Desk Staff</option>
                                    <option value="lab_staff">Lab Staff</option>
                                    <option value="pharmacist">Pharmacist</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <div class="invalid-feedback">Please select a staff type.</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="ChuyenKhoa" class="form-label">Specialization</label>
                                <input type="text" class="form-control" id="ChuyenKhoa" name="ChuyenKhoa"
                                    placeholder="Enter specialization (optional for doctors)">
                                <div class="form-text">Optional field, mainly for doctors.</div>
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
                                            <i class="fas fa-save"></i> Update Staff
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
                <h5 class="modal-title"><i class="fas fa-eye"></i> Preview Staff Updates</h5>
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
        const searchBtn = document.getElementById('searchStaffBtn');
        const staffIdSearch = document.getElementById('staffIdSearch');
        const searchResults = document.getElementById('searchResults');
        const updateFormSection = document.getElementById('updateFormSection');
        const updateForm = document.getElementById('updateStaffForm');
        const cancelBtn = document.getElementById('cancelUpdateBtn');
        const previewBtn = document.getElementById('previewUpdateBtn');
        const submitBtn = document.getElementById('submitUpdateBtn');

        let currentStaffData = null;

        // Search for staff
        searchBtn.addEventListener('click', searchStaff);
        staffIdSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStaff();
            }
        });

        function searchStaff() {
            const staffId = staffIdSearch.value.trim();

            if (!staffId) {
                showMessage('warning', 'Please enter a staff ID.', searchResults);
                return;
            }

            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

            fetch(`<?php echo url('admin/findStaff'); ?>/${staffId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.staff) {
                        data.staff = data.staff[0]
                        console.log("hehe");
                        console.log(data.staff)
                        currentStaffData = data.staff;
                        displayStaffFound(data.staff);
                        populateUpdateForm(data.staff);
                    } else {
                        showMessage('danger', `Staff with ID ${staffId} not found.`, searchResults);
                        hideUpdateForm();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Error searching for staff. Please try again.', searchResults);
                    hideUpdateForm();
                })
                .finally(() => {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = '<i class="fas fa-search"></i> Search Staff';
                });
        }

        function displayStaffFound(staff) {
            // console.log(staff[0]);
            const staffCard = `
            <div class="alert alert-success mt-3">
                <h6 class="alert-heading"><i class="fas fa-check-circle"></i> Staff Found!</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong> ${staff.HoTen}<br>
                        <strong>Phone:</strong> ${staff.SoDienThoai}<br>
                        <strong>ID Number:</strong> ${staff.SoDinhDanh}
                    </div>
                    <div class="col-md-6">
                        <strong>Staff Type:</strong> ${staff.LoaiNhanVien}<br>
                        <strong>Department ID:</strong> ${staff.IDPhongBan}<br>
                        <strong>Specialization:</strong> ${staff.ChuyenKhoa || 'Not specified'}
                    </div>
                </div>
                <hr>
                <p class="mb-0">You can now update this staff member's information below.</p>
            </div>
        `;

            searchResults.innerHTML = staffCard;
            updateFormSection.style.display = 'block';
            updateFormSection.scrollIntoView({
                behavior: 'smooth'
            });
        }

        function populateUpdateForm(staff) {
            document.getElementById('staffId').value = staff.MaNhanVien;
            document.getElementById('HoTen').value = staff.HoTen || '';
            document.getElementById('NgaySinh').value = staff.NgaySinh || '';
            document.getElementById('SoDienThoai').value = staff.SoDienThoai || '';
            document.getElementById('SoDinhDanh').value = staff.SoDinhDanh || '';
            document.getElementById('DiaChi').value = staff.DiaChi || '';
            document.getElementById('IDPhongBan').value = staff.IDPhongBan || '';
            document.getElementById('LoaiNhanVien').value = staff.LoaiNhanVien || '';
            document.getElementById('ChuyenKhoa').value = staff.ChuyenKhoa || '';
        }

        function hideUpdateForm() {
            updateFormSection.style.display = 'none';
            currentStaffData = null;
        }

        // Cancel update
        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
                hideUpdateForm();
                searchResults.innerHTML = '';
                staffIdSearch.value = '';
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

            showPreview(currentStaffData, newData);
        });

        function showPreview(oldData, newData) {
            const previewContent = `
            <div class="row">
                <div class="col-12">
                    <h6 class="text-primary">Staff Update Preview</h6>
                    <p class="text-muted">Review the changes before confirming the update.</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success">Current Information</h6>
                    <table class="table table-sm table-striped">
                        <tr><td><strong>Full Name:</strong></td><td>${oldData.HoTen}</td></tr>
                        <tr><td><strong>Date of Birth:</strong></td><td>${oldData.NgaySinh}</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>${oldData.SoDienThoai}</td></tr>
                        <tr><td><strong>ID Number:</strong></td><td>${oldData.SoDinhDanh}</td></tr>
                        <tr><td><strong>Address:</strong></td><td>${oldData.DiaChi || 'Not provided'}</td></tr>
                        <tr><td><strong>Department ID:</strong></td><td>${oldData.IDPhongBan}</td></tr>
                        <tr><td><strong>Staff Type:</strong></td><td>${oldData.LoaiNhanVien}</td></tr>
                        <tr><td><strong>Specialization:</strong></td><td>${oldData.ChuyenKhoa || 'Not specified'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-warning">New Information</h6>
                    <table class="table table-sm table-striped">
                        <tr><td><strong>Full Name:</strong></td><td>${newData.HoTen}</td></tr>
                        <tr><td><strong>Date of Birth:</strong></td><td>${newData.NgaySinh}</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>${newData.SoDienThoai}</td></tr>
                        <tr><td><strong>ID Number:</strong></td><td>${newData.SoDinhDanh}</td></tr>
                        <tr><td><strong>Address:</strong></td><td>${newData.DiaChi || 'Not provided'}</td></tr>
                        <tr><td><strong>Department ID:</strong></td><td>${newData.IDPhongBan}</td></tr>
                        <tr><td><strong>Staff Type:</strong></td><td>${newData.LoaiNhanVien}</td></tr>
                        <tr><td><strong>Specialization:</strong></td><td>${newData.ChuyenKhoa || 'Not specified'}</td></tr>
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
            const staffId = formData.get('staffId');

            // Remove staffId from form data as it shouldn't be in the update payload
            formData.delete('staffId');
            const updateData = Object.fromEntries(formData.entries());

            fetch(`<?php echo url('admin/updateStaff'); ?>/${staffId}`, {
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
                    <strong>Staff updated successfully!</strong><br>
                    <strong>Staff ID:</strong> ${staffId}<br>
                    <strong>Name:</strong> ${updateData.HoTen}
                `, document.getElementById('messageContainer'));

                        // Scroll to success message
                        document.getElementById('messageContainer').scrollIntoView({
                            behavior: 'smooth'
                        });

                        // Reset form after delay
                        setTimeout(() => {
                            hideUpdateForm();
                            searchResults.innerHTML = '';
                            staffIdSearch.value = '';
                            updateForm.reset();
                            updateForm.classList.remove('was-validated');
                            document.getElementById('messageContainer').innerHTML = '';
                        }, 3000);
                    } else {
                        showMessage('danger', 'Error: ' + (data.message || 'Failed to update staff member'), document.getElementById('messageContainer'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('danger', 'Network error occurred. Please try again.', document.getElementById('messageContainer'));
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Staff';
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
$title = 'Update Staff - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>