<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Calculate basic statistics
$totalDepartments = is_array($departments) ? count($departments) : 0;
$departmentsWithManagers = 0;
$departmentsWithoutManagers = 0;

if (is_array($departments) && !empty($departments)) {
    foreach ($departments as $dept) {
        if (!empty($dept['IDTruongPhongBan'])) {
            $departmentsWithManagers++;
        } else {
            $departmentsWithoutManagers++;
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
                    <h1><i class="fas fa-building"></i> Department Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Departments</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $totalDepartments; ?></h4>
                            <p class="mb-0">Total Departments</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $departmentsWithManagers; ?></h4>
                            <p class="mb-0">With Managers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $departmentsWithoutManagers; ?></h4>
                            <p class="mb-0">Without Managers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Departments List</h5>
                    <small class="text-muted"><?php echo $totalDepartments; ?> department(s) found</small>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($departments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Departments Found</h5>
                            <p class="text-muted">No departments are currently configured in the system.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> Department ID</th>
                                        <th><i class="fas fa-building"></i> Department Name</th>
                                        <th><i class="fas fa-user-tie"></i> Manager ID</th>
                                        <th><i class="fas fa-info-circle"></i> Manager Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $index => $department): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?php echo htmlspecialchars($department['IDPhongBan'] ?? 'N/A'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-2"
                                                        style="width: 35px; height: 35px; font-size: 14px;">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            <?php echo htmlspecialchars($department['TenPhongBan'] ?? 'Unknown Department'); ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            Department #<?php echo ($index + 1); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <?php if (!empty($department['IDTruongPhongBan'])): ?>
                                                    <code class="bg-light p-1 rounded">
                                                        <?php echo htmlspecialchars($department['IDTruongPhongBan']); ?>
                                                    </code>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($department['IDTruongPhongBan'])): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-user-check"></i> Has Manager
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-user-times"></i> No Manager
                                                    </span>
                                                <?php endif; ?>
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
<?php print_r($departments); ?>
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
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Department Management - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>