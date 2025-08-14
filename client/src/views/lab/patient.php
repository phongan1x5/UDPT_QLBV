<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Debug section (remove this once it's working)
echo '<pre>';
print_r($labServices);
echo '</pre>';

// Group lab services by MaGiayKhamBenh
$groupedLabServices = [];
if (!empty($labServices)) {
    foreach ($labServices as $labService) {
        $recordId = $labService['MaGiayKhamBenh'];
        if (!isset($groupedLabServices[$recordId])) {
            $groupedLabServices[$recordId] = [];
        }
        $groupedLabServices[$recordId][] = $labService;
    }
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-microscope"></i> My Lab Services</h1>
            </div>
        </div>
    </div>

    <?php if (!empty($groupedLabServices)): ?>
        <div class="row">
            <div class="col-12">
                <!-- Loop through each medical record -->
                <?php foreach ($groupedLabServices as $recordId => $services): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-medical"></i> Medical Record #<?php echo htmlspecialchars($recordId); ?>
                                </h5>
                                <div>
                                    <span class="badge bg-light text-dark me-2">
                                        <?php echo count($services); ?> Service<?php echo count($services) > 1 ? 's' : ''; ?>
                                    </span>
                                    <span class="badge bg-info">
                                        <?php
                                        $recordDate = !empty($services) ? new DateTime($services[0]['ThoiGian']) : null;
                                        echo $recordDate ? $recordDate->format('M j, Y') : 'N/A';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Services for this medical record -->
                            <?php foreach ($services as $labService):
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
                                                        <i class="fas fa-calendar"></i>
                                                        <strong>Service Date:</strong>
                                                        <?php
                                                        $serviceDate = new DateTime($labService['ThoiGian']);
                                                        echo $serviceDate->format('F j, Y - g:i A');
                                                        ?>
                                                        <br>
                                                        <i class="fas fa-clipboard-check"></i>
                                                        <strong>Status:</strong> <?php echo htmlspecialchars($labService['TrangThai']); ?>
                                                    </small>
                                                </div>

                                                <?php if ($serviceInfo): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle"></i>
                                                            <strong>Service:</strong> <?php echo htmlspecialchars($serviceInfo['NoiDungDichVu']); ?>
                                                            <br>
                                                            <i class="fas fa-dollar-sign"></i>
                                                            <strong>Cost:</strong> $<?php echo number_format($serviceInfo['DonGia'], 2); ?>
                                                            <br>
                                                            <i class="fas fa-receipt"></i>
                                                            <strong>Paid Status:</strong>
                                                            <?php
                                                            $status = strtolower($labService['TrangThai'] ?? 'unknown');
                                                            $statusBadge = [
                                                                'paid' => 'success',
                                                                'unpaid' => 'danger',
                                                                'pending' => 'warning'
                                                            ][$status] ?? 'secondary';
                                                            ?>
                                                            <span class="badge bg-<?= $statusBadge ?>">
                                                                <?= ucfirst($status) ?>
                                                            </span>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>


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

                                                    <?php if (!empty($labService['FileKetQua'])): ?>

                                                        <a href="<?php echo url('labResults/download/' . $labService['MaDVSD']); ?>"
                                                            class="btn btn-outline-success btn-sm">
                                                            <i class="fas fa-download"></i> Download Result
                                                        </a>
                                                    <?php endif; ?>


                                                    <!-- <?php if (empty($labService['KetQua'])): ?>
                                                        <button class="btn btn-outline-warning btn-sm" onclick="checkStatus(<?php echo $labService['MaDVSD']; ?>)">
                                                            <i class="fas fa-refresh"></i> Check Status
                                                        </button>
                                                    <?php endif; ?> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Summary for this medical record -->
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-primary mb-1">
                                            <?php echo count($services); ?>
                                        </h6>
                                        <small class="text-muted">Total Services</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-success mb-1">
                                            <?php
                                            $completed = array_filter($services, function ($s) {
                                                return !empty($s['KetQua']);
                                            });
                                            echo count($completed);
                                            ?>
                                        </h6>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-warning mb-1">
                                            <?php
                                            $pending = array_filter($services, function ($s) {
                                                return empty($s['KetQua']);
                                            });
                                            echo count($pending);
                                            ?>
                                        </h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-info mb-1">
                                            $<?php
                                                $recordTotal = 0;
                                                foreach ($services as $s) {
                                                    if (isset($s['Service']['DonGia'])) {
                                                        $recordTotal += $s['Service']['DonGia'];
                                                    }
                                                }
                                                echo number_format($recordTotal, 2);
                                                ?>
                                        </h6>
                                        <small class="text-muted">Record Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Overall Summary Cards -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-chart-bar"></i> Overall Summary</h5>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-light">
                    <div class="card-body">
                        <h4 class="text-primary">
                            <?php echo count($groupedLabServices); ?>
                        </h4>
                        <small class="text-muted">Medical Records</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-light">
                    <div class="card-body">
                        <h4 class="text-info">
                            <?php echo count($labServices); ?>
                        </h4>
                        <small class="text-muted">Total Services</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-light">
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
                <div class="card text-center bg-light">
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
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Breakdown -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-microscope"></i> Service Types Breakdown</h6>
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
                                    <div class="d-flex justify-content-between align-items-center">
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
                        <a href="<?php echo url('appointments'); ?>" class="btn btn-primary">
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

    .card-header.bg-primary {
        background-color: #0d6efd !important;
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