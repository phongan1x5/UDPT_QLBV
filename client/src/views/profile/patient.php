<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
// echo '<pre>';
// print_r($patient);
// echo '</pre>';
ob_start();

// Extract patient data from the response structure
$patientData = null;
$error = null;

if ($patient && $patient['status'] === 200 && isset($patient['data'][0])) {
    $patientData = $patient['data'][0];
} else {
    $error = "Failed to load patient information";
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Patient Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($patientData): ?>

        <!-- Patient Profile Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="avatar-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; border-radius: 50%; font-size: 2rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h2 class="mb-1"><?php echo htmlspecialchars($patientData['HoTen']); ?></h2>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-id-card"></i> Patient ID: BN<?php echo htmlspecialchars($patientData['id']); ?>
                                </p>
                                <p class="mb-0">
                                    <span class="badge bg-<?php echo $patientData['is_active'] ? 'success' : 'secondary'; ?>">
                                        <i class="fas fa-circle"></i> <?php echo $patientData['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <?php if ($patientData['BaoHiemYTe']): ?>
                                        <span class="badge bg-info ms-2">
                                            <i class="fas fa-shield-alt"></i> Insured
                                        </span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <!-- <div class="col-md-3 text-end">
                                <a href="<?php echo url('profile/edit'); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-circle"></i> Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5"><strong>Full Name:</strong></div>
                            <div class="col-7"><?php echo htmlspecialchars($patientData['HoTen']); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5"><strong>Date of Birth:</strong></div>
                            <div class="col-7">
                                <?php
                                $birthDate = new DateTime($patientData['NgaySinh']);
                                $age = $birthDate->diff(new DateTime())->y;
                                echo $birthDate->format('F j, Y') . " (Age: $age)";
                                ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5"><strong>Gender:</strong></div>
                            <div class="col-7">
                                <i class="fas fa-<?php echo $patientData['GioiTinh'] === 'Nam' ? 'mars text-primary' : 'venus text-pink'; ?>"></i>
                                <?php echo htmlspecialchars($patientData['GioiTinh']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5"><strong>ID Number:</strong></div>
                            <div class="col-7"><?php echo htmlspecialchars($patientData['SoDinhDanh']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-address-book"></i> Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4"><strong>Phone:</strong></div>
                            <div class="col-8">
                                <a href="tel:<?php echo htmlspecialchars($patientData['SoDienThoai']); ?>" class="text-decoration-none">
                                    <i class="fas fa-phone text-success"></i> <?php echo htmlspecialchars($patientData['SoDienThoai']); ?>
                                </a>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><strong>Email:</strong></div>
                            <div class="col-8">
                                <a href="mailto:<?php echo htmlspecialchars($patientData['Email']); ?>" class="text-decoration-none">
                                    <i class="fas fa-envelope text-primary"></i> <?php echo htmlspecialchars($patientData['Email']); ?>
                                </a>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><strong>Address:</strong></div>
                            <div class="col-8">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <?php echo htmlspecialchars($patientData['DiaChi']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insurance & Medical Information -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Insurance Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($patientData['BaoHiemYTe']): ?>
                            <div class="row mb-3">
                                <div class="col-5"><strong>Insurance ID:</strong></div>
                                <div class="col-7">
                                    <span class="badge bg-info"><?php echo htmlspecialchars($patientData['BaoHiemYTe']); ?></span>
                                </div>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i>
                                You have valid health insurance coverage.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                No health insurance information on file.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Debug Information (remove in production) -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-bug"></i> Debug Information</h6>
                    </div>
                    <div class="card-body">
                        <small>
                            <strong>Patient Record ID:</strong> <?php echo htmlspecialchars($patientData['id']); ?><br>
                            <strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s'); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<style>
    .text-pink {
        color: #e91e63 !important;
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Patient Profile - ' . ($patientData ? htmlspecialchars($patientData['HoTen']) : 'Patient') . ' - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>