<?php
ob_start();
?>

<!-- <?php
        echo '<pre>';
        print_r($_SESSION['user']);
        echo '</pre>';
        ?> -->

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-hospital"></i> Hospital Management Dashboard</h1>
            <?php if (!empty($user)): ?>
                <div class="text-muted">
                    Welcome back, <?php echo htmlspecialchars($user['user_id']); ?>!
                    <span class="badge bg-<?php echo $user['user_role'] === 'admin' ? 'danger' : ($user['user_role'] === 'doctor' ? 'success' : 'primary'); ?>">
                        <?php echo ucfirst($user['user_role']); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="text-muted">
                    Welcome, Guest
                    <a href="/login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($user)): ?>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Today's Appointments -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day"></i> Today's Appointments
                    </h5>
                    <a href="/appointments" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09:00 AM</td>
                                    <td>John Doe (P001)</td>
                                    <td>Dr. Smith</td>
                                    <td>Check-up</td>
                                    <td><span class="badge bg-warning">Scheduled</span></td>
                                </tr>
                                <tr>
                                    <td>10:30 AM</td>
                                    <td>Jane Wilson (P002)</td>
                                    <td>Dr. Johnson</td>
                                    <td>Follow-up</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>02:00 PM</td>
                                    <td>Mike Brown (P003)</td>
                                    <td>Dr. Davis</td>
                                    <td>Consultation</td>
                                    <td><span class="badge bg-primary">In Progress</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Medical Records -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical"></i> Recent Medical Records
                    </h5>
                    <a href="/medical-records" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Annual Physical Examination</h6>
                                <small>2 hours ago</small>
                            </div>
                            <p class="mb-1">Patient: Jane Wilson (P002) - Dr. Johnson</p>
                            <small>Blood pressure normal, cholesterol levels elevated</small>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Emergency Room Visit</h6>
                                <small>5 hours ago</small>
                            </div>
                            <p class="mb-1">Patient: Mike Brown (P003) - Dr. Emergency</p>
                            <small>Minor injury, treated and discharged</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Quick Actions for Patients -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/appointments/book" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </a>
                        <a href="/medical-records" class="btn btn-outline-info">
                            <i class="fas fa-file-medical"></i> View Medical Records
                        </a>
                        <a href="/prescriptions" class="btn btn-outline-success">
                            <i class="fas fa-pills"></i> My Prescriptions
                        </a>
                        <a href="/lab-results" class="btn btn-outline-warning">
                            <i class="fas fa-flask"></i> Lab Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>ID:</strong></div>
                        <div class="col-7"><?php echo htmlspecialchars($user['user_id']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Role:</strong></div>
                        <div class="col-7">
                            <span class="badge bg-<?php echo $user['user_role'] === 'admin' ? 'danger' : ($user['user_role'] === 'doctor' ? 'success' : 'primary'); ?>">
                                <?php echo ucfirst($user['user_role']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="/profile" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 small">Appointment reminder: Patient John Doe at 3:00 PM</p>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-flask text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 small">Lab results ready for Patient P002</p>
                                    <small class="text-muted">3 hours ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Guest Dashboard -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-hospital fa-4x text-muted mb-4"></i>
                    <h3>Welcome to Hospital Management System</h3>
                    <p class="text-muted">Please log in to access your dashboard and manage hospital operations.</p>
                    <a href="/login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$title = 'Dashboard - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>