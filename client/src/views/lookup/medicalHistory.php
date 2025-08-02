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
                    <h1><i class="fas fa-search"></i> Medical History Lookup</h1>
                    <p class="text-muted mb-0">Search for a Medical History by entering the record ID</p>
                </div>
                <div>
                    <a href="<?php echo url('dashboard'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0"><i class="fas fa-file-medical"></i> Find Medical History</h5>
                </div>
                <div class="card-body">
                    <form id="lookupForm" method="POST" action="<?php echo url('lookup/patientMedicalHistory'); ?>">
                        <div class="mb-3">
                            <label for="medicalHistoryId" class="form-label">
                                <strong>Medical History ID *</strong>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-hashtag"></i>
                                </span>
                                <input type="number"
                                    class="form-control"
                                    id="medicalHistoryId"
                                    name="id"
                                    placeholder="Enter medical history ID (e.g., 1, 2, 3...)"
                                    min="1"
                                    required
                                    autofocus>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                Enter the numerical ID of the medical history you want to view
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="searchBtn">
                                <i class="fas fa-search"></i> Search Medical History
                            </button>
                        </div>
                    </form>

                    <!-- Quick Tips -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-lightbulb"></i> Quick Tips:
                        </h6>
                        <ul class="mb-0 small">
                            <li>Medical History IDs are numerical (e.g., 1, 2, 3)</li>
                            <li>You can find the ID from appointment confirmations</li>
                            <li>Contact reception if you don't know your record ID</li>
                            <li>Each visit creates a new Medical History</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="row justify-content-center" style="display: none;">
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="text-primary">Searching Medical History...</h5>
                    <p class="text-muted">Please wait while we retrieve the information</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <?php if (isset($_SESSION['lookup_error'])): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['lookup_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php unset($_SESSION['lookup_error']);
    endif; ?>

    <!-- Success Message -->
    <?php if (isset($_SESSION['lookup_success'])): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <strong>Success:</strong> <?php echo htmlspecialchars($_SESSION['lookup_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php unset($_SESSION['lookup_success']);
    endif; ?>

    <!-- Recent Searches (if any) -->
    <?php if (isset($_SESSION['recent_lookups']) && !empty($_SESSION['recent_lookups'])): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-history"></i> Recent Searches</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach (array_slice($_SESSION['recent_lookups'], -6) as $recentId): ?>
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <a href="<?php echo url('lookup/medical-record?record_id=' . $recentId); ?>"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-file-medical-alt"></i> Record #<?php echo $recentId; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Help Section -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-question-circle"></i> Need Help?</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-info">Can't find your Medical History ID?</h6>
                            <ul class="small">
                                <li>Check your appointment confirmation email</li>
                                <li>Look at your receipt from the last visit</li>
                                <li>Contact our reception desk</li>
                                <li>Bring your ID for assistance</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">Contact Information</h6>
                            <ul class="small">
                                <li><i class="fas fa-phone"></i> Reception: (555) 123-4567</li>
                                <li><i class="fas fa-envelope"></i> records@hospital.com</li>
                                <li><i class="fas fa-clock"></i> Hours: 8AM - 6PM Mon-Fri</li>
                                <li><i class="fas fa-map-marker-alt"></i> Information Desk - Ground Floor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('lookupForm');
        const searchBtn = document.getElementById('searchBtn');
        const loadingState = document.getElementById('loadingState');

        form.addEventListener('submit', function(e) {
            // Show loading state
            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            loadingState.style.display = 'block';

            // Scroll to loading state
            loadingState.scrollIntoView({
                behavior: 'smooth'
            });
        });

        // Auto-focus on page load
        document.getElementById('medicalRecordId').focus();

        // Enter key handling
        document.getElementById('medicalRecordId').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                form.submit();
            }
        });

        // Input validation
        document.getElementById('medicalRecordId').addEventListener('input', function() {
            const value = this.value;
            if (value && value < 1) {
                this.setCustomValidity('Medical History ID must be a positive number');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>

<style>
    .input-group-lg .form-control {
        font-size: 1.2rem;
        font-weight: 500;
    }

    .input-group-lg .input-group-text {
        font-size: 1.2rem;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: box-shadow 0.15s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Medical History Lookup - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>