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
                    <h1><i class="fas fa-building"></i> Create New Department</h1>
                    <p class="text-muted mb-0">Add a new department to the hospital system</p>
                </div>
                <div>
                    <a href="<?php echo url('admin/departments'); ?>" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View All Departments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Create Department Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Department Information
                    </h5>
                </div>
                <div class="card-body">
                    <form id="createDepartmentForm" method="POST" action="<?php echo url('admin/createDepartment'); ?>" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="departmentName" class="form-label">
                                        <i class="fas fa-building"></i> Department Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-building text-primary"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            id="departmentName"
                                            name="department_name"
                                            placeholder="Enter department name (e.g., Cardiology, Emergency)"
                                            required
                                            maxlength="100"
                                            autocomplete="off">
                                        <div class="invalid-feedback">
                                            Please provide a valid department name.
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i>
                                        Department name must be unique and descriptive (max 100 characters)
                                    </div>
                                    <div id="nameValidationMessage" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="departmentDescription" class="form-label">
                                        <i class="fas fa-align-left"></i> Department Description
                                    </label>
                                    <textarea class="form-control"
                                        id="departmentDescription"
                                        name="department_description"
                                        rows="4"
                                        placeholder="Optional: Describe the department's role and responsibilities"
                                        maxlength="500"></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i>
                                        Optional description to help identify the department's purpose (max 500 characters)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="reset" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                        <i class="fas fa-undo"></i> Reset Form
                                    </button>
                                    <button type="submit" class="btn btn-success" id="submitBtn">
                                        <i class="fas fa-plus"></i> Create Department
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Departments List -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Existing Departments
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($currentAllDepartments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($currentAllDepartments as $dept): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <i class="fas fa-building text-primary me-2"></i>
                                        <strong><?php echo htmlspecialchars($dept['TenPhongBan']); ?></strong>
                                        <small class="text-muted d-block">
                                            ID: <?php echo $dept['IDPhongBan']; ?>
                                            <?php if (!empty($dept['IDTruongPhongBan'])): ?>
                                                | Head: <?php echo $dept['IDTruongPhongBan']; ?>
                                            <?php else: ?>
                                                | No Head Assigned
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Total: <?php echo count($currentAllDepartments); ?> departments
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Departments Yet</h6>
                            <p class="text-muted small mb-0">
                                This will be the first department in the system.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-secondary">
                        <i class="fas fa-lightbulb"></i> Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Use clear, descriptive department names</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Department names must be unique</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Assign department heads later</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Review existing departments first</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Store existing department names for validation
    const existingDepartments = <?php echo json_encode(array_column($currentAllDepartments, 'TenPhongBan')); ?>;

    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (form.checkValidity() === true && validateDepartmentName()) {
                        submitForm();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Real-time department name validation
    document.getElementById('departmentName').addEventListener('input', function() {
        validateDepartmentName();
    });

    function validateDepartmentName() {
        const input = document.getElementById('departmentName');
        const messageDiv = document.getElementById('nameValidationMessage');
        const departmentName = input.value.trim();

        // Clear previous messages
        messageDiv.innerHTML = '';
        input.classList.remove('is-invalid', 'is-valid');

        if (departmentName === '') {
            return false;
        }

        // Check if department name already exists (case-insensitive)
        const nameExists = existingDepartments.some(dept =>
            dept.toLowerCase() === departmentName.toLowerCase()
        );

        if (nameExists) {
            input.classList.add('is-invalid');
            messageDiv.innerHTML = `
            <div class="alert alert-danger py-2">
                <i class="fas fa-exclamation-triangle"></i>
                Department name "<strong>${departmentName}</strong>" already exists. Please choose a different name.
            </div>
        `;
            return false;
        } else if (departmentName.length >= 3) {
            input.classList.add('is-valid');
            messageDiv.innerHTML = `
            <div class="alert alert-success py-2">
                <i class="fas fa-check-circle"></i>
                Department name is available!
            </div>
        `;
            return true;
        }

        return false;
    }

    function submitForm() {
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Department...';

        // Submit the form
        document.getElementById('createDepartmentForm').submit();
    }

    function resetForm() {
        document.getElementById('createDepartmentForm').reset();
        document.getElementById('createDepartmentForm').classList.remove('was-validated');
        document.getElementById('nameValidationMessage').innerHTML = '';
        document.getElementById('departmentName').classList.remove('is-invalid', 'is-valid');
    }

    // Auto-focus on department name field
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('departmentName').focus();
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #dee2e6;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .input-group .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group:focus-within .input-group-text {
        background-color: #fff;
        border-color: #86b7fe;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 10px;
        }

        .card {
            margin-bottom: 1rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Create New Department - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>