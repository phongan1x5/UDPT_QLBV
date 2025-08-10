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
                    <h1><i class="fas fa-users"></i> Patient Report</h1>
                    <p class="text-muted mb-0">Generate and view patient examination reports by year and month</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard'); ?>">Admin Panel</a></li>
                            <li class="breadcrumb-item active">Patient Report</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button onclick="exportReport()" class="btn btn-success" id="exportBtn" disabled>
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Year and Month Selection Form -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Select Report Parameters
                    </h5>
                </div>
                <div class="card-body">
                    <form id="reportForm" method="POST" action="<?php echo url('admin/patientReport'); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="yearSelect" class="form-label">
                                        <i class="fas fa-calendar"></i> Report Year
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="yearSelect" name="year" required>
                                        <option value="">Choose a year...</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= ($currentYear - 5); $year--):
                                        ?>
                                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="statusFilter" class="form-label">
                                        <i class="fas fa-filter"></i> Filter by Status
                                        <small class="text-muted">(Optional)</small>
                                    </label>
                                    <select class="form-select" id="statusFilter" name="status">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed Examinations</option>
                                        <option value="pending">Pending Examinations</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" id="generateBtn">
                                <i class="fas fa-chart-line"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats Preview -->
        <div class="col-lg-6">
            <div class="card" id="quickStats" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt"></i> Report Summary
                        <span id="reportPeriod" class="badge bg-light text-dark ms-2"></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 col-lg-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                                    <h4 id="totalExaminations" class="text-primary">0</h4>
                                    <small class="text-muted">Examinations</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                                    <h4 id="uniquePatients" class="text-success">0</h4>
                                    <small class="text-muted">Unique Patients</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-user-md fa-2x text-warning mb-2"></i>
                                    <h4 id="totalDoctors" class="text-warning">0</h4>
                                    <small class="text-muted">Doctors</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                                    <h4 id="completedExams" class="text-info">0</h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Filter Controls (shown after data is loaded) -->
    <div class="row mb-4" id="monthFilterControls" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-sliders-h"></i> Filter Controls
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Filter by Month:</label>
                            <select class="form-select" id="monthFilterDropdown" onchange="applyMonthFilter()">
                                <option value="">Show All Months</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filter by Status:</label>
                            <select class="form-select" id="statusFilterDropdown" onchange="applyStatusFilter()">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary mt-4" onclick="clearAllFilters()">
                                <i class="fas fa-eye"></i> Show All
                            </button>
                            <button class="btn btn-outline-info mt-4" onclick="refreshChart()">
                                <i class="fas fa-sync"></i> Refresh Chart
                            </button>
                        </div>
                        <div class="col-md-3 text-end">
                            <small class="text-muted mt-4" id="filterStatus">Showing all records</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="row">
        <div class="col-12">
            <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Generating patient report...</p>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div class="row">
        <div class="col-12">
            <div id="errorMessage" class="alert alert-danger" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="errorText">An error occurred while generating the report.</span>
            </div>
        </div>
    </div>

    <!-- Report Results -->
    <div class="row">
        <div class="col-12">
            <div id="reportResults" style="display: none;">
                <!-- Monthly Chart -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar"></i> Monthly Patient Examination Trends
                            <span id="chartTitle" class="badge bg-light text-dark ms-2"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" width="400" height="100"></canvas>
                    </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-pie-chart"></i> Examination Status Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="statusChart" width="300" height="150"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="h-100 d-flex align-items-center">
                                    <div class="w-100">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="p-3 border rounded">
                                                    <h4 id="completedCount" class="text-success mb-0">0</h4>
                                                    <small class="text-muted">Completed</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 border rounded">
                                                    <h4 id="pendingCount" class="text-warning mb-0">0</h4>
                                                    <small class="text-muted">Pending</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Report Table -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table"></i> Detailed Patient Examination Report
                            <span id="tableTitle" class="badge bg-light text-dark ms-2"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-hover table-striped">
                                <thead class="table-success">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Patient ID</th>
                                        <th>Doctor ID</th>
                                        <th>Diagnosis</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                        <th>Appointment</th>
                                    </tr>
                                </thead>
                                <tbody id="reportTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let reportData = null;
    let fullYearData = null;
    let monthlyChart = null;
    let statusChart = null;
    let selectedMonth = null;
    let selectedStatus = null;
    let selectedYear = null;

    // Form submission
    document.getElementById('reportForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const year = document.getElementById('yearSelect').value;
        if (!year) {
            alert('Please select a year');
            return;
        }

        await generateReport(year);
    });

    async function generateReport(year) {
        // Show loading
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('reportResults').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('quickStats').style.display = 'none';
        document.getElementById('monthFilterControls').style.display = 'none';

        // Disable form
        document.getElementById('generateBtn').disabled = true;
        document.getElementById('generateBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

        try {
            const formData = new FormData();
            formData.append('year', year); // Add year parameter for future backend filtering

            const response = await fetch('<?php echo url('admin/patientReport'); ?>', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const rawData = await response.json();

            // Handle the nested response structure
            let processedData;
            if (Array.isArray(rawData) && rawData.length >= 2) {
                // Server returns [dataWrapper, httpStatus]
                const dataWrapper = rawData[0];
                const httpStatus = rawData[1];

                if (httpStatus === 200 && dataWrapper && dataWrapper.data && Array.isArray(dataWrapper.data)) {
                    processedData = {
                        status: 'success',
                        data: dataWrapper.data,
                        total_records: dataWrapper.total || dataWrapper.data.length
                    };
                } else {
                    throw new Error('Invalid response format from server');
                }
            } else if (rawData.status === 'success') {
                // Standard format (if you change backend later)
                processedData = rawData;
            } else {
                throw new Error(rawData.message || 'Failed to generate report');
            }

            // Filter data by year on frontend (since backend doesn't filter yet)
            if (year && processedData.data) {
                processedData.data = processedData.data.filter(record => {
                    if (record.NgayKham) {
                        const recordYear = new Date(record.NgayKham).getFullYear();
                        return recordYear.toString() === year;
                    }
                    return false;
                });
                processedData.total_records = processedData.data.length;
            }

            reportData = processedData;
            fullYearData = processedData;
            selectedYear = year;
            selectedMonth = null;
            selectedStatus = null;

            setupFilters(processedData.data);
            displayReport(processedData, year);
            document.getElementById('monthFilterControls').style.display = 'block';

        } catch (error) {
            console.error('Error generating report:', error);
            document.getElementById('errorText').textContent = error.message;
            document.getElementById('errorMessage').style.display = 'block';
        } finally {
            // Hide loading and re-enable form
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('generateBtn').disabled = false;
            document.getElementById('generateBtn').innerHTML = '<i class="fas fa-chart-line"></i> Generate Report';
        }
    }

    function setupFilters(data) {
        const monthFilterDropdown = document.getElementById('monthFilterDropdown');
        monthFilterDropdown.innerHTML = '<option value="">Show All Months</option>';

        // Get available months from data
        const availableMonths = new Set();

        if (data && Array.isArray(data)) {
            data.forEach(record => {
                const dateStr = record.NgayKham;
                if (dateStr) {
                    try {
                        const date = new Date(dateStr);
                        availableMonths.add(date.getMonth() + 1);
                    } catch (e) {
                        console.warn('Invalid date:', dateStr);
                    }
                }
            });
        }

        // Populate month dropdown
        const monthNames = [
            '', 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        Array.from(availableMonths).sort((a, b) => a - b).forEach(month => {
            const option = document.createElement('option');
            option.value = month;
            option.textContent = monthNames[month];
            monthFilterDropdown.appendChild(option);
        });
    }

    function applyMonthFilter() {
        const selectedMonthValue = document.getElementById('monthFilterDropdown').value;
        selectedMonth = selectedMonthValue ? parseInt(selectedMonthValue) : null;
        applyFilters();
    }

    function applyStatusFilter() {
        selectedStatus = document.getElementById('statusFilterDropdown').value || null;
        applyFilters();
    }

    function applyFilters() {
        let filteredData = [...fullYearData.data];

        // Apply month filter
        if (selectedMonth) {
            filteredData = filteredData.filter(record => {
                const dateStr = record.NgayKham;
                if (dateStr) {
                    try {
                        const date = new Date(dateStr);
                        return date.getMonth() + 1 === selectedMonth;
                    } catch (e) {
                        return false;
                    }
                }
                return false;
            });
        }

        // Apply status filter
        if (selectedStatus) {
            filteredData = filteredData.filter(record => {
                const isCompleted = !record.ChanDoan.includes('@Pending');
                if (selectedStatus === 'completed') {
                    return isCompleted;
                } else if (selectedStatus === 'pending') {
                    return !isCompleted;
                }
                return true;
            });
        }

        reportData = {
            ...fullYearData,
            data: filteredData,
            total_records: filteredData.length
        };

        // Update filter status
        updateFilterStatus();

        // Update display
        displayReport(reportData, selectedYear);
    }

    function updateFilterStatus() {
        const monthNames = [
            '', 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        let statusText = 'Showing ';
        let titleText = '';

        if (selectedMonth && selectedStatus) {
            const statusName = selectedStatus === 'completed' ? 'Completed' : 'Pending';
            statusText += `${statusName} examinations in ${monthNames[selectedMonth]} ${selectedYear}`;
            titleText = `${monthNames[selectedMonth]} ${selectedYear} - ${statusName}`;
        } else if (selectedMonth) {
            statusText += `${monthNames[selectedMonth]} ${selectedYear}`;
            titleText = `${monthNames[selectedMonth]} ${selectedYear}`;
        } else if (selectedStatus) {
            const statusName = selectedStatus === 'completed' ? 'Completed' : 'Pending';
            statusText += `${statusName} examinations in ${selectedYear}`;
            titleText = `${selectedYear} - ${statusName}`;
        } else {
            statusText += `all examinations in ${selectedYear}`;
            titleText = `Full Year ${selectedYear}`;
        }

        statusText += ` (${reportData.total_records} records)`;

        document.getElementById('filterStatus').textContent = statusText;
        document.getElementById('reportPeriod').textContent = titleText;
        document.getElementById('chartTitle').textContent = titleText;
        document.getElementById('tableTitle').textContent = titleText;
    }

    function clearAllFilters() {
        selectedMonth = null;
        selectedStatus = null;
        reportData = fullYearData;

        document.getElementById('monthFilterDropdown').value = '';
        document.getElementById('statusFilterDropdown').value = '';

        updateFilterStatus();
        displayReport(reportData, selectedYear);
    }

    function refreshChart() {
        generateMonthlyChart(reportData.data, selectedYear);
        generateStatusChart(reportData.data);
    }

    function displayReport(data, year) {
        // Calculate statistics
        const totalExaminations = data.total_records || 0;
        const uniquePatients = new Set(data.data.map(record => record.MaHSBA)).size;
        const uniqueDoctors = new Set(data.data.map(record => record.BacSi)).size;
        const completedExams = data.data.filter(record => !record.ChanDoan.includes('@Pending')).length;
        const pendingExams = totalExaminations - completedExams;

        // Update quick stats
        document.getElementById('totalExaminations').textContent = totalExaminations;
        document.getElementById('uniquePatients').textContent = uniquePatients;
        document.getElementById('totalDoctors').textContent = uniqueDoctors;
        document.getElementById('completedExams').textContent = completedExams;

        // Update status counts
        document.getElementById('completedCount').textContent = completedExams;
        document.getElementById('pendingCount').textContent = pendingExams;

        // Show stats card
        document.getElementById('quickStats').style.display = 'block';

        // Generate charts
        generateMonthlyChart(data.data, year);
        generateStatusChart(data.data);

        // Populate table
        populateTable(data.data);

        // Show results
        document.getElementById('reportResults').style.display = 'block';

        // Enable export button
        document.getElementById('exportBtn').disabled = false;

        // Update filter status if no filters applied yet
        if (!selectedMonth && !selectedStatus) {
            updateFilterStatus();
        }
    }

    function generateMonthlyChart(data, year) {
        const monthlyData = {};
        const monthlyCompleted = {};
        const monthlyPending = {};

        for (let i = 1; i <= 12; i++) {
            monthlyData[i] = 0;
            monthlyCompleted[i] = 0;
            monthlyPending[i] = 0;
        }

        if (data && Array.isArray(data)) {
            data.forEach(record => {
                const dateStr = record.NgayKham;
                if (dateStr) {
                    try {
                        const date = new Date(dateStr);
                        const month = date.getMonth() + 1;
                        monthlyData[month]++;

                        if (record.ChanDoan.includes('@Pending')) {
                            monthlyPending[month]++;
                        } else {
                            monthlyCompleted[month]++;
                        }
                    } catch (e) {
                        console.warn('Invalid date:', dateStr);
                    }
                }
            });
        }

        const ctx = document.getElementById('monthlyChart').getContext('2d');

        if (monthlyChart) {
            monthlyChart.destroy();
        }

        monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                        label: 'Completed Examinations',
                        data: Object.values(monthlyCompleted),
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pending Examinations',
                        data: Object.values(monthlyPending),
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: `Monthly Patient Examination Distribution - ${year}`
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const clickedMonth = elements[0].index + 1;
                        document.getElementById('monthFilterDropdown').value = clickedMonth;
                        applyMonthFilter();
                    }
                }
            }
        });
    }

    function generateStatusChart(data) {
        const completed = data.filter(record => !record.ChanDoan.includes('@Pending')).length;
        const pending = data.length - completed;

        const ctx = document.getElementById('statusChart').getContext('2d');

        if (statusChart) {
            statusChart.destroy();
        }

        statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending'],
                datasets: [{
                    data: [completed, pending],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Examination Status Distribution'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function populateTable(data) {
        const tbody = document.getElementById('reportTableBody');
        tbody.innerHTML = '';

        if (!data || !Array.isArray(data)) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No data available</td></tr>';
            return;
        }

        data.forEach((record, index) => {
            const row = document.createElement('tr');
            const isCompleted = !record.ChanDoan.includes('@Pending');
            const statusBadge = isCompleted ?
                '<span class="badge bg-success">Completed</span>' :
                '<span class="badge bg-warning">Pending</span>';

            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${record.NgayKham || 'N/A'}</td>
                <td>${record.MaHSBA || 'N/A'}</td>
                <td>${record.BacSi || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-info" onclick="showDiagnosis(${index})" title="${record.ChanDoan}">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showNotes(${index})" title="${record.LuuY}">
                        <i class="fas fa-sticky-note"></i> Notes
                    </button>
                </td>
                <td>${record.MaLichHen || 'N/A'}</td>
            `;

            tbody.appendChild(row);
        });
    }

    function showDiagnosis(index) {
        if (!reportData || !reportData.data || !reportData.data[index]) return;

        const record = reportData.data[index];
        const diagnosis = record.ChanDoan || 'No diagnosis available';

        const modalContent = `
            <div class="modal fade" id="diagnosisModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Diagnosis - Patient ${record.MaHSBA}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <strong>Date:</strong> ${record.NgayKham}
                            </div>
                            <div class="mb-3">
                                <strong>Doctor ID:</strong> ${record.BacSi}
                            </div>
                            <div class="mb-3">
                                <strong>Diagnosis:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    ${diagnosis}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        showModal(modalContent, 'diagnosisModal');
    }

    function showNotes(index) {
        if (!reportData || !reportData.data || !reportData.data[index]) return;

        const record = reportData.data[index];
        const notes = record.LuuY || 'No notes available';

        const modalContent = `
            <div class="modal fade" id="notesModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Medical Notes - Patient ${record.MaHSBA}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <strong>Date:</strong> ${record.NgayKham}
                            </div>
                            <div class="mb-3">
                                <strong>Doctor ID:</strong> ${record.BacSi}
                            </div>
                            <div class="mb-3">
                                <strong>Notes:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    ${notes}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        showModal(modalContent, 'notesModal');
    }

    function showModal(content, modalId) {
        // Remove existing modal if any
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', content);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }

    function exportReport() {
        if (!reportData) return;

        let fileName = `patient-report-${selectedYear}`;
        if (selectedMonth) {
            const monthNames = [
                '', 'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            fileName += `-${monthNames[selectedMonth]}`;
        }
        if (selectedStatus) {
            fileName += `-${selectedStatus}`;
        }
        fileName += '.csv';

        // Create CSV content
        let csvContent = "Date,Patient ID,Doctor ID,Diagnosis,Status,Notes,Appointment ID\n";

        reportData.data.forEach(record => {
            const isCompleted = !record.ChanDoan.includes('@Pending');
            const status = isCompleted ? 'Completed' : 'Pending';
            const diagnosis = (record.ChanDoan || '').replace(/"/g, '""');
            const notes = (record.LuuY || '').replace(/"/g, '""');

            csvContent += `"${record.NgayKham || ''}",`;
            csvContent += `"${record.MaHSBA || ''}",`;
            csvContent += `"${record.BacSi || ''}",`;
            csvContent += `"${diagnosis}",`;
            csvContent += `"${status}",`;
            csvContent += `"${notes}",`;
            csvContent += `"${record.MaLichHen || ''}"\n`;
        });

        // Download CSV
        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Patient Report page loaded');
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    #monthlyChart,
    #statusChart {
        max-height: 400px;
        cursor: pointer;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 10px;
        }

        .card {
            margin-bottom: 1rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Patient Report - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>