<?php require_once __DIR__ . '/../../helper/url_parsing.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Hospital Management System'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo url('public/css/style.css'); ?>" rel="stylesheet">
</head>

<body>

    <?php if (isset($_SESSION['user'])): ?>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?php echo url('dashboard'); ?>">
                    <i class="fas fa-hospital-alt"></i> HMS
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('dashboard'); ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        <?php if ($_SESSION['user']['user_role'] === 'admin' || $_SESSION['user']['user_role'] === 'doctor'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="patientsDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users"></i> Patients
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo url('patients'); ?>">
                                            <i class="fas fa-list"></i> All Patients
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('patients/add'); ?>">
                                            <i class="fas fa-user-plus"></i> Add Patient
                                        </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['user_role'] === 'admin' || $_SESSION['user']['user_role'] === 'doctor' || $_SESSION['user']['user_role'] === 'staff'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="appointmentsDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-calendar-check"></i> Appointments
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo url('appointments'); ?>">
                                            <i class="fas fa-calendar-alt"></i> All Appointments
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('appointments/create'); ?>">
                                            <i class="fas fa-calendar-plus"></i> Schedule Appointment
                                        </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['user_role'] === 'patient'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo url('appointments/book'); ?>">
                                    <i class="fas fa-calendar-plus"></i> Book Appointment
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo url('medical-records'); ?>">
                                    <i class="fas fa-file-medical"></i> Medical Records
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['user_role'] === 'doctor'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="medicalDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-stethoscope"></i> Medical
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo url('prescriptions'); ?>">
                                            <i class="fas fa-prescription-bottle"></i> Prescriptions
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('lab/orders'); ?>">
                                            <i class="fas fa-vial"></i> Lab Orders
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('medical-records'); ?>">
                                            <i class="fas fa-file-medical-alt"></i> Medical Records
                                        </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['user_role'] === 'lab_staff'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo url('lab'); ?>">
                                    <i class="fas fa-flask"></i> Lab Management
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['user_role'] === 'pharmacist'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo url('pharmacy'); ?>">
                                    <i class="fas fa-pills"></i> Pharmacy
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['user_role'] === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Admin
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo url('staff'); ?>">
                                            <i class="fas fa-user-md"></i> Staff Management
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('reports'); ?>">
                                            <i class="fas fa-chart-bar"></i> Reports
                                        </a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('settings'); ?>">
                                            <i class="fas fa-wrench"></i> System Settings
                                        </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['user']['user_id']); ?>
                                <span class="badge bg-light text-dark ms-1">
                                    <?php echo ucfirst($_SESSION['user']['user_role']); ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo url('profile'); ?>">
                                        <i class="fas fa-user"></i> My Profile
                                    </a></li>
                                <li><a class="dropdown-item" href="<?php echo url('notifications'); ?>">
                                        <i class="fas fa-bell"></i> Notifications
                                        <span class="badge bg-danger ms-1">3</span>
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo url('logout'); ?>">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="<?php echo isset($_SESSION['user']) ? 'container-fluid mt-4' : ''; ?>">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-hospital-alt"></i> Hospital Management System</h5>
                    <p class="mb-2">A comprehensive platform for managing hospital operations, patient care, and medical records.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="<?php echo url('dashboard'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a></li>
                            <li><a href="<?php echo url('appointments'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-calendar-check"></i> Appointments
                                </a></li>
                            <li><a href="<?php echo url('patients'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-users"></i> Patients
                                </a></li>
                            <li><a href="<?php echo url('medical-records'); ?>" class="text-light text-decoration-none">
                                    <i class="fas fa-file-medical"></i> Medical Records
                                </a></li>
                        <?php else: ?>
                            <li><a href="login" class="text-light text-decoration-none">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a></li>
                            <li><a href="register" class="text-light text-decoration-none">
                                    <i class="fas fa-user-plus"></i> Register
                                </a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Emergency Contact</h6>
                    <p class="mb-1"><i class="fas fa-phone"></i> Emergency: 911</p>
                    <p class="mb-1"><i class="fas fa-envelope"></i> info@hospital.com</p>
                    <p class="mb-1"><i class="fas fa-map-marker-alt"></i> 123 Medical Center Dr.</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-8">
                    <p class="mb-0">
                        <i class="fas fa-copyright"></i> <?php echo date('Y'); ?> Hospital Management System.
                        All rights reserved. | Patient confidentiality protected.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <small>
                        <i class="fas fa-code"></i> Built with PHP & Microservices
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS for notifications -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }
            });
        }, 5000);
    </script>
</body>

</html>