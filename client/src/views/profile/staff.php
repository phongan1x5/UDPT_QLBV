<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();

// Extract staff data from the response structure
$staffData = null;
$error = null;

if ($staff && $staff['status'] === 200 && isset($staff['data'][0])) {
    $staffData = $staff['data'][0];
} else {
    $error = "Failed to load staff information";
}

// Helper function to get staff type display name and icon
function getStaffTypeInfo($type)
{
    switch (strtolower($type)) {
        case 'doctor':
            return ['name' => 'Doctor', 'icon' => 'fas fa-user-md', 'color' => 'primary', 'badge' => 'success'];
        case 'desk_staff':
            return ['name' => 'Desk Staff', 'icon' => 'fas fa-user-nurse', 'color' => 'info', 'badge' => 'info'];
        case 'lab_staff':
            return ['name' => 'Laboratory Staff', 'icon' => 'fas fa-flask', 'color' => 'warning', 'badge' => 'warning'];
        case 'pharmacist':
            return ['name' => 'Pharmacist', 'icon' => 'fas fa-pills', 'color' => 'success', 'badge' => 'primary'];
        case 'admin':
            return ['name' => 'Administrator', 'icon' => 'fas fa-user-shield', 'color' => 'dark', 'badge' => 'dark'];
        default:
            return ['name' => 'Staff Member', 'icon' => 'fas fa-user', 'color' => 'secondary', 'badge' => 'secondary'];
    }
}

$staffTypeInfo = $staffData ? getStaffTypeInfo($staffData['LoaiNhanVien']) : null;
error_log(print_r($staffTypeInfo, true));
?>

