<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// Debug - you can remove this later
echo '<pre>';
print_r($user['token']);
print_r($admin);
echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-shield"></i> Admin Dashboard</h1>
                    <p class="text-muted mb-0">Hospital Management System Control Panel</p>
                </div>
                <div class="text-end">
                    <div class="text-muted">
                        Welcome back, <strong><?php echo htmlspecialchars($admin['HoTen']); ?></strong>
                        <span class="badge bg-danger ms-2">
                            <i class="fas fa-shield-alt"></i> Administrator
                        </span>
                    </div>
                    <small class="text-muted">Staff ID: <?php echo $admin['MaNhanVien']; ?> | User ID: <?php echo $user['user_id']; ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h5>Staff Management</h5>
                    <small>Manage hospital staff</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-user-injured fa-2x mb-2"></i>
                    <h5>Patient Records</h5>
                    <small>Patient management</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-2x mb-2"></i>
                    <h5>Department Setup</h5>
                    <small>Departments & services</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h5>Reports & Analytics</h5>
                    <small>System insights</small>
                </div>
            </div>
        </div>
    </div>

    Main Admin Functions
    <div class="row">
        <!-- Staff Management Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Staff Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/createStaff'); ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-user-plus"></i><br>
                                <small>Create New Staff</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/viewAllStaff'); ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-list"></i><br>
                                <small>View All Staff</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/updateStaff'); ?>" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-file"></i><br>
                                <small>Update Staff</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-user-nurse"></i><br>
                                <small>Manage Nurses</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-info w-100">
                                <i class="fas fa-flask"></i><br>
                                <small>Lab Staff</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-success w-100">
                                <i class="fas fa-pills"></i><br>
                                <small>Pharmacists</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Management Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-injured"></i> Patient Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-user-plus"></i><br>
                                <small>Register Patient</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/viewAllPatient'); ?>" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-search"></i><br>
                                <small>View All Patients</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/updatePatient'); ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-file-medical"></i><br>
                                <small>Update Patient</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-calendar-check"></i><br>
                                <small>Appointments</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-info w-100">
                                <i class="fas fa-bed"></i><br>
                                <small>Room Assignment</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-archive"></i><br>
                                <small>Patient Archive</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department & Services Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Departments & Services</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/createDepartment'); ?>" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-plus-circle"></i><br>
                                <small>Add Department</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo url('admin/viewAllDepartment'); ?>" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-list-alt"></i><br>
                                <small>Manage Departments</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-concierge-bell"></i><br>
                                <small>Medical Services</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-flask"></i><br>
                                <small>Lab Services</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-warning w-100">
                                <i class="fas fa-bed"></i><br>
                                <small>Room Management</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-cogs"></i><br>
                                <small>Equipment</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Management Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> System Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-danger w-100 mb-2">
                                <i class="fas fa-user-shield"></i><br>
                                <small>User Roles</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-key"></i><br>
                                <small>Access Control</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-database"></i><br>
                                <small>Database Backup</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-history"></i><br>
                                <small>System Logs</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-success w-100">
                                <i class="fas fa-bell"></i><br>
                                <small>Notifications</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="fas fa-tools"></i><br>
                                <small>System Settings</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports & Analytics Section -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Reports & Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <a href="<?php echo url('admin/prescriptionReport'); ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-chart-line"></i><br>
                                <small>Prescription Reports</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="<?php echo url('admin/patientReport'); ?>" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-chart-pie"></i><br>
                                <small>Patient Report</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-money-bill"></i><br>
                                <small>Financial Report</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-user-clock"></i><br>
                                <small>Staff Performance</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-hospital"></i><br>
                                <small>Department Stats</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-outline-danger w-100 mb-2">
                                <i class="fas fa-download"></i><br>
                                <small>Export Data</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <a href="#" class="btn btn-danger w-100">
                                <i class="fas fa-exclamation-triangle"></i><br>
                                <small>Emergency Alert</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="<?php echo url('admin/broadcastNotificationForStaff'); ?>" class="btn btn-warning w-100">
                                <i class="fas fa-broadcast-tower"></i><br>
                                <small>System Broadcast</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-info w-100">
                                <i class="fas fa-sync"></i><br>
                                <small>Sync Services</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-success w-100">
                                <i class="fas fa-server"></i><br>
                                <small>System Status</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-primary w-100">
                                <i class="fas fa-question-circle"></i><br>
                                <small>Help & Support</small>
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-secondary w-100">
                                <i class="fas fa-sign-out-alt"></i><br>
                                <small>Logout</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn {
        transition: all 0.3s ease;
        text-align: center;
        padding: 1rem 0.5rem;
        height: auto;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn i {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .btn small {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
        .btn {
            padding: 0.75rem 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Admin Dashboard - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>