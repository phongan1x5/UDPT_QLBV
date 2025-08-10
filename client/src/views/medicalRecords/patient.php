<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-file-medical-alt"></i> My Medical Records</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Medical Records</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php
    // Extract and process medical records from the data structure
    $allMedicalRecords = [];
    $medicalProfile = null;
    $totalRecords = 0;

    if ($medicalHistory && $medicalHistory['status'] === 200 && isset($medicalHistory['data'][0])) {
        $medicalData = $medicalHistory['data'][0];

        // Get medical profile
        if (isset($medicalData['MedicalProfile'])) {
            $medicalProfile = $medicalData['MedicalProfile'];
        }

        // Get all medical records
        if (isset($medicalData['MedicalRecords']) && is_array($medicalData['MedicalRecords'])) {
            $allMedicalRecords = $medicalData['MedicalRecords'];

            // Sort by date (most recent first)
            usort($allMedicalRecords, function ($a, $b) {
                return strtotime($b['NgayKham']) - strtotime($a['NgayKham']);
            });

            $totalRecords = count($allMedicalRecords);
        }
    }
    ?>

    <!-- Medical Profile Summary -->
    <?php if ($medicalProfile): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-user-md"></i> Medical History Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <p class="mb-0"><?php echo htmlspecialchars($medicalProfile['TienSu']); ?></p>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="badge bg-<?php echo $medicalProfile['is_active'] ? 'success' : 'secondary'; ?> fs-6">
                                    <?php echo $medicalProfile['is_active'] ? 'Active Patient' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Records Count -->
    <div class="row mb-3">
        <div class="col-md-8">
            <h5 class="text-muted">
                <i class="fas fa-list"></i>
                Total: <?php echo $totalRecords; ?> medical records
            </h5>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Print All
            </button>
        </div>
    </div>

    <!-- Medical Records List -->
    <?php if (!empty($allMedicalRecords)): ?>
        <div class="row">
            <div class="col-12">
                <?php foreach ($allMedicalRecords as $index => $record): ?>
                    <div class="card mb-3 border-left-primary shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-1 text-center">
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <strong><?php echo $index + 1; ?></strong>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title text-primary mb-0">
                                            <i class="fas fa-stethoscope"></i>
                                            <?php
                                            if ($record['ChanDoan'] !== '@Pending examination') {
                                                echo htmlspecialchars($record['ChanDoan']);
                                            } else {
                                                echo 'Pending';
                                            }
                                            ?>
                                        </h6>
                                        <small class="text-muted">
                                            Record ID: #<?php echo $record['MaGiayKhamBenh']; ?>
                                        </small>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i>
                                                <strong>Date:</strong>
                                                <?php
                                                $date = new DateTime($record['NgayKham']);
                                                echo $date->format('F j, Y (l)');
                                                ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-user-md"></i>
                                                <strong>Doctor ID:</strong> <?php echo htmlspecialchars($record['BacSi']); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <?php if ($record['MaLichHen']): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check"></i>
                                                <strong>Appointment:</strong> #<?php echo htmlspecialchars($record['MaLichHen']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-text">
                                        <strong class="text-info">Medical Notes:</strong>
                                        <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($record['LuuY'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="mb-2">
                                        <!-- <span class="badge bg-success">Completed</span> -->
                                    </div>
                                    <div class="d-grid gap-1">
                                        <a href="<?php echo url('medicalRecords/view-detail/' . $record['MaGiayKhamBenh']) ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary"><?php echo $totalRecords; ?></h4>
                        <small class="text-muted">Total Records</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success">
                            <?php
                            $thisYear = date('Y');
                            $thisYearCount = count(array_filter($allMedicalRecords, function ($r) use ($thisYear) {
                                return date('Y', strtotime($r['NgayKham'])) == $thisYear;
                            }));
                            echo $thisYearCount;
                            ?>
                        </h4>
                        <small class="text-muted">This Year</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info">
                            <?php
                            $lastRecord = !empty($allMedicalRecords) ? $allMedicalRecords[0] : null;
                            if ($lastRecord) {
                                $daysSince = floor((time() - strtotime($lastRecord['NgayKham'])) / (60 * 60 * 24));
                                echo $daysSince;
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </h4>
                        <small class="text-muted">Days Since Last Visit</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning">
                            <?php
                            $uniqueDoctors = array_unique(array_column($allMedicalRecords, 'BacSi'));
                            echo count($uniqueDoctors);
                            ?>
                        </h4>
                        <small class="text-muted">Doctors Seen</small>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-medical fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Medical Records Found</h4>
                        <p class="text-muted">Your medical records will appear here after your appointments.</p>
                        <a href="/appointments/book" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    @media print {

        .btn,
        nav,
        .no-print {
            display: none !important;
        }
    }
</style>

<script>
    function printRecord(recordId) {
        window.open('/medical-records/print/' + recordId, '_blank');
    }
</script>

<?php
$content = ob_get_clean();
$title = 'My Medical Records - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>