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
                    <h1><i class="fas fa-file-medical-alt"></i> Medical Record Details</h1>
                    <!-- <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('medical-records'); ?>">Medical Records</a></li>
                            <li class="breadcrumb-item active">Record #<?php echo $medicalRecord['MedicalRecord']['MaGiayKhamBenh']; ?></li>
                        </ol>
                    </nav> -->
                </div>
                <div>
                    <!-- <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print"></i> Print
                    </button> -->
                    <a href="<?php echo url('medicalRecords/' . $medicalRecord['MedicalRecord']['MaHSBA']); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Records
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Record Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Medical Record Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Record ID:</strong></td>
                                    <td>#<?php echo htmlspecialchars($medicalRecord['MedicalRecord']['MaGiayKhamBenh']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Patient File ID:</strong></td>
                                    <td>#<?php echo htmlspecialchars($medicalRecord['MedicalRecord']['MaHSBA']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Doctor ID:</strong></td>
                                    <td>#<?php echo htmlspecialchars($medicalRecord['MedicalRecord']['BacSi']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Examination Date:</strong></td>
                                    <td><?php echo date('M j, Y', strtotime($medicalRecord['MedicalRecord']['NgayKham'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Appointment ID:</strong></td>
                                    <td>#<?php echo htmlspecialchars($medicalRecord['MedicalRecord']['MaLichHen']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Diagnosis -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-stethoscope"></i> Diagnosis</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($medicalRecord['MedicalRecord']['ChanDoan'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor's Notes -->
                    <?php if (!empty($medicalRecord['MedicalRecord']['LuuY'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-sticky-note"></i> Doctor's Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($medicalRecord['MedicalRecord']['LuuY'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Lab Services -->
    <?php if (!empty($labServices)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-flask"></i> Lab Services
                            <span class="badge bg-light text-dark ms-2"><?php echo count($labServices); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($labServices as $index => $service): ?>
                            <div class="border rounded p-3 <?php echo $index < count($labServices) - 1 ? 'mb-3' : ''; ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-2 text-primary">
                                            <?php echo htmlspecialchars($service['Service']['TenDichVu']); ?>
                                        </h6>
                                        <p class="mb-2 text-muted"><?php echo htmlspecialchars($service['Service']['NoiDungDichVu']); ?></p>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-info me-2">$<?php echo number_format($service['Service']['DonGia'], 2); ?></span>
                                            <small class="text-muted">
                                                Requested: <?php echo date('M j, Y H:i', strtotime($service['ThoiGian'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if (empty($service['KetQua'])): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                            <br><small class="text-muted">Results not available yet</small>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </span>
                                            <br>
                                            <div class="mt-2">
                                                <strong>Results:</strong>
                                                <p class="mb-1"><?php echo nl2br(htmlspecialchars($service['KetQua'])); ?></p>
                                                <?php if (!empty($service['FileKetQua'])): ?>
                                                    <a href="<?php echo $service['FileKetQua']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-alt"></i> View File
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Lab Services Summary -->
                        <div class="row mt-3 pt-3 border-top">
                            <div class="col-md-6">
                                <?php
                                $pendingServices = array_filter($labServices, function ($service) {
                                    return empty($service['KetQua']);
                                });
                                $completedServices = count($labServices) - count($pendingServices);
                                ?>
                                <div class="d-flex justify-content-between">
                                    <span>Total Services:</span>
                                    <strong><?php echo count($labServices); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Completed:</span>
                                    <strong class="text-success"><?php echo $completedServices; ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Pending:</span>
                                    <strong class="text-warning"><?php echo count($pendingServices); ?></strong>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <?php
                                $totalCost = array_sum(array_column(array_column($labServices, 'Service'), 'DonGia'));
                                ?>
                                <div class="h5 mb-0">
                                    Total Cost: <span class="text-primary">$<?php echo number_format($totalCost, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Prescription -->
    <?php if (!empty($prescription) && !empty($prescription['Medicines'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-prescription"></i> Prescription
                            <span class="badge bg-light text-dark ms-2">#<?php echo $prescription['MaToaThuoc']; ?></span>
                            <span class="badge bg-success ms-2"><?php echo $prescription['TrangThaiToaThuoc']; ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3"><i class="fas fa-pills"></i> Prescribed Medicines</h6>

                                <?php foreach ($prescription['Medicines'] as $index => $medicine): ?>
                                    <div class="border rounded p-3 <?php echo $index < count($prescription['Medicines']) - 1 ? 'mb-3' : ''; ?>">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($medicine['TenThuoc']); ?></h6>
                                                <p class="mb-1">
                                                    <strong>Quantity:</strong> <?php echo $medicine['SoLuong']; ?> <?php echo htmlspecialchars($medicine['DonViTinh']); ?>
                                                </p>
                                                <?php if (!empty($medicine['GhiChu'])): ?>
                                                    <p class="mb-1 text-muted">
                                                        <strong>Instructions:</strong> <?php echo htmlspecialchars($medicine['GhiChu']); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h6 mb-0">$<?php echo number_format($medicine['GiaTien'], 2); ?></div>
                                                    <small class="text-muted">per <?php echo htmlspecialchars($medicine['DonViTinh']); ?></small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-end">
                                                    <div class="h6 mb-0 text-success">
                                                        $<?php echo number_format($medicine['GiaTien'] * $medicine['SoLuong'], 2); ?>
                                                    </div>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Prescription Summary -->
                                <div class="row mt-3 pt-3 border-top">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Medicines:</span>
                                            <strong><?php echo count($prescription['Medicines']); ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Total Items:</span>
                                            <strong><?php echo array_sum(array_column($prescription['Medicines'], 'SoLuong')); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <?php
                                        $prescriptionTotal = 0;
                                        foreach ($prescription['Medicines'] as $medicine) {
                                            $prescriptionTotal += $medicine['GiaTien'] * $medicine['SoLuong'];
                                        }
                                        ?>
                                        <div class="h5 mb-0">
                                            Prescription Total: <span class="text-success">$<?php echo number_format($prescriptionTotal, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Summary Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Visit Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="h4 text-primary mb-1"><?php echo count($labServices); ?></div>
                            <div class="text-muted">Lab Services</div>
                        </div>
                        <div class="col-md-3">
                            <div class="h4 text-info mb-1"><?php echo !empty($prescription['Medicines']) ? count($prescription['Medicines']) : 0; ?></div>
                            <div class="text-muted">Medicines</div>
                        </div>
                        <div class="col-md-3">
                            <div class="h4 text-success mb-1">
                                $<?php
                                    $totalVisitCost = 0;
                                    if (!empty($labServices)) {
                                        $totalVisitCost += array_sum(array_column(array_column($labServices, 'Service'), 'DonGia'));
                                    }
                                    if (!empty($prescription['Medicines'])) {
                                        foreach ($prescription['Medicines'] as $medicine) {
                                            $totalVisitCost += $medicine['GiaTien'] * $medicine['SoLuong'];
                                        }
                                    }
                                    echo number_format($totalVisitCost, 2);
                                    ?>
                            </div>
                            <div class="text-muted">Total Cost</div>
                        </div>
                        <div class="col-md-3">
                            <div class="h4 text-warning mb-1"><?php echo date('M j, Y', strtotime($medicalRecord['MedicalRecord']['NgayKham'])); ?></div>
                            <div class="text-muted">Visit Date</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {

        .btn,
        .breadcrumb,
        .no-print {
            display: none !important;
        }

        .card {
            border: 1px solid #000 !important;
            break-inside: avoid;
        }

        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Medical Record Details - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>