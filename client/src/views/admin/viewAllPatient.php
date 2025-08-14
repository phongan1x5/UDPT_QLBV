<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Calculate basic statistics
$totalPatients = is_array($patients) ? count($patients) : 0;
$activePatients = 0;
$inactivePatients = 0;
$malePatients = 0;
$femalePatients = 0;

if (is_array($patients) && !empty($patients)) {
    foreach ($patients as $patient) {
        // Count active/inactive patients
        if (!empty($patient['is_active']) && $patient['is_active'] == 1) {
            $activePatients++;
        } else {
            $inactivePatients++;
        }

        // Count by gender
        if (isset($patient['GioiTinh'])) {
            if (strtolower($patient['GioiTinh']) === 'nam' || strtolower($patient['GioiTinh']) === 'male') {
                $malePatients++;
            } else {
                $femalePatients++;
            }
        }
    }
}
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-users"></i> Patient Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Patients</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $totalPatients; ?></h4>
                            <p class="mb-0">Total Patients</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $activePatients; ?></h4>
                            <p class="mb-0">Active Patients</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $malePatients; ?></h4>
                            <p class="mb-0">Male Patients</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-mars fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $femalePatients; ?></h4>
                            <p class="mb-0">Female Patients</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-venus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Patients List</h5>
                    <small class="text-muted"><?php echo $totalPatients; ?> patient(s) found</small>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($patients)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Patients Found</h5>
                            <p class="text-muted">No patients are currently registered in the system.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> ID</th>
                                        <th><i class="fas fa-user"></i> Patient Info</th>
                                        <th><i class="fas fa-birthday-cake"></i> Birth Date & Age</th>
                                        <th><i class="fas fa-venus-mars"></i> Gender</th>
                                        <th><i class="fas fa-phone"></i> Contact</th>
                                        <th><i class="fas fa-id-card"></i> ID Number</th>
                                        <th><i class="fas fa-shield-alt"></i> Insurance</th>
                                        <th><i class="fas fa-toggle-on"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patients as $patient): ?>
                                        <?php
                                        // Calculate age
                                        $age = 'N/A';
                                        if (!empty($patient['NgaySinh'])) {
                                            try {
                                                $birthDate = new DateTime($patient['NgaySinh']);
                                                $today = new DateTime();
                                                $age = $today->diff($birthDate)->y;
                                            } catch (Exception $e) {
                                                $age = 'N/A';
                                            }
                                        }

                                        // Determine status
                                        $isActive = !empty($patient['is_active']) && $patient['is_active'] == 1;
                                        $statusClass = $isActive ? 'success' : 'secondary';
                                        $statusIcon = $isActive ? 'check-circle' : 'minus-circle';
                                        $statusText = $isActive ? 'Active' : 'Inactive';

                                        // Gender icon and color
                                        $genderIcon = 'user';
                                        $genderColor = 'secondary';
                                        if (isset($patient['GioiTinh'])) {
                                            if (strtolower($patient['GioiTinh']) === 'nam' || strtolower($patient['GioiTinh']) === 'male') {
                                                $genderIcon = 'mars';
                                                $genderColor = 'info';
                                            } else {
                                                $genderIcon = 'venus';
                                                $genderColor = 'warning';
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?php echo htmlspecialchars($patient['id'] ?? 'N/A'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                        style="width: 35px; height: 35px; font-size: 14px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            <?php echo htmlspecialchars($patient['HoTen'] ?? 'N/A'); ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($patient['Email'] ?? 'No email'); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo !empty($patient['NgaySinh']) ? date('d/m/Y', strtotime($patient['NgaySinh'])) : 'N/A'; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo $age; ?> years old
                                                    </small>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="badge bg-<?php echo $genderColor; ?>">
                                                    <i class="fas fa-<?php echo $genderIcon; ?>"></i>
                                                    <?php echo htmlspecialchars($patient['GioiTinh'] ?? 'Unknown'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div>
                                                    <div>
                                                        <i class="fas fa-phone text-success me-1"></i>
                                                        <?php echo htmlspecialchars($patient['SoDienThoai'] ?? 'N/A'); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt text-info me-1"></i>
                                                        <?php echo htmlspecialchars($patient['DiaChi'] ?? 'No address'); ?>
                                                    </small>
                                                </div>
                                            </td>

                                            <td>
                                                <code class="bg-light p-1 rounded">
                                                    <?php echo htmlspecialchars($patient['SoDinhDanh'] ?? 'N/A'); ?>
                                                </code>
                                            </td>

                                            <td>
                                                <?php if (!empty($patient['BaoHiemYTe'])): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-shield-alt"></i>
                                                        <?php echo htmlspecialchars($patient['BaoHiemYTe']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-shield-times"></i>
                                                        No Insurance
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <i class="fas fa-<?php echo $statusIcon; ?>"></i>
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Information (Remove in production) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 text-muted"><i class="fas fa-bug"></i> Debug Information</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded" style="font-size: 12px; max-height: 300px; overflow-y: auto;">
<?php print_r($patients); ?>
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .table th {
        font-weight: 600;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }

    .badge {
        font-size: 0.75em;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .d-flex .fw-bold {
            font-size: 0.9rem;
        }

        .d-flex small {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {

        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }

        .badge {
            font-size: 0.65em;
            padding: 0.25em 0.5em;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Patient Management - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>