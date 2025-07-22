<?php
ob_start();

// Debug section (remove this once it's working)
// echo '<pre>';
// print_r($labServices);
// echo '</pre>';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-microscope"></i> My Lab Services</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Lab Services</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if (!empty($labServices)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-flask"></i> Lab Service History
                        </h5>
                        <span class="badge bg-info">
                            <?php echo count($labServices); ?> Total Services
                        </span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($labServices as $labService):
                            // Now $labService is directly the lab service object
                            $serviceInfo = $labService['Service'] ?? null;
                        ?>

                            <div class="card mb-3 border-left-<?php echo !empty($labService['KetQua']) ? 'success' : 'warning'; ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-vial"></i>
                                                    <?php echo htmlspecialchars($serviceInfo['TenDichVu'] ?? 'Lab Service #' . $labService['MaDVSD']); ?>
                                                </h6>
                                                <span class="badge bg-<?php echo !empty($labService['KetQua']) ? 'success' : 'warning'; ?>">
                                                    <?php echo !empty($labService['KetQua']) ? 'Completed' : 'Pending'; ?>
                                                </span>
                                            </div>

                                            <?php if ($serviceInfo): ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle"></i>
                                                        <strong>Service:</strong> <?php echo htmlspecialchars($serviceInfo['NoiDungDichVu']); ?>
                                                        <br>
                                                        <i class="fas fa-dollar-sign"></i>
                                                        <strong>Cost:</strong> $<?php echo number_format($serviceInfo['DonGia'], 2); ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-id-card"></i>
                                                    <strong>Medical Record ID:</strong> <?php echo htmlspecialchars($labService['MaGiayKhamBenh']); ?>
                                                    <br>
                                                    <i class="fas fa-calendar"></i>
                                                    <strong>Service Date:</strong>
                                                    <?php
                                                    $serviceDate = new DateTime($labService['ThoiGian']);
                                                    echo $serviceDate->format('F j, Y - g:i A');
                                                    ?>
                                                </small>
                                            </div>

                                            <div class="service-details">
                                                <?php if (!empty($labService['KetQua'])): ?>
                                                    <h6 class="text-success mb-2">
                                                        <i class="fas fa-check-circle"></i> Result
                                                    </h6>
                                                    <div class="bg-light p-3 rounded">
                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($labService['KetQua'])); ?></p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                                        <p class="mb-0 text-warning">
                                                            <i class="fas fa-hourglass-half"></i>
                                                            Result pending - Please check back later
                                                        </p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4 text-end">
                                            <div class="mb-2">
                                                <small class="text-muted">Service ID: #<?php echo $labService['MaDVSD']; ?></small>
                                            </div>
                                            <div class="d-grid gap-1">
                                                <a href="/lab/view/<?php echo $labService['MaDVSD']; ?>"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>

                                                <!-- <?php if (!empty($labService['FileKetQua'])): ?>
                                                    <a href="/lab/download/<?php echo $labService['MaDVSD']; ?>"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-download"></i> Download Result
                                                    </a>
                                                <?php endif; ?>

                                                <button class="btn btn-outline-info btn-sm" onclick="printLabResult(<?php echo $labService['MaDVSD']; ?>)">
                                                    <i class="fas fa-print"></i> Print
                                                </button> -->

                                                <?php if (empty($labService['KetQua'])): ?>
                                                    <button class="btn btn-outline-warning btn-sm" onclick="checkStatus(<?php echo $labService['MaDVSD']; ?>)">
                                                        <i class="fas fa-refresh"></i> Check Status
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning">
                            <?php
                            $pendingCount = 0;
                            foreach ($labServices as $ls) {
                                if (empty($ls['KetQua'])) {
                                    $pendingCount++;
                                }
                            }
                            echo $pendingCount;
                            ?>
                        </h4>
                        <small class="text-muted">Pending Results</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success">
                            <?php
                            $completedCount = 0;
                            foreach ($labServices as $ls) {
                                if (!empty($ls['KetQua'])) {
                                    $completedCount++;
                                }
                            }
                            echo $completedCount;
                            ?>
                        </h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info">
                            <?php
                            $withFileCount = 0;
                            foreach ($labServices as $ls) {
                                if (!empty($ls['FileKetQua'])) {
                                    $withFileCount++;
                                }
                            }
                            echo $withFileCount;
                            ?>
                        </h4>
                        <small class="text-muted">With Files</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary">
                            $<?php
                                $totalCost = 0;
                                foreach ($labServices as $ls) {
                                    if (isset($ls['Service']) && isset($ls['Service']['DonGia'])) {
                                        $totalCost += $ls['Service']['DonGia'];
                                    }
                                }
                                echo number_format($totalCost, 2);
                                ?>
                        </h4>
                        <small class="text-muted">Total Cost</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Service Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Group services by type
                            $serviceTypes = [];
                            foreach ($labServices as $ls) {
                                if (isset($ls['Service']['TenDichVu'])) {
                                    $serviceName = $ls['Service']['TenDichVu'];
                                    if (!isset($serviceTypes[$serviceName])) {
                                        $serviceTypes[$serviceName] = 0;
                                    }
                                    $serviceTypes[$serviceName]++;
                                }
                            }

                            foreach ($serviceTypes as $serviceName => $count):
                            ?>
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($serviceName); ?></span>
                                        <span class="badge bg-secondary"><?php echo $count; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-microscope fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Lab Services Found</h4>
                        <p class="text-muted">You don't have any lab services yet. They will appear here after your doctor orders lab tests.</p>
                        <a href="/appointments" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }

    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }

    .bg-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>

<script>
    function printLabResult(serviceId) {
        window.open('/lab/print/' + serviceId, '_blank');
    }

    function checkStatus(serviceId) {
        // Refresh the page or make an AJAX call to check status
        location.reload();
    }

    // Auto-refresh pending results every 30 seconds
    <?php if (!empty($labServices)): ?>
        let hasPendingResults = <?php echo json_encode(array_filter($labServices, function ($ls) {
                                    return empty($ls['KetQua']);
                                }) ? true : false); ?>;
        if (hasPendingResults) {
            setTimeout(function() {
                location.reload();
            }, 30000); // 30 seconds
        }
    <?php endif; ?>
</script>

<?php
$content = ob_get_clean();
$title = 'My Lab Services - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>