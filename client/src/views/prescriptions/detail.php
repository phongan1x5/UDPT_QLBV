<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
echo '<pre>';
// print_r($prescriptionDetail);
echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-prescription-bottle"></i> Prescription Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('prescriptions'); ?>">Prescriptions</a></li>
                            <li class="breadcrumb-item active">Prescription #<?php echo $prescriptionDetail['MaToaThuoc']; ?></li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo url('prescriptions'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Prescriptions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-prescription"></i>
                            Prescription #<?php echo htmlspecialchars($prescriptionDetail['MaToaThuoc']); ?>
                        </h5>
                        <div>
                            <?php
                            $statusClass = $prescriptionDetail['TrangThaiToaThuoc'] === 'Active' ? 'success' : 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $statusClass; ?> fs-6">
                                <i class="fas fa-<?php echo $prescriptionDetail['TrangThaiToaThuoc'] === 'Active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                <?php echo htmlspecialchars($prescriptionDetail['TrangThaiToaThuoc']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-prescription-bottle fa-3x text-primary mb-3"></i>
                                <h6 class="text-primary">Prescription ID</h6>
                                <h4 class="text-dark">#<?php echo htmlspecialchars($prescriptionDetail['MaToaThuoc']); ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-file-medical fa-3x text-info mb-3"></i>
                                <h6 class="text-info">Medical Record ID</h6>
                                <h4 class="text-dark">#<?php echo htmlspecialchars($prescriptionDetail['MaGiayKhamBenh']); ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-pills fa-3x text-success mb-3"></i>
                                <h6 class="text-success">Total Medicines</h6>
                                <h4 class="text-dark"><?php echo count($prescriptionDetail['Medicines']); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medicines List -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-pills"></i> Prescribed Medicines
                        <span class="badge bg-light text-success ms-2"><?php echo count($prescriptionDetail['Medicines']); ?> items</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">Medicine ID</th>
                                    <th width="25%">Medicine Name</th>
                                    <th width="12%">Unit</th>
                                    <th width="10%">Quantity</th>
                                    <th width="12%">Unit Price</th>
                                    <th width="12%">Total Price</th>
                                    <th width="21%">Instructions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalCost = 0;
                                foreach ($prescriptionDetail['Medicines'] as $index => $medicine):
                                    $totalPrice = floatval($medicine['SoLuong']) * floatval($medicine['GiaTien']);
                                    $totalCost += $totalPrice;
                                ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">#<?php echo htmlspecialchars($medicine['MaThuoc']); ?></span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong class="text-primary"><?php echo htmlspecialchars($medicine['TenThuoc']); ?></strong>
                                                <br>
                                                <small class="text-muted">Medicine <?php echo $index + 1; ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($medicine['DonViTinh']); ?></span>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?php echo htmlspecialchars($medicine['SoLuong']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-warning">$<?php echo number_format($medicine['GiaTien'], 2); ?></span>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?php echo number_format($totalPrice, 2); ?></strong>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle text-info"></i>
                                                <?php echo nl2br(htmlspecialchars($medicine['GhiChu'])); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Total Prescription Cost:</strong></td>
                                    <td><strong class="text-success fs-5">$<?php echo number_format($totalCost, 2); ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary and Actions -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Prescription Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <i class="fas fa-pills fa-2x text-primary mb-2"></i>
                                <h5><?php echo count($prescriptionDetail['Medicines']); ?></h5>
                                <small class="text-muted">Medicines</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <i class="fas fa-calculator fa-2x text-success mb-2"></i>
                                <h5><?php echo array_sum(array_column($prescriptionDetail['Medicines'], 'SoLuong')); ?></h5>
                                <small class="text-muted">Total Quantity</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>
                                <h5>$<?php echo number_format($totalCost, 2); ?></h5>
                                <small class="text-muted">Total Cost</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <i class="fas fa-<?php echo $prescriptionDetail['TrangThaiToaThuoc'] === 'Active' ? 'check-circle text-success' : 'pause-circle text-secondary'; ?> fa-2x mb-2"></i>
                            <h5><?php echo $prescriptionDetail['TrangThaiToaThuoc']; ?></h5>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Prescription
                        </button>
                        <!-- <a href="<?php echo url('prescriptions/edit/' . $prescriptionDetail['MaToaThuoc']); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Prescription
                        </a> -->
                        <!-- <a href="<?php echo url('medical-records/view/' . $prescriptionDetail['MaGiayKhamBenh']); ?>" class="btn btn-outline-info">
                            <i class="fas fa-file-medical"></i> View Medical Record
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function downloadPDF() {
        // TODO: Implement PDF download functionality
        alert('PDF download functionality would be implemented here');
    }

    // Print specific prescription content
    function printPrescription() {
        window.print();
    }
</script>

<style>
    .border-start {
        border-left-width: 3px !important;
    }

    .border-end {
        border-right: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .border-end {
            border-right: none;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    @media print {

        .btn,
        nav,
        .no-print {
            display: none !important;
        }

        .card {
            border: 1px solid #000 !important;
            break-inside: avoid;
        }

        .container-fluid {
            padding: 0;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Prescription #' . $prescriptionDetail['MaToaThuoc'] . ' - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>