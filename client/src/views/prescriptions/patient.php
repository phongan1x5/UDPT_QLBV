<?php
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-prescription-bottle-alt"></i> My Prescriptions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Prescriptions</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if (!empty($prescriptions)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-pills"></i> Prescription History
                        </h5>
                        <span class="badge bg-info">
                            <?php echo count($prescriptions); ?> Total Prescriptions
                        </span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($prescriptions as $prescriptionItem):
                            // Extract the actual prescription data
                            $prescriptionData = $prescriptionItem['prescription'][0]; // The actual prescription is in index 0
                            $httpStatus = $prescriptionItem['prescription'][1]; // HTTP status is in index 1
                            $medicalRecord = $prescriptionItem['MedicalRecord'] ?? null; // Medical record info

                            // Skip if HTTP status is not 200
                            if ($httpStatus !== 200) continue;
                        ?>

                            <div class="card mb-3 border-left-<?php echo $prescriptionData['TrangThaiToaThuoc'] === 'Active' ? 'success' : ($prescriptionData['TrangThaiToaThuoc'] === 'Completed' ? 'primary' : 'secondary'); ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-file-prescription"></i>
                                                    Prescription #<?php echo $prescriptionData['MaToaThuoc']; ?>
                                                </h6>
                                                <span class="badge bg-<?php echo $prescriptionData['TrangThaiToaThuoc'] === 'Active' ? 'success' : ($prescriptionData['TrangThaiToaThuoc'] === 'Completed' ? 'primary' : 'secondary'); ?>">
                                                    <?php echo $prescriptionData['TrangThaiToaThuoc']; ?>
                                                </span>
                                            </div>

                                            <?php if ($medicalRecord): ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-stethoscope"></i>
                                                        Related to: <?php echo htmlspecialchars($medicalRecord['ChanDoan']); ?>
                                                        <br>
                                                        <i class="fas fa-calendar"></i>
                                                        Date: <?php
                                                                $date = new DateTime($medicalRecord['NgayKham']);
                                                                echo $date->format('F j, Y');
                                                                ?>
                                                        <br>
                                                        <i class="fas fa-user-md"></i>
                                                        Doctor ID: <?php echo htmlspecialchars($medicalRecord['BacSi']); ?>
                                                        <?php if (!empty($medicalRecord['LuuY'])): ?>
                                                            <br>
                                                            <i class="fas fa-sticky-note"></i>
                                                            Notes: <?php echo htmlspecialchars($medicalRecord['LuuY']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>

                                            <div class="medicines-list">
                                                <h6 class="text-primary mb-2">
                                                    <i class="fas fa-pills"></i> Prescribed Medicines
                                                    <span class="badge bg-secondary"><?php echo count($prescriptionData['Medicines']); ?> items</span>
                                                </h6>
                                                <?php if (!empty($prescriptionData['Medicines'])): ?>
                                                    <div class="row">
                                                        <?php foreach ($prescriptionData['Medicines'] as $medicine): ?>
                                                            <div class="col-md-6 mb-2">
                                                                <div class="medicine-item p-3 bg-light rounded">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <h6 class="mb-1 text-primary">
                                                                                <?php echo htmlspecialchars($medicine['TenThuoc']); ?>
                                                                            </h6>
                                                                            <div class="small text-muted">
                                                                                <i class="fas fa-capsules"></i>
                                                                                Quantity: <?php echo $medicine['SoLuong']; ?> <?php echo htmlspecialchars($medicine['DonViTinh']); ?>
                                                                            </div>
                                                                            <?php if (!empty($medicine['GhiChu'])): ?>
                                                                                <div class="small text-info mt-1">
                                                                                    <i class="fas fa-info-circle"></i>
                                                                                    <?php echo htmlspecialchars($medicine['GhiChu']); ?>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <div class="small text-muted">
                                                                                $<?php echo number_format($medicine['GiaTien'], 2); ?>/unit
                                                                            </div>
                                                                            <span class="badge bg-success">
                                                                                $<?php echo number_format($medicine['SoLuong'] * $medicine['GiaTien'], 2); ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>

                                                    <!-- Total Cost -->
                                                    <?php
                                                    $totalCost = 0;
                                                    foreach ($prescriptionData['Medicines'] as $medicine) {
                                                        $totalCost += $medicine['SoLuong'] * $medicine['GiaTien'];
                                                    }
                                                    ?>
                                                    <div class="mt-3 pt-3 border-top">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold">Total Cost:</span>
                                                            <span class="h5 text-success mb-0">
                                                                $<?php echo number_format($totalCost, 2); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-muted">No medicine details available</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4 text-end">
                                            <div class="btn-group-vertical" role="group">
                                                <a href="/prescriptions/view/<?php echo $prescriptionData['MaToaThuoc']; ?>"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>

                                                <?php if ($prescriptionData['TrangThaiToaThuoc'] === 'Active'): ?>
                                                    <form method="POST" action="/prescriptions/update-status" class="d-inline">
                                                        <input type="hidden" name="prescription_id" value="<?php echo $prescriptionData['MaToaThuoc']; ?>">
                                                        <input type="hidden" name="status" value="Completed">
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            onclick="return confirm('Mark this prescription as completed?')">
                                                            <i class="fas fa-check"></i> Mark Completed
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <button class="btn btn-outline-info btn-sm" onclick="printPrescription(<?php echo $prescriptionData['MaToaThuoc']; ?>)">
                                                    <i class="fas fa-print"></i> Print
                                                </button>
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
                        <h4 class="text-success">
                            <?php
                            $activeCount = 0;
                            foreach ($prescriptions as $p) {
                                if (isset($p['prescription'][0]['TrangThaiToaThuoc']) && $p['prescription'][0]['TrangThaiToaThuoc'] === 'Active') {
                                    $activeCount++;
                                }
                            }
                            echo $activeCount;
                            ?>
                        </h4>
                        <small class="text-muted">Active Prescriptions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary">
                            <?php
                            $completedCount = 0;
                            foreach ($prescriptions as $p) {
                                if (isset($p['prescription'][0]['TrangThaiToaThuoc']) && $p['prescription'][0]['TrangThaiToaThuoc'] === 'Completed') {
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
                            $totalMedicines = 0;
                            foreach ($prescriptions as $p) {
                                if (isset($p['prescription'][0]['Medicines'])) {
                                    $totalMedicines += count($p['prescription'][0]['Medicines']);
                                }
                            }
                            echo $totalMedicines;
                            ?>
                        </h4>
                        <small class="text-muted">Total Medicines</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning">
                            $<?php
                                $grandTotal = 0;
                                foreach ($prescriptions as $p) {
                                    if (isset($p['prescription'][0]['Medicines'])) {
                                        foreach ($p['prescription'][0]['Medicines'] as $m) {
                                            $grandTotal += $m['SoLuong'] * $m['GiaTien'];
                                        }
                                    }
                                }
                                echo number_format($grandTotal, 2);
                                ?>
                        </h4>
                        <small class="text-muted">Total Spent</small>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-prescription-bottle-alt fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Prescriptions Found</h4>
                        <p class="text-muted">You don't have any prescriptions yet. Visit a doctor to get your first prescription.</p>
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

    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    .border-left-secondary {
        border-left: 4px solid #6c757d !important;
    }

    .medicine-item {
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }

    .medicine-item:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        transform: translateY(-2px);
    }
</style>

<script>
    function printPrescription(prescriptionId) {
        window.open('/prescriptions/print/' + prescriptionId, '_blank');
    }
</script>

<?php
$content = ob_get_clean();
$title = 'My Prescriptions - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>