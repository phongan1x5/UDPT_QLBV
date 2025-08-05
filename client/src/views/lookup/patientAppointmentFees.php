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
                    <h1><i class="fas fa-search"></i> Patient Lookup - Fees Collection</h1>
                    <p class="text-muted mb-0">Search for patient by ID or phone number for appointment fee collection</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Patient Lookup</li>
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

    <!-- Error/Success Messages -->
    <?php if (isset($_SESSION['lookup_error'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['lookup_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['lookup_error']); ?>
    <?php endif; ?>

    <!-- Main Search Card -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-search"></i> Find Patient
                    </h4>
                    <small>Enter Patient ID or Phone Number</small>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo url('lookup/patientAppointmentFees'); ?>" id="lookupForm">
                        <div class="mb-4">
                            <label for="input" class="form-label fs-5">
                                <i class="fas fa-search text-primary"></i> Search by:
                            </label>
                            <input type="text"
                                class="form-control form-control-lg"
                                id="input"
                                name="input"
                                placeholder="Enter Patient ID (e.g., 123) or Phone Number (e.g., 0123456789)"
                                required
                                autocomplete="off">
                            <div class="form-text">
                                <i class="fas fa-info-circle text-info"></i>
                                You can search using either:
                                <ul class="mt-2 mb-0">
                                    <li><strong>Patient ID:</strong> Numbers only (e.g., 1, 23, 456)</li>
                                    <li><strong>Phone Number:</strong> 10-11 digits (e.g., 0123456789)</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="searchBtn">
                                <i class="fas fa-search"></i> Search Patient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-lightbulb text-warning"></i> Search Tips</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-id-badge"></i> Patient ID Search</h6>
                            <ul class="mb-0">
                                <li>Enter numbers only (e.g., <code>123</code>, <code>45</code>)</li>
                                <li>Do not include "BN" prefix</li>
                                <li>Case sensitive: exact match required</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-phone"></i> Phone Number Search</h6>
                            <ul class="mb-0">
                                <li>Enter 10-11 digit phone number</li>
                                <li>Include area code (e.g., <code>0123456789</code>)</li>
                                <li>No spaces or special characters</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Searches (if you want to implement this feature) -->
    <div class="row mt-4" id="recentSearches" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Recent Searches</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2" id="recentSearchesList">
                        <!-- Recent searches will be populated here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('lookupForm');
        const input = document.getElementById('input');
        const searchBtn = document.getElementById('searchBtn');

        // Input formatting and validation
        input.addEventListener('input', function() {
            let value = this.value.trim();

            // Remove any non-numeric characters for basic cleanup
            // But keep the original value to allow both ID and phone searches

            if (value.length > 0) {
                // Determine if it looks like a phone number or ID
                if (value.length >= 10 && /^\d+$/.test(value)) {
                    this.placeholder = "Phone number detected...";
                    this.classList.add('border-success');
                    this.classList.remove('border-warning');
                } else if (/^\d+$/.test(value) && value.length < 10) {
                    this.placeholder = "Patient ID detected...";
                    this.classList.add('border-warning');
                    this.classList.remove('border-success');
                } else {
                    this.placeholder = "Enter Patient ID or Phone Number";
                    this.classList.remove('border-success', 'border-warning');
                }
            } else {
                this.placeholder = "Enter Patient ID (e.g., 123) or Phone Number (e.g., 0123456789)";
                this.classList.remove('border-success', 'border-warning');
            }
        });

        // Form submission with loading state
        form.addEventListener('submit', function(e) {
            const value = input.value.trim();

            if (!value) {
                e.preventDefault();
                showAlert('Please enter a Patient ID or Phone Number', 'warning');
                return;
            }

            // Basic validation
            if (!/^\d+$/.test(value)) {
                e.preventDefault();
                showAlert('Please enter only numbers (Patient ID or Phone Number)', 'danger');
                return;
            }

            if (value.length < 1 || value.length > 12) {
                e.preventDefault();
                showAlert('Please enter a valid Patient ID (1-5 digits) or Phone Number (10-11 digits)', 'danger');
                return;
            }

            // Show loading state
            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

            // Save to recent searches
            saveRecentSearch(value);
        });

        // Auto-focus on input
        input.focus();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === '/') {
                e.preventDefault();
                input.focus();
                input.select();
            }
        });

        // Recent searches functionality
        function saveRecentSearch(value) {
            let recent = JSON.parse(localStorage.getItem('recentPatientSearches') || '[]');
            recent = recent.filter(item => item !== value); // Remove if exists
            recent.unshift(value); // Add to beginning
            recent = recent.slice(0, 5); // Keep only 5 recent
            localStorage.setItem('recentPatientSearches', JSON.stringify(recent));
            displayRecentSearches();
        }

        function displayRecentSearches() {
            const recent = JSON.parse(localStorage.getItem('recentPatientSearches') || '[]');
            const container = document.getElementById('recentSearchesList');
            const section = document.getElementById('recentSearches');

            if (recent.length > 0) {
                container.innerHTML = recent.map(search =>
                    `<button class="btn btn-outline-secondary btn-sm" onclick="searchRecent('${search}')">
                    <i class="fas fa-search"></i> ${search}
                </button>`
                ).join('');
                section.style.display = 'block';
            }
        }

        // Global function for recent search buttons
        window.searchRecent = function(value) {
            input.value = value;
            input.focus();
        };

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
            <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'exclamation-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

            // Insert after header
            const header = document.querySelector('.row.mb-4');
            header.insertAdjacentElement('afterend', alertDiv);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Load recent searches on page load
        displayRecentSearches();
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card.shadow-lg:hover {
        transform: translateY(-2px);
    }

    .form-control-lg {
        font-size: 1.1rem;
        padding: 0.75rem 1rem;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .border-success {
        border-color: #198754 !important;
    }

    .border-warning {
        border-color: #ffc107 !important;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .card-body {
            padding: 1.5rem !important;
        }
    }

    /* Focus enhancement */
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Loading animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Patient Lookup - Fee Collection';
include __DIR__ . '/../layouts/main.php';
?>