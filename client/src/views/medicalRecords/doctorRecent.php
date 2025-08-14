<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($medicalRecords);
// echo '</pre>';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-file-medical"></i> My Recent Medical Records</h1>
                    <p class="text-muted mb-0">Review and manage your recent patient examinations</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <a href="<?php echo url('dashboard'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <div class="h2 mb-2"><?php echo count($medicalRecords); ?></div>
                    <div>Total Records</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <?php
                    $pendingCount = 0;
                    foreach ($medicalRecords as $record) {
                        if ($record['ChanDoan'] === '@Pending examination') {
                            $pendingCount++;
                        }
                    }
                    ?>
                    <div class="h2 mb-2"><?php echo $pendingCount; ?></div>
                    <div>Pending Examination</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <div class="h2 mb-2"><?php echo count($medicalRecords) - $pendingCount; ?></div>
                    <div>Completed</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <?php
                    $todayRecords = 0;
                    $today = date('Y-m-d');
                    foreach ($medicalRecords as $record) {
                        if ($record['NgayKham'] === $today) {
                            $todayRecords++;
                        }
                    }
                    ?>
                    <div class="h2 mb-2"><?php echo $todayRecords; ?></div>
                    <div>Today's Records</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search by record ID, appointment ID, or diagnosis...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending Examination</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="dateFilter">
                                <option value="">All Dates</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Records List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Medical Records
                        <span class="badge bg-primary ms-2" id="recordCount"><?php echo count($medicalRecords); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($medicalRecords)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Medical Records Found</h5>
                            <p class="text-muted">You don't have any recent medical records.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="recordsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> Record ID</th>
                                        <th><i class="fas fa-calendar"></i> Exam Date</th>
                                        <th><i class="fas fa-calendar-check"></i> Appointment</th>
                                        <th><i class="fas fa-folder"></i> Patient File</th>
                                        <th><i class="fas fa-stethoscope"></i> Diagnosis</th>
                                        <th><i class="fas fa-sticky-note"></i> Notes</th>
                                        <th><i class="fas fa-cog"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medicalRecords as $record): ?>
                                        <tr class="record-row"
                                            data-record-id="<?php echo $record['MaGiayKhamBenh']; ?>"
                                            data-diagnosis="<?php echo strtolower($record['ChanDoan']); ?>"
                                            data-date="<?php echo $record['NgayKham']; ?>">
                                            <td>
                                                <strong class="text-primary">#<?php echo $record['MaGiayKhamBenh']; ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $examDate = new DateTime($record['NgayKham']);
                                                $today = new DateTime();
                                                $diff = $today->diff($examDate);

                                                if ($examDate->format('Y-m-d') === $today->format('Y-m-d')) {
                                                    echo '<span class="badge bg-info">Today</span><br>';
                                                } elseif ($diff->days === 1 && $examDate < $today) {
                                                    echo '<span class="badge bg-secondary">Yesterday</span><br>';
                                                } elseif ($diff->days <= 7 && $examDate < $today) {
                                                    echo '<span class="badge bg-secondary">' . $diff->days . ' days ago</span><br>';
                                                }
                                                ?>
                                                <small class="text-muted"><?php echo $examDate->format('M j, Y'); ?></small>
                                            </td>
                                            <td>
                                                <a href="<?php echo url('appointments/detail/' . $record['MaLichHen']); ?>"
                                                    class="text-decoration-none">
                                                    <i class="fas fa-calendar-check text-info"></i> #<?php echo $record['MaLichHen']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo url('patients/detail/' . $record['MaHSBA']); ?>"
                                                    class="text-decoration-none">
                                                    <i class="fas fa-user text-success"></i> #<?php echo $record['MaHSBA']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($record['ChanDoan'] === '@Pending examination'): ?>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Completed
                                                    </span>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($record['ChanDoan'], 0, 30)); ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="text-muted small" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($record['LuuY'], 0, 50)); ?>
                                                    <?php if (strlen($record['LuuY']) > 50): ?>...<?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo url('medicalRecords/view-detail/' . $record['MaGiayKhamBenh']); ?>"
                                                        class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <!-- <?php if ($record['ChanDoan'] === '@Pending examination'): ?>
                                                        <a href="<?php echo url('consultation/' . $record['MaLichHen']); ?>"
                                                            class="btn btn-outline-success" title="Continue Consultation">
                                                            <i class="fas fa-stethoscope"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo url('medical-records/edit/' . $record['MaGiayKhamBenh']); ?>"
                                                            class="btn btn-outline-warning" title="Edit Record">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button class="btn btn-outline-info"
                                                        onclick="printRecord(<?php echo $record['MaGiayKhamBenh']; ?>)"
                                                        title="Print Record">
                                                        <i class="fas fa-print"></i>
                                                    </button> -->
                                                </div>
                                            </td>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const dateFilter = document.getElementById('dateFilter');
        const recordRows = document.querySelectorAll('.record-row');
        const recordCount = document.getElementById('recordCount');

        function filterRecords() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const dateValue = dateFilter.value;
            let visibleCount = 0;

            recordRows.forEach(row => {
                const recordId = row.getAttribute('data-record-id');
                const diagnosis = row.getAttribute('data-diagnosis');
                const recordDate = new Date(row.getAttribute('data-date'));
                const today = new Date();

                // Search filter
                const matchesSearch = !searchTerm ||
                    recordId.includes(searchTerm) ||
                    diagnosis.includes(searchTerm) ||
                    row.textContent.toLowerCase().includes(searchTerm);

                // Status filter
                const isPending = diagnosis.includes('pending');
                const matchesStatus = !statusValue ||
                    (statusValue === 'pending' && isPending) ||
                    (statusValue === 'completed' && !isPending);

                // Date filter
                let matchesDate = true;
                if (dateValue === 'today') {
                    matchesDate = recordDate.toDateString() === today.toDateString();
                } else if (dateValue === 'week') {
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matchesDate = recordDate >= weekAgo;
                } else if (dateValue === 'month') {
                    const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matchesDate = recordDate >= monthAgo;
                }

                if (matchesSearch && matchesStatus && matchesDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            recordCount.textContent = visibleCount;
        }

        // Event listeners
        searchInput.addEventListener('input', filterRecords);
        statusFilter.addEventListener('change', filterRecords);
        dateFilter.addEventListener('change', filterRecords);
    });

    function printRecord(recordId) {
        window.open(`<?php echo url('medical-records/print/'); ?>${recordId}`, '_blank');
    }

    // Auto-refresh every 5 minutes for pending records
    setInterval(() => {
        const pendingRecords = document.querySelectorAll('[data-diagnosis*="pending"]');
        if (pendingRecords.length > 0) {
            console.log('Auto-refreshing for pending records...');
            // Uncomment to enable auto-refresh
            // window.location.reload();
        }
    }, 5 * 60 * 1000);
</script>

<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .record-row:hover {
        background-color: #f8f9fa;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group-sm>.btn {
            padding: 0.125rem 0.25rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'My Recent Medical Records - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>