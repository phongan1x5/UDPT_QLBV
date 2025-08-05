<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
echo '<pre>';
print_r($patient);
echo '</pre>';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-file-medical-alt"></i> Medical Records</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Medical Records</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Patient Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Patient Information
                        <span class="badge bg-<?php echo $patient['is_active'] ? 'success' : 'danger'; ?> ms-2">
                            <?php echo $patient['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold text-muted" style="width: 40%;">
                                        <i class="fas fa-user text-primary"></i> Full Name:
                                    </td>
                                    <td><?php echo htmlspecialchars($patient['HoTen']); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">
                                        <i class="fas fa-id-card text-primary"></i> Patient ID:
                                    </td>
                                    <td><span class="badge bg-info">BN<?php echo $patient['id']; ?></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">
                                        <i class="fas fa-calendar text-primary"></i> Date of Birth:
                                    </td>
                                    <td>
                                        <?php
                                        $dob = new DateTime($patient['NgaySinh']);
                                        echo $dob->format('F j, Y');

                                        // Calculate age
                                        $today = new DateTime();
                                        $age = $today->diff($dob)->y;
                                        echo " <small class='text-muted'>(Age: {$age} years)</small>";
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">
                                        <i class="fas fa-venus-mars text-primary"></i> Gender:
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $patient['GioiTinh'] === 'Nam' ? 'info' : ($patient['GioiTinh'] === 'Nữ' ? 'pink' : 'secondary'); ?>">
                                            <?php echo htmlspecialchars($patient['GioiTinh']); ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold text-muted" style="width: 40%;">
                                        <i class="fas fa-phone text-primary"></i> Phone:
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo $patient['SoDienThoai']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($patient['SoDienThoai']); ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">
                                        <i class="fas fa-envelope text-primary"></i> Email:
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo $patient['Email']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($patient['Email']); ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">
                                        <i class="fas fa-credit-card text-primary"></i> ID Number:
                                    </td>
                                    <td><code><?php echo htmlspecialchars($patient['SoDinhDanh']); ?></code></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">
                                        <i class="fas fa-shield-alt text-primary"></i> Insurance:
                                    </td>
                                    <td>
                                        <?php if (!empty($patient['BaoHiemYTe'])): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> <?php echo htmlspecialchars($patient['BaoHiemYTe']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle"></i> No Insurance
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Address Row -->
                    <?php if (!empty($patient['DiaChi'])): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="bg-light p-3 rounded">
                                    <strong class="text-muted">
                                        <i class="fas fa-map-marker-alt text-primary"></i> Address:
                                    </strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($patient['DiaChi']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Extract and process medical records from the data structure
    $allMedicalRecords = [];
    $medicalProfile = null;
    $totalRecords = 0;

    if (isset($medicalHistory) && $medicalHistory && $medicalHistory['status'] === 200 && isset($medicalHistory['data'][0])) {
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

    <!-- Patient Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center">
                <div class="card-body">
                    <i class="fas fa-user-circle fa-2x mb-2"></i>
                    <h5><?php echo htmlspecialchars($patient['HoTen']); ?></h5>
                    <small>Patient Name</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <i class="fas fa-birthday-cake fa-2x mb-2"></i>
                    <h5>
                        <?php
                        $dob = new DateTime($patient['NgaySinh']);
                        $today = new DateTime();
                        $age = $today->diff($dob)->y;
                        echo $age;
                        ?>
                    </h5>
                    <small>Years Old</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white text-center">
                <div class="card-body">
                    <i class="fas fa-file-medical fa-2x mb-2"></i>
                    <h5><?php echo $totalRecords; ?></h5>
                    <small>Medical Records</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-<?php echo $patient['is_active'] ? 'success' : 'danger'; ?> text-white text-center">
                <div class="card-body">
                    <i class="fas fa-<?php echo $patient['is_active'] ? 'check-circle' : 'times-circle'; ?> fa-2x mb-2"></i>
                    <h5><?php echo $patient['is_active'] ? 'Active' : 'Inactive'; ?></h5>
                    <small>Patient Status</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Records Count -->
    <div class="row mb-3">
        <div class="col-md-8">
            <h5 class="text-muted">
                <i class="fas fa-list"></i>
                Medical Records for <?php echo htmlspecialchars($patient['HoTen']); ?> (<?php echo $totalRecords; ?> records)
            </h5>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Print All
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportPatientData()">
                <i class="fas fa-download"></i> Export
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
                                            <?php echo htmlspecialchars($record['ChanDoan']); ?>
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
                                        <span class="badge bg-success">Completed</span>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-medical fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Medical Records Found</h4>
                            <p class="text-muted">Medical records for <strong><?php echo htmlspecialchars($patient['HoTen']); ?></strong> will appear here after appointments.</p>
                            <a href="<?php echo url('appointments/book/' . $patient['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Stats Summary -->
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
                        <p class="text-muted">Medical records for <strong><?php echo htmlspecialchars($patient['HoTen']); ?></strong> will appear here after appointments.</p>
                        <a href="<?php echo url('appointments/book/' . $patient['id']) ?>" class="btn btn-primary">
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

    .bg-pink {
        background-color: #e91e63 !important;
    }

    .table-borderless td {
        padding: 0.5rem 0;
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

    function exportPatientData() {
        // Export patient data functionality
        const patientData = {
            name: '<?php echo addslashes($patient['HoTen']); ?>',
            id: '<?php echo $patient['id']; ?>',
            records: <?php echo $totalRecords; ?>
        };

        console.log('Exporting patient data:', patientData);
        // Implement export functionality here
        alert('Export functionality would be implemented here');
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Medical Records - ' . htmlspecialchars($patient['HoTen']) . ' - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>