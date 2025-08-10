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
                    <h1><i class="fas fa-search"></i> Lookup Doctor Appointments</h1>
                    <p class="text-muted mb-0">Search for doctor appointments by Doctor ID</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('desk-staff/dashboard'); ?>">Desk Staff</a></li>
                            <li class="breadcrumb-item active">Doctor Appointments Lookup</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo url('desk-staff/dashboard'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <?php if (isset($_SESSION['lookup_error'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $_SESSION['lookup_error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['lookup_error']); ?>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-md"></i> Find Doctor Appointments
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo url('lookup/doctorAppointments'); ?>" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="doctor_id" class="form-label">
                                <i class="fas fa-id-card"></i> Doctor ID
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-user-md text-primary"></i>
                                </span>
                                <input type="number" 
                                       class="form-control" 
                                       id="doctor_id" 
                                       name="doctor_id" 
                                       placeholder="Enter Doctor ID (e.g., 2)" 
                                       required
                                       min="1"
                                       autofocus>
                                <div class="invalid-feedback">
                                    Please enter a valid Doctor ID.
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                Enter the numeric Doctor ID to search for their appointments
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search"></i> Search Doctor Appointments
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" onclick="clearForm()">
                                <i class="fas fa-eraser"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Card -->
    <div class="row mt-4 justify-content-center">
        <div class="col-md-8">
            <div class="card border-info">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-info">
                        <i class="fas fa-info-circle"></i> How to Use
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-1"></i> Search Process
                            </h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Enter the Doctor ID</li>
                                <li><i class="fas fa-check text-success"></i> Click "Search Doctor Appointments"</li>
                                <li><i class="fas fa-check text-success"></i> View doctor's appointments</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-lightbulb"></i> Tips
                            </h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info text-info"></i> Use numeric Doctor ID only</li>
                                <li><i class="fas fa-info text-info"></i> Make sure the doctor exists</li>
                                <li><i class="fas fa-info text-info"></i> Results will show all appointments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Searches (Optional) -->
    <div class="row mt-4 justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i> Quick Search
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Common Doctor IDs:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="fillDoctorId(1)">
                            <i class="fas fa-user-md"></i> Doctor ID: 1
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="fillDoctorId(2)">
                            <i class="fas fa-user-md"></i> Doctor ID: 2
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="fillDoctorId(3)">
                            <i class="fas fa-user-md"></i> Doctor ID: 3
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Clear form
function clearForm() {
    document.getElementById('doctor_id').value = '';
    document.querySelector('.needs-validation').classList.remove('was-validated');
    document.getElementById('doctor_id').focus();
}

// Fill doctor ID from quick search
function fillDoctorId(doctorId) {
    document.getElementById('doctor_id').value = doctorId;
    document.getElementById('doctor_id').focus();
}

// Auto-focus on input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('doctor_id').focus();
    
    // Allow Enter key to submit
    document.getElementById('doctor_id').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.querySelector('form').submit();
        }
    });
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

.input-group-lg .input-group-text {
    font-size: 1.25rem;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0 15px;
    }
    
    .card {
        margin: 0 -5px;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Lookup Doctor Appointments - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>