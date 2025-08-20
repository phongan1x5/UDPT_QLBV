<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// Debug - you can remove this later
// echo '<pre>';
// print_r($user);
// echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-desktop"></i> Desk Staff Dashboard</h1>
                    <p class="text-muted mb-0">Patient Registration & Appointment Management</p>
                </div>
                <div class="text-end">
                    <div class="text-muted">
                        Welcome back, <strong>Desk Staff</strong>
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-user-tie"></i> <?php echo $user['user_id']; ?>
                        </span>
                    </div>
                    <small class="text-muted">Role: <?php echo ucfirst(str_replace('_', ' ', $user['user_role'])); ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Cards - Main Functions -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card bg-primary text-white h-100 shadow-lg">
                <div class="card-body text-center d-flex flex-column justify-content-center" style="min-height: 200px;">
                    <div class="mb-3">
                        <i class="fas fa-user-plus fa-4x mb-3"></i>
                        <h3 class="card-title">Patient</h3>
                        <p class="card-text">Patient related actions</p>
                    </div>
                    <a href="<?php echo url('deskStaff/createPatient'); ?>" class="btn btn-light btn-lg" style="display:block; margin-bottom:20px;">
                        <i class="fas fa-plus-circle"></i> Create New Patient
                    </a>

                    <a href="<?php echo url('lookup/patientMedicalHistory'); ?>" class="btn btn-light btn-lg" style="display:block; margin-bottom:20px;">
                        <i class="fas fa-plus-circle"></i> Patient Lookup
                    </a>

                    <a href="<?php echo url('lookup/patientAppointmentFees'); ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-cash-register"></i> Collect Fees
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-success text-white h-100 shadow-lg">
                <div class="card-body text-center d-flex flex-column justify-content-center" style="min-height: 200px;">
                    <div class="mb-3">
                        <i class="fas fa-user-md fa-4x mb-3"></i>
                        <h3 class="card-title">Doctor</h3>
                        <p class="card-text">Doctor related actions</p>
                    </div>
                    <a href="<?php echo url('lookup/doctorAppointments'); ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-cash-register"></i> Appointments
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }

    .bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }

    .bg-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .fa-4x {
            font-size: 2.5rem !important;
        }
    }

    /* Pulse animation for main action buttons */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .card.bg-primary:hover,
    .card.bg-success:hover {
        animation: pulse 2s infinite;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click tracking for analytics
        document.querySelectorAll('a[href*="create-patient"], a[href*="collect-appointment-fees"]').forEach(button => {
            button.addEventListener('click', function(e) {
                console.log('Primary action clicked:', this.href);
                // You can add analytics tracking here
            });
        });

        // Auto-refresh stats every 5 minutes
        setInterval(() => {
            // You can add AJAX calls to update stats here
            console.log('Refreshing dashboard stats...');
        }, 5 * 60 * 1000);
    });
</script>

<?php
$content = ob_get_clean();
$title = 'Desk Staff Dashboard - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>