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
                    <h1><i class="fas fa-chart-bar"></i> Prescription Report</h1>
                    <p class="text-muted mb-0">Generate and view prescription reports by year and month</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('admin/dashboard'); ?>">Admin Panel</a></li>
                            <li class="breadcrumb-item active">Prescription Report</li>
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Select Report Parameters
                    </h5>
                </div>
                <div class="card-body">
                    <form id="reportForm" method="POST" action="<?php echo url('admin/prescriptionReport'); ?>">
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
                                    <label for="monthFilter" class="form-label">
                                        <i class="fas fa-filter"></i> Filter by Month
                                        <small class="text-muted">(Optional)</small>
                                    </label>
                                    <select class="form-select" id="monthFilter" name="month">
                                        <option value="">All Months</option>
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="generateBtn">
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
                                    <i class="fas fa-prescription fa-2x text-primary mb-2"></i>
                                    <h4 id="totalPrescriptions" class="text-primary">0</h4>
                                    <small class="text-muted">Prescriptions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                                    <h4 id="totalPatients" class="text-success">0</h4>
                                    <small class="text-muted">Patients</small>
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
                                    <i class="fas fa-pills fa-2x text-danger mb-2"></i>
                                    <h4 id="totalMedicines" class="text-danger">0</h4>
                                    <small class="text-muted">Medicines</small>
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
                        <i class="fas fa-sliders-h"></i> Month Filter Controls
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Month:</label>
                            <select class="form-select" id="monthFilterDropdown" onchange="applyMonthFilter()">
                                <option value="">Show All Months</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary" onclick="showAllMonths()">
                                <i class="fas fa-eye"></i> Show All
                            </button>
                            <button class="btn btn-outline-info" onclick="refreshChart()">
                                <i class="fas fa-sync"></i> Refresh Chart
                            </button>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted" id="filterStatus">Showing all months</small>
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
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Generating prescription report...</p>
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
                            <i class="fas fa-chart-line"></i> Monthly Prescription Trends
                            <span id="chartTitle" class="badge bg-light text-dark ms-2"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" width="400" height="100"></canvas>
                    </div>
                </div>

                <!-- Detailed Report Table -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table"></i> Detailed Prescription Report
                            <span id="tableTitle" class="badge bg-light text-dark ms-2"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-hover table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Patient ID</th>
                                        <th>Doctor ID</th>
                                        <th>Prescription ID</th>
                                        <th>Medicines</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
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
    let fullYearData = null; // Store complete year data
    let monthlyChart = null;
    let selectedMonth = null;
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
            formData.append('year', year);

            const response = await fetch('<?php echo url('admin/prescriptionReport'); ?>', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.status === 'success') {
                reportData = data;
                fullYearData = data; // Store complete data
                selectedYear = year;
                selectedMonth = null;

                // Setup month filter dropdown
                setupMonthFilter(data.data);

                displayReport(data, year);
                document.getElementById('monthFilterControls').style.display = 'block';
            } else {
                throw new Error(data.message || 'Failed to generate report');
            }

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

    function setupMonthFilter(data) {
        const monthFilterDropdown = document.getElementById('monthFilterDropdown');
        monthFilterDropdown.innerHTML = '<option value="">Show All Months</option>';

        // Get available months from data
        const availableMonths = new Set();

        if (data && Array.isArray(data)) {
            data.forEach(record => {
                const dateStr = record.medical_record?.NgayKham;
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

        // Populate dropdown with available months
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

        if (selectedMonth) {
            // Filter data by selected month
            const filteredData = fullYearData.data.filter(record => {
                const dateStr = record.medical_record?.NgayKham;
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

            const monthNames = [
                '', 'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            reportData = {
                ...fullYearData,
                data: filteredData,
                total_records: filteredData.length
            };

            document.getElementById('filterStatus').textContent = `Showing ${monthNames[selectedMonth]} ${selectedYear} (${filteredData.length} records)`;
            document.getElementById('reportPeriod').textContent = `${monthNames[selectedMonth]} ${selectedYear}`;
            document.getElementById('chartTitle').textContent = `${monthNames[selectedMonth]} ${selectedYear}`;
            document.getElementById('tableTitle').textContent = `${monthNames[selectedMonth]} ${selectedYear}`;
        } else {
            // Show all data
            showAllMonths();
        }

        // Update display
        displayReport(reportData, selectedYear, selectedMonth);
    }

    function showAllMonths() {
        selectedMonth = null;
        reportData = fullYearData;
        document.getElementById('monthFilterDropdown').value = '';
        document.getElementById('filterStatus').textContent = `Showing all months of ${selectedYear}`;
        document.getElementById('reportPeriod').textContent = `Year ${selectedYear}`;
        document.getElementById('chartTitle').textContent = `Full Year ${selectedYear}`;
        document.getElementById('tableTitle').textContent = `Full Year ${selectedYear}`;

        displayReport(reportData, selectedYear);
    }

    function refreshChart() {
        generateMonthlyChart(reportData.data, selectedYear, selectedMonth);
    }

    function displayReport(data, year, month = null) {
        // Show quick stats
        document.getElementById('totalPrescriptions').textContent = data.total_records || 0;

        // Calculate unique patients and doctors
        let uniquePatients = new Set();
        let uniqueDoctors = new Set();
        let totalMedicines = 0;

        if (data.data && Array.isArray(data.data)) {
            data.data.forEach(record => {
                if (record.medical_record?.MaHSBA) {
                    uniquePatients.add(record.medical_record.MaHSBA);
                }
                if (record.medical_record?.BacSi) {
                    uniqueDoctors.add(record.medical_record.BacSi);
                }
                if (record.prescription?.medicines) {
                    totalMedicines += record.prescription.medicines.length;
                }
            });
        }

        document.getElementById('totalPatients').textContent = uniquePatients.size;
        document.getElementById('totalDoctors').textContent = uniqueDoctors.size;
        document.getElementById('totalMedicines').textContent = totalMedicines;

        // Show stats card
        document.getElementById('quickStats').style.display = 'block';

        // Generate monthly chart
        generateMonthlyChart(data.data, year, month);

        // Populate table
        populateTable(data.data);

        // Show results
        document.getElementById('reportResults').style.display = 'block';

        // Enable export button
        document.getElementById('exportBtn').disabled = false;
    }

    function generateMonthlyChart(data, year, filterMonth = null) {
        // Group data by month
        const monthlyData = {};
        for (let i = 1; i <= 12; i++) {
            monthlyData[i] = 0;
        }

        if (data && Array.isArray(data)) {
            data.forEach(record => {
                const dateStr = record.medical_record?.NgayKham;
                if (dateStr) {
                    try {
                        const date = new Date(dateStr);
                        const month = date.getMonth() + 1;
                        monthlyData[month]++;
                    } catch (e) {
                        console.warn('Invalid date:', dateStr);
                    }
                }
            });
        }

        const ctx = document.getElementById('monthlyChart').getContext('2d');

        // Destroy existing chart if it exists
        if (monthlyChart) {
            monthlyChart.destroy();
        }

        // Prepare chart data
        let chartLabel = `Prescriptions in ${year}`;
        let backgroundColor = 'rgba(75, 192, 192, 0.1)';
        let borderColor = 'rgb(75, 192, 192)';

        if (filterMonth) {
            const monthNames = [
                '', 'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            chartLabel = `${monthNames[filterMonth]} ${year} Prescriptions`;
            backgroundColor = 'rgba(255, 99, 132, 0.1)';
            borderColor = 'rgb(255, 99, 132)';

            // Highlight selected month
            const backgroundColors = Array(12).fill('rgba(75, 192, 192, 0.1)');
            backgroundColors[filterMonth - 1] = 'rgba(255, 99, 132, 0.3)';
            backgroundColor = backgroundColors;
        }

        monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: chartLabel,
                    data: Object.values(monthlyData),
                    borderColor: borderColor,
                    backgroundColor: backgroundColor,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: filterMonth ?
                            `Monthly Distribution - ${chartLabel}` : `Monthly Prescription Distribution - ${year}`
                    }
                },
                scales: {
                    y: {
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

    function populateTable(data) {
        const tbody = document.getElementById('reportTableBody');
        tbody.innerHTML = '';

        if (!data || !Array.isArray(data)) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No data available</td></tr>';
            return;
        }

        data.forEach((record, index) => {
            const medicalRecord = record.medical_record || {};
            const prescription = record.prescription || {};
            const medicines = prescription.medicines || [];

            const row = document.createElement('tr');

            // Use total_cost from API response if available, otherwise calculate
            const totalCost = prescription.total_cost || medicines.reduce((sum, med) => {
                return sum + ((med.GiaTien || 0) * (med.SoLuong || 0));
            }, 0);

            row.innerHTML = `
            <td>${index + 1}</td>
            <td>${medicalRecord.NgayKham || 'N/A'}</td>
            <td>${medicalRecord.MaHSBA || 'N/A'}</td>
            <td>${medicalRecord.BacSi || 'N/A'}</td>
            <td>${prescription.MaToaThuoc || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="showMedicines(${index})">
                    <i class="fas fa-pills"></i> ${medicines.length} medicines
                </button>
            </td>
            <td class="text-end">${totalCost.toLocaleString()} VND</td>
            <td>
                <span class="badge ${prescription.TrangThaiToaThuoc === 'Active' ? 'bg-success' : 'bg-secondary'}">
                    ${prescription.TrangThaiToaThuoc || 'Unknown'}
                </span>
            </td>
        `;

            tbody.appendChild(row);
        });
    }

    function showMedicines(index) {
        if (!reportData || !reportData.data || !reportData.data[index]) return;

        const prescription = reportData.data[index].prescription;
        const medicines = prescription.medicines || [];

        let medicinesList = medicines.map(med =>
            `<li class="list-group-item d-flex justify-content-between">
            <div>
                <strong>${med.TenThuoc}</strong> - ${med.SoLuong} ${med.DonViTinh || 'units'}
                ${med.GhiChu ? `<br><small class="text-muted">${med.GhiChu}</small>` : ''}
            </div>
            <div class="text-end">
                <span class="text-muted">${(med.GiaTien || 0).toLocaleString()} VND/unit</span><br>
                <strong>${(med.ThanhTien || 0).toLocaleString()} VND</strong>
            </div>
        </li>`
        ).join('');

        // Create modal content
        const modalContent = `
        <div class="modal fade" id="medicinesModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Medicines in Prescription #${prescription.MaToaThuoc}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            ${medicinesList}
                        </ul>
                        <div class="mt-3 text-end">
                            <h5>Total Cost: <span class="text-success">${(prescription.total_cost || 0).toLocaleString()} VND</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Remove existing modal if any
        const existingModal = document.getElementById('medicinesModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalContent);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('medicinesModal'));
        modal.show();
    }

    function exportReport() {
        if (!reportData) return;

        const isFiltered = selectedMonth ? true : false;
        const monthNames = [
            '', 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        const fileName = isFiltered ?
            `prescription-report-${monthNames[selectedMonth]}-${selectedYear}.csv` :
            `prescription-report-${selectedYear}.csv`;

        // Create CSV content
        let csvContent = "Date,Patient ID,Doctor ID,Prescription ID,Medicines Count,Total Cost,Status\n";

        reportData.data.forEach(record => {
            const medicalRecord = record.medical_record || {};
            const prescription = record.prescription || {};
            const medicines = prescription.medicines || [];
            const totalCost = prescription.total_cost || 0;

            csvContent += `"${medicalRecord.NgayKham || ''}",`;
            csvContent += `"${medicalRecord.MaHSBA || ''}",`;
            csvContent += `"${medicalRecord.BacSi || ''}",`;
            csvContent += `"${prescription.MaToaThuoc || ''}",`;
            csvContent += `"${medicines.length}",`;
            csvContent += `"${totalCost}",`;
            csvContent += `"${prescription.TrangThaiToaThuoc || ''}"\n`;
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
        console.log('Prescription Report page loaded with month filtering');
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

    #monthlyChart {
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
$title = 'Prescription Report - Admin Panel';
include __DIR__ . '/../layouts/main.php';
?>