<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($staffs);
// echo '</pre>';

// Calculate statistics
$totalStaff = is_array($staffs) ? count($staffs) : 0;
$activeStaff = 0;
$staffByType = [];
$staffByDepartment = [];

if (is_array($staffs) && !empty($staffs)) {
    foreach ($staffs as $staff) {
        // Count active staff
        if (!isset($staff['TrangThai']) || $staff['TrangThai'] !== 'inactive') {
            $activeStaff++;
        }

        // Count by staff type
        $type = $staff['LoaiNhanVien'] ?? 'Unknown';
        $staffByType[$type] = ($staffByType[$type] ?? 0) + 1;

        // Count by department
        $dept = $staff['IDPhongBan'] ?? 'Unknown';
        $staffByDepartment[$dept] = ($staffByDepartment[$dept] ?? 0) + 1;
    }
}
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-users"></i> Staff Management</h1>
                    <!-- <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Staff Management</li>
                        </ol>
                    </nav> -->
                </div>
                <div>
                    <a href="<?php echo url('admin/create-staff'); ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add New Staff
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $totalStaff; ?></h4>
                            <p class="mb-0">Total Staff</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $activeStaff; ?></h4>
                            <p class="mb-0">Active Staff</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo count($staffByType); ?></h4>
                            <p class="mb-0">Staff Types</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-briefcase fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo count($staffByDepartment); ?></h4>
                            <p class="mb-0">Departments</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Search & Filter</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Search Staff</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, ID, phone...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Filter by Type</label>
                            <select id="typeFilter" class="form-select">
                                <option value="">All Types</option>
                                <?php foreach ($staffByType as $type => $count): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo htmlspecialchars($type); ?> (<?php echo $count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Filter by Department</label>
                            <select id="departmentFilter" class="form-select">
                                <option value="">All Departments</option>
                                <?php foreach ($staffByDepartment as $dept => $count): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>">
                                        <?php echo htmlspecialchars($dept); ?> (<?php echo $count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Staff List</h5>
                    <small class="text-muted">Total: <span id="displayCount"><?php echo $totalStaff; ?></span> staff members</small>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($staffs)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Staff Members Found</h5>
                            <p class="text-muted">Start by adding your first staff member.</p>
                            <a href="<?php echo url('admin/create-staff'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Staff Member
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Staff Info</th>
                                        <th>User ID</th>
                                        <th>Type</th>
                                        <th>Department</th>
                                        <th>Contact</th>
                                        <th>Age</th>
                                        <th>Specialty</th>
                                        <th>Status</th>
                                        <!-- <th>Actions</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($staffs as $staff): ?>
                                        <?php
                                        // Calculate age
                                        $age = 'N/A';
                                        if (!empty($staff['NgaySinh'])) {
                                            try {
                                                $birthDate = new DateTime($staff['NgaySinh']);
                                                $today = new DateTime();
                                                $age = $today->diff($birthDate)->y;
                                            } catch (Exception $e) {
                                                $age = 'N/A';
                                            }
                                        }

                                        // Determine status
                                        $status = $staff['TrangThai'] ?? 'active';
                                        $statusClass = $status === 'active' ? 'success' : 'danger';
                                        $statusIcon = $status === 'active' ? 'check-circle' : 'times-circle';
                                        ?>
                                        <tr class="staff-row"
                                            data-name="<?php echo strtolower($staff['HoTen'] ?? ''); ?>"
                                            data-type="<?php echo htmlspecialchars($staff['LoaiNhanVien'] ?? ''); ?>"
                                            data-department="<?php echo htmlspecialchars($staff['IDPhongBan'] ?? ''); ?>"
                                            data-userid="<?php echo strtolower($staff['MaNhanVien'] ?? ''); ?>"
                                            data-phone="<?php echo $staff['SoDienThoai'] ?? ''; ?>"
                                            data-id="<?php echo $staff['MaNhanVien'] ?? ''; ?>">

                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo htmlspecialchars($staff['MaNhanVien'] ?? 'N/A'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                        style="width: 35px; height: 35px; font-size: 14px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($staff['HoTen'] ?? 'N/A'); ?></div>
                                                        <small class="text-muted">
                                                            ID: <?php echo htmlspecialchars($staff['SoDinhDanh'] ?? 'N/A'); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <code class="bg-light p-1 rounded">
                                                    <?php echo htmlspecialchars($staff['MaNhanVien'] ?? 'N/A'); ?>
                                                </code>
                                            </td>

                                            <td>
                                                <?php
                                                $typeColors = [
                                                    'doctor' => 'primary',
                                                    'nurse' => 'info',
                                                    'lab_staff' => 'warning',
                                                    'pharmacist' => 'success',
                                                    'admin' => 'danger'
                                                ];
                                                $typeColor = $typeColors[$staff['LoaiNhanVien'] ?? ''] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $typeColor; ?>">
                                                    <?php echo htmlspecialchars($staff['LoaiNhanVien'] ?? 'Unknown'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <i class="fas fa-building text-primary me-1"></i>
                                                <?php echo htmlspecialchars($staff['IDPhongBan'] ?? 'N/A'); ?>
                                            </td>

                                            <td>
                                                <i class="fas fa-phone text-success me-1"></i>
                                                <?php echo htmlspecialchars($staff['SoDienThoai'] ?? 'N/A'); ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo $age; ?> years
                                                </span>
                                            </td>

                                            <td>
                                                <?php if (!empty($staff['ChuyenKhoa'])): ?>
                                                    <i class="fas fa-stethoscope text-info me-1"></i>
                                                    <?php echo htmlspecialchars($staff['ChuyenKhoa']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <i class="fas fa-<?php echo $statusIcon; ?>"></i>
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>

                                            <!-- <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="viewStaff(<?php echo $staff['IDNhanVien']; ?>)"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                        onclick="editStaff(<?php echo $staff['IDNhanVien']; ?>)"
                                                        title="Edit Staff">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteStaff(<?php echo $staff['IDNhanVien']; ?>, '<?php echo addslashes($staff['HoTen'] ?? ''); ?>')"
                                                        title="Delete Staff">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td> -->
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
</div>

<!-- Staff Details Modal -->
<div class="modal fade" id="staffDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user"></i> Staff Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="staffDetailsContent">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="editFromModal">
                    <i class="fas fa-edit"></i> Edit Staff
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const departmentFilter = document.getElementById('departmentFilter');
        const clearFilters = document.getElementById('clearFilters');
        const staffRows = document.querySelectorAll('.staff-row');
        const displayCount = document.getElementById('displayCount');

        // Search and filter functionality
        function filterStaff() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedType = typeFilter.value;
            const selectedDepartment = departmentFilter.value;
            let visibleCount = 0;

            staffRows.forEach(row => {
                const name = row.getAttribute('data-name');
                const userid = row.getAttribute('data-userid');
                const phone = row.getAttribute('data-phone');
                const id = row.getAttribute('data-id');
                const type = row.getAttribute('data-type');
                const department = row.getAttribute('data-department');

                const matchesSearch = !searchTerm ||
                    name.includes(searchTerm) ||
                    userid.includes(searchTerm) ||
                    phone.includes(searchTerm) ||
                    id.includes(searchTerm);

                const matchesType = !selectedType || type === selectedType;
                const matchesDepartment = !selectedDepartment || department === selectedDepartment;

                if (matchesSearch && matchesType && matchesDepartment) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            displayCount.textContent = visibleCount;
        }

        // Event listeners
        searchInput.addEventListener('input', filterStaff);
        typeFilter.addEventListener('change', filterStaff);
        departmentFilter.addEventListener('change', filterStaff);

        clearFilters.addEventListener('click', function() {
            searchInput.value = '';
            typeFilter.value = '';
            departmentFilter.value = '';
            filterStaff();
        });
    });

    // Staff action functions
    function viewStaff(staffId) {
        const staffData = <?php echo json_encode($staffs); ?>;
        const staff = staffData.find(s => s.IDNhanVien == staffId);

        if (!staff) {
            alert('Staff not found!');
            return;
        }

        const modalContent = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Personal Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Full Name:</strong></td><td>${staff.HoTen || 'N/A'}</td></tr>
                    <tr><td><strong>Date of Birth:</strong></td><td>${staff.NgaySinh ? new Date(staff.NgaySinh).toLocaleDateString() : 'N/A'}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${staff.SoDienThoai || 'N/A'}</td></tr>
                    <tr><td><strong>ID Number:</strong></td><td>${staff.SoDinhDanh || 'N/A'}</td></tr>
                    <tr><td><strong>Address:</strong></td><td>${staff.DiaChi || 'N/A'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Employment Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Staff ID:</strong></td><td><span class="badge bg-secondary">${staff.IDNhanVien}</span></td></tr>
                    <tr><td><strong>User ID:</strong></td><td><code>${staff.MaNhanVien || 'N/A'}</code></td></tr>
                    <tr><td><strong>Staff Type:</strong></td><td><span class="badge bg-primary">${staff.LoaiNhanVien || 'N/A'}</span></td></tr>
                    <tr><td><strong>Department:</strong></td><td>${staff.IDPhongBan || 'N/A'}</td></tr>
                    <tr><td><strong>Specialization:</strong></td><td>${staff.ChuyenKhoa || 'Not specified'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${(staff.TrangThai === 'active' || !staff.TrangThai) ? 'success' : 'danger'}">${staff.TrangThai || 'Active'}</span></td></tr>
                </table>
            </div>
        </div>
    `;

        document.getElementById('staffDetailsContent').innerHTML = modalContent;
        document.getElementById('editFromModal').onclick = () => editStaff(staffId);

        new bootstrap.Modal(document.getElementById('staffDetailsModal')).show();
    }

    function editStaff(staffId) {
        window.location.href = `<?php echo url('admin/edit-staff'); ?>/${staffId}`;
    }

    function deleteStaff(staffId, staffName) {
        if (confirm(`Are you sure you want to delete staff member "${staffName}"?\n\nThis action cannot be undone.`)) {
            fetch(`<?php echo url('admin/delete-staff'); ?>/${staffId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Staff member deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Failed to delete staff member: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the staff member.');
                });
        }
    }
</script>

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

    .staff-row:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }

    .btn-group .btn {
        border: 1px solid #dee2e6;
    }

    .btn-group .btn:hover {
        z-index: 1;
    }

    .badge {
        font-size: 0.75em;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group .btn {
            padding: 0.25rem 0.4rem;
        }

        .card-body {
            padding: 0.75rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Staff Management - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>