<div class="container-fluid py-4">
    <?php if ($error): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger text-white">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        </div>
    <?php else: ?>

        <!-- Staff Header Card -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0">👨‍⚕️ Staff Profile</h6>
                            </div>
                            <!-- <div class="col-6 text-end">
                                <button class="btn btn-outline-primary btn-sm mb-0">
                                    <i class="fas fa-edit me-1"></i> Edit Profile
                                </button>
                            </div> -->
                        </div>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="row">
                            <!-- Profile Picture Section -->
                            <div class="col-lg-3 col-md-4">
                                <div class="text-center">
                                    <div class="avatar avatar-xxl position-relative">
                                        <div class="bg-gradient-<?php echo $staffTypeInfo['color']; ?> shadow-<?php echo $staffTypeInfo['color']; ?> border-radius-lg d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; margin: 0 auto;">
                                            <i class="<?php echo $staffTypeInfo['icon']; ?> text-white" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                    <h5 class="mt-3 mb-1"><?php echo htmlspecialchars($staffData['HoTen']); ?></h5>
                                    <span class="badge bg-gradient-<?php echo $staffTypeInfo['badge']; ?> badge-lg">
                                        <i class="<?php echo $staffTypeInfo['icon']; ?> me-1"></i>
                                        <?php echo $staffTypeInfo['name']; ?>
                                    </span>
                                    <p class="text-secondary text-sm mt-2">
                                        Staff ID: #<?php echo $staffData['MaNhanVien']; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="col-lg-9 col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                                <i class="fas fa-user me-1"></i> Full Name
                                            </label>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?php echo htmlspecialchars($staffData['HoTen']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                                <i class="fas fa-id-card me-1"></i> ID Number
                                            </label>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?php echo htmlspecialchars($staffData['SoDinhDanh']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                                <i class="fas fa-birthday-cake me-1"></i> Date of Birth
                                            </label>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?php
                                                $dob = new DateTime($staffData['NgaySinh']);
                                                echo $dob->format('F j, Y');

                                                // Calculate age
                                                $now = new DateTime();
                                                $age = $now->diff($dob)->y;
                                                echo " <span class='text-secondary'>($age years old)</span>";
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                                <i class="fas fa-phone me-1"></i> Phone Number
                                            </label>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <a href="tel:<?php echo $staffData['SoDienThoai']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($staffData['SoDienThoai']); ?>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-item mb-3">
                                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                                <i class="fas fa-map-marker-alt me-1"></i> Address
                                            </label>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?php echo htmlspecialchars($staffData['DiaChi']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Information Card -->
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm border-radius-md bg-gradient-primary shadow text-center me-2">
                                <i class="fas fa-briefcase text-white opacity-10"></i>
                            </div>
                            <h6 class="mb-0">Work Information</h6>
                        </div>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="info-item mb-3">
                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                <i class="fas fa-building me-1"></i> Department ID
                            </label>
                            <p class="text-sm font-weight-bold mb-0">
                                Department #<?php echo $staffData['IDPhongBan']; ?>
                            </p>
                        </div>

                        <div class="info-item mb-3">
                            <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                <i class="fas fa-user-tag me-1"></i> Staff Type
                            </label>
                            <p class="text-sm font-weight-bold mb-0">
                                <?php if ($staffTypeInfo): ?>
                                    <span style="
                                        display: inline-block;
                                        padding: 0.5rem 0.875rem;
                                        background: linear-gradient(135deg, #ffc107 0%, #ffcd3c 100%);
                                        color: #000;
                                        border-radius: 0.5rem;
                                        font-size: 0.875rem;
                                        font-weight: 600;
                                        box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
                                        border: 1px solid rgba(255, 193, 7, 0.5);
                                    ">
                                        <i class="<?php echo $staffTypeInfo['icon']; ?>" style="margin-right: 0.5rem; color: #663c00;"></i>
                                        <?php echo $staffTypeInfo['name']; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: red;">❌ No staff type info</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <?php if (!empty($staffData['ChuyenKhoa'])): ?>
                            <div class="info-item mb-3">
                                <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                    <i class="fas fa-stethoscope me-1"></i> Specialization
                                </label>
                                <p class="text-sm font-weight-bold mb-0">
                                    <?php echo htmlspecialchars($staffData['ChuyenKhoa']); ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="info-item mb-3">
                                <label class="form-label text-uppercase text-xs font-weight-bolder opacity-7">
                                    <i class="fas fa-stethoscope me-1"></i> Specialization
                                </label>
                                <p class="text-sm text-secondary mb-0">
                                    <em>No specialization specified</em>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <!-- <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm border-radius-md bg-gradient-success shadow text-center me-2">
                                <i class="fas fa-bolt text-white opacity-10"></i>
                            </div>
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-2"></i> Edit Profile
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-key me-2"></i> Change Password
                            </button>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-calendar me-2"></i> View Schedule
                            </button>

                            <?php if (strtolower($staffData['LoaiNhanVien']) === 'doctor'): ?>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-user-injured me-2"></i> View Patients
                                </button>
                            <?php elseif (strtolower($staffData['LoaiNhanVien']) === 'lab_staff'): ?>
                                <button class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-flask me-2"></i> Lab Results
                                </button>
                            <?php elseif (strtolower($staffData['LoaiNhanVien']) === 'pharmacist'): ?>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-pills me-2"></i> Prescriptions
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Statistics Row (if applicable) -->
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Experience</p>
                                    <h5 class="font-weight-bolder">
                                        <?php
                                        // Calculate years since birth (placeholder for experience)
                                        $dob = new DateTime($staffData['NgaySinh']);
                                        $now = new DateTime();
                                        $years = max(0, $now->diff($dob)->y - 22); // Assuming 22 years minimum age for staff
                                        echo $years . ' years';
                                        ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-award text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Department</p>
                                    <h5 class="font-weight-bolder">
                                        Dept. <?php echo $staffData['IDPhongBan']; ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-building text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Status</p>
                                    <h5 class="font-weight-bolder">
                                        <span class="badge bg-gradient-success">Active</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-check-circle text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Staff ID</p>
                                    <h5 class="font-weight-bolder">
                                        #<?php echo $staffData['MaNhanVien']; ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-id-badge text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<style>
    .info-item label {
        margin-bottom: 0.25rem;
    }

    .info-item p {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .avatar-xxl {
        width: 120px;
        height: 120px;
    }

    .card {
        box-shadow: 0 0 1.25rem rgba(31, 45, 61, 0.08);
        border: 1px solid rgba(31, 45, 61, 0.05);
    }

    .badge-lg {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }

    .btn:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Staff Profile - ' . ($staffData ? htmlspecialchars($staffData['HoTen']) : 'Staff') . ' - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>