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
                    <h1><i class="fas fa-calendar-alt"></i> My Schedule</h1>
                    <p class="text-muted mb-0">View and manage your daily appointments</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo url('doctor/dashboard'); ?>">Doctor</a></li>
                            <li class="breadcrumb-item active">My Schedule</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo url('doctor/dashboard'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day"></i> Select Date to View Schedule
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="schedule_date" class="form-label">
                                <i class="fas fa-calendar"></i> Select Date
                            </label>
                            <input type="date"
                                class="form-control form-control-lg"
                                id="schedule_date"
                                name="schedule_date"
                                value="<?php echo date('Y-m-d'); ?>"
                                min="<?php echo date('Y-m-d', strtotime('-30 days')); ?>"
                                max="<?php echo date('Y-m-d', strtotime('+90 days')); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary btn-lg" id="loadScheduleBtn">
                                <i class="fas fa-search"></i> Load Schedule
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" id="todayBtn">
                                <i class="fas fa-calendar-day"></i> Today
                            </button>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <div id="selectedDateInfo" class="text-muted">
                                    <strong>Today:</strong> <?php echo date('l, F j, Y'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                    <h5>Loading your schedule...</h5>
                    <p class="text-muted">Please wait while we fetch your appointments.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Display -->
    <div id="scheduleContainer">
        <!-- Schedule Summary Cards -->
        <div class="row mb-4" id="summaryCards">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                        <h4 id="totalAppointments">0</h4>
                        <small>Total Appointments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4 id="confirmedAppointments">0</h4>
                        <small>Confirmed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4 id="pendingAppointments">0</h4>
                        <small>Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-hourglass-half fa-2x mb-2"></i>
                        <h4 id="availableSlots">0</h4>
                        <small>Available Slots</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Schedule Grid -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clock"></i> Daily Schedule
                                <span id="scheduleDate" class="text-primary">Today</span>
                            </h5>
                            <div>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-circle text-success"></i> Available
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-circle text-secondary"></i> Completed
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-circle text-primary"></i> Confirmed
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-circle text-warning"></i> Pending
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-circle text-danger"></i> Cancelled
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Morning Session -->
                        <div class="border-bottom">
                            <div class="bg-light p-2">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-sun"></i> Morning Session (7:30 AM - 11:15 AM)
                                </h6>
                            </div>
                            <div class="p-2">
                                <div class="row g-1" id="morningSlots">
                                    <!-- Morning time slots will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Afternoon Session -->
                        <div>
                            <div class="bg-light p-2">
                                <h6 class="mb-0 text-info">
                                    <i class="fas fa-cloud-sun"></i> Afternoon Session (1:30 PM - 5:15 PM)
                                </h6>
                            </div>
                            <div class="p-2">
                                <div class="row g-1" id="afternoonSlots">
                                    <!-- Afternoon time slots will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <!-- <i class="fas fa-list"></i> Today's Appointments -->
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="appointmentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="appointmentsTableBody" class="d-none">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                                            Select a date to view appointments
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- No Appointments Message -->
    <div id="noAppointmentsMessage" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Appointments Scheduled</h4>
                    <p class="text-muted">You have no appointments for the selected date.</p>
                    <p class="text-muted">Enjoy your free time! 😊</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Detail Modal -->
<!-- <div class="modal fade" id="appointmentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check"></i> Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetailContent">
                <!-- Content will be loaded here -->
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="startConsultationBtn">
        <i class="fas fa-stethoscope"></i> Start Consultation
    </button>
</div>
</div>
</div>
</div> -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleDateInput = document.getElementById('schedule_date');
        const loadScheduleBtn = document.getElementById('loadScheduleBtn');
        const todayBtn = document.getElementById('todayBtn');
        const loadingState = document.getElementById('loadingState');
        const scheduleContainer = document.getElementById('scheduleContainer');
        const noAppointmentsMessage = document.getElementById('noAppointmentsMessage');
        const selectedDateInfo = document.getElementById('selectedDateInfo');

        // Time slots configuration
        const timeSlots = {
            morning: [
                '07:30', '07:45', '08:00', '08:15', '08:30', '08:45',
                '09:00', '09:15', '09:30', '09:45', '10:00', '10:15',
                '10:30', '10:45', '11:00', '11:15'
            ],
            afternoon: [
                '13:30', '13:45', '14:00', '14:15', '14:30', '14:45',
                '15:00', '15:15', '15:30', '15:45', '16:00', '16:15',
                '16:30', '16:45', '17:00', '17:15'
            ]
        };

        // Load schedule when date changes or button is clicked
        scheduleDateInput.addEventListener('change', updateDateInfo);
        loadScheduleBtn.addEventListener('click', loadSchedule);
        todayBtn.addEventListener('click', function() {
            scheduleDateInput.value = new Date().toISOString().split('T')[0];
            updateDateInfo();
            loadSchedule();
        });

        function updateDateInfo() {
            const selectedDate = new Date(scheduleDateInput.value);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            selectedDateInfo.innerHTML = `<strong>Selected:</strong> ${selectedDate.toLocaleDateString('en-US', options)}`;
            document.getElementById('scheduleDate').textContent = selectedDate.toLocaleDateString('en-US', options);
        }

        function loadSchedule() {
            const selectedDate = scheduleDateInput.value;
            if (!selectedDate) return;

            showLoading(true);

            // Get current user (doctor) ID from session or context
            const doctorId = '<?php echo $doctorId; ?>'; // Fallback to 1 for demo
            const apiUrl = `<?php echo url('/appointments/available-slots'); ?>?doctor_id=${doctorId}&date=${selectedDate}`;

            fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Schedule data:', data);
                    displaySchedule(data, selectedDate);
                })
                .catch(error => {
                    console.error('Error loading schedule:', error);
                    showError('Failed to load schedule. Please try again.');
                })
                .finally(() => {
                    showLoading(false);
                });
        }

        function displaySchedule(data, selectedDate) {
            // Process the data based on your API response format
            let availableSlots = [];
            let doctorInfo = {};

            console.log('Raw API data:', data);

            if (data && data.status === 200 && data.data && data.data.length > 0) {
                const scheduleData = data.data[0]; // Get the first item
                if (scheduleData.data && Array.isArray(scheduleData.data)) {
                    availableSlots = scheduleData.data;
                    doctorInfo = {
                        doctor_id: scheduleData.doctor_id,
                        date: scheduleData.date,
                        total_available: scheduleData.total_available,
                        total_booked: scheduleData.total_booked
                    };
                }
            }

            console.log('Available slots:', availableSlots);
            console.log('Doctor info:', doctorInfo);

            // Update summary cards with API data
            updateSummaryCardsWithAvailability(availableSlots, doctorInfo);

            // Display time slots - slots not in API are booked
            displayTimeSlotsWithAvailability(availableSlots, selectedDate);

            // Create mock appointments for booked slots (for table display)
            const bookedAppointments = createBookedAppointmentsList(availableSlots, selectedDate);
            displayAppointmentsTable(bookedAppointments);

            // Always show schedule container since we have data
            scheduleContainer.classList.remove('d-none');
            noAppointmentsMessage.classList.add('d-none');
        }

        function updateSummaryCardsWithAvailability(availableSlots, doctorInfo) {
            // Use API data if available
            const totalAvailable = doctorInfo.total_available || availableSlots.length;
            const totalBooked = doctorInfo.total_booked || 0;
            const totalSlots = totalAvailable + totalBooked;

            document.getElementById('totalAppointments').textContent = totalBooked;
            document.getElementById('confirmedAppointments').textContent = totalBooked; // Assume all booked are confirmed
            document.getElementById('pendingAppointments').textContent = 0; // No pending info in API
            document.getElementById('availableSlots').textContent = totalAvailable;
        }

        function displayTimeSlotsWithAvailability(availableSlots, selectedDate) {
            const morningContainer = document.getElementById('morningSlots');
            const afternoonContainer = document.getElementById('afternoonSlots');

            // Create available slots lookup by time
            const availableSlotsByTime = {};
            availableSlots.forEach(slot => {
                availableSlotsByTime[slot.time] = slot;
            });

            // Time slots configuration (all possible slots)
            const allTimeSlots = {
                morning: [
                    '07:30', '07:45', '08:00', '08:15', '08:30', '08:45',
                    '09:00', '09:15', '09:30', '09:45', '10:00', '10:15',
                    '10:30', '10:45', '11:00', '11:15'
                ],
                afternoon: [
                    '13:30', '13:45', '14:00', '14:15', '14:30', '14:45',
                    '15:00', '15:15', '15:30', '15:45', '16:00', '16:15',
                    '16:30', '16:45', '17:00', '17:15'
                ]
            };

            // Generate morning slots
            morningContainer.innerHTML = '';
            allTimeSlots.morning.forEach(time => {
                const isAvailable = availableSlotsByTime[time]; // If exists in API = available
                const slot = createTimeSlotBasedOnAvailability(time, isAvailable, selectedDate);
                morningContainer.appendChild(slot);
            });

            // Generate afternoon slots
            afternoonContainer.innerHTML = '';
            allTimeSlots.afternoon.forEach(time => {
                const isAvailable = availableSlotsByTime[time]; // If exists in API = available
                const slot = createTimeSlotBasedOnAvailability(time, isAvailable, selectedDate);
                afternoonContainer.appendChild(slot);
            });
        }

        function createTimeSlotBasedOnAvailability(time, availableSlotData, selectedDate) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3 col-xl-2 mb-2';

            let statusClass = 'secondary';
            let statusIcon = 'question';
            let statusText = 'Unknown';
            let clickAction = '';
            let displayTime = time;

            // Check if the slot is in the past
            const now = new Date();
            const slotDateTime = new Date(`${selectedDate} ${time}:00`);
            const isPast = slotDateTime < now;

            if (availableSlotData) {
                // Slot exists in API response = Available
                if (isPast) {
                    statusClass = 'secondary';
                    statusIcon = 'clock';
                    statusText = 'Past';
                } else {
                    statusClass = 'success';
                    statusIcon = 'check';
                    statusText = 'Available';
                }
                displayTime = availableSlotData.display_time || time;
            } else {
                // Slot does NOT exist in API response = Booked
                statusClass = 'primary';
                statusIcon = 'user';
                statusText = 'Booked';
                clickAction = `onclick="showBookedSlotDetail('${time}', '${selectedDate}')"`;

                if (isPast) {
                    statusText = 'Past (Booked)';
                    statusClass = 'info';
                }
            }

            col.innerHTML = `
                <div class="card border-${statusClass} ${!availableSlotData ? 'appointment-slot' : ''} ${isPast ? 'past-slot' : ''}" 
                     style="cursor: ${!availableSlotData && !isPast ? 'pointer' : 'default'};" ${clickAction}>
                    <div class="card-body p-2 text-center">
                        <i class="fas fa-${statusIcon} text-${statusClass} mb-1"></i>
                        <div class="fw-bold">${time}</div>
                        <small class="text-${statusClass}">${statusText}</small>
                        ${availableSlotData && displayTime !== time ? `<br><small class="text-muted">${displayTime}</small>` : ''}
                    </div>
                </div>
            `;

            return col;
        }

        function createBookedAppointmentsList(availableSlots, selectedDate) {
            // All possible time slots
            const allTimeSlots = [
                '07:30', '07:45', '08:00', '08:15', '08:30', '08:45',
                '09:00', '09:15', '09:30', '09:45', '10:00', '10:15',
                '10:30', '10:45', '11:00', '11:15',
                '13:30', '13:45', '14:00', '14:15', '14:30', '14:45',
                '15:00', '15:15', '15:30', '15:45', '16:00', '16:15',
                '16:30', '16:45', '17:00', '17:15'
            ];

            // Get available slots times
            const availableTimes = availableSlots.map(slot => slot.time);

            // Find booked slots (slots that are NOT in available slots)
            const bookedTimes = allTimeSlots.filter(time => !availableTimes.includes(time));

            // Create mock appointment objects for booked slots
            const bookedAppointments = bookedTimes.map((time, index) => ({
                MaLichHen: `booked-${time.replace(':', '')}-${selectedDate.replace(/-/g, '')}`,
                MaBenhNhan: 'Unknown', // We don't have patient info
                Gio: time + ':00',
                TrangThai: 'DaXacNhan', // Assume booked = confirmed
                LoaiLichHen: 'Unknown' // We don't have appointment type info
            }));

            return bookedAppointments;
        }

        function showBookedSlotDetail(time, date) {
            console.log('Show booked slot detail for:', time, date);

            const modalContent = document.getElementById('appointmentDetailContent');
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5>Booked Time Slot</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Appointment Information</h6>
                        <p><strong>Date:</strong> ${new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        <p><strong>Time:</strong> ${time}</p>
                        <p><strong>Status:</strong> <span class="badge bg-primary">Booked</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Patient Information</h6>
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            This time slot is booked but patient details are not available in the current API response.
                        </p>
                        <p class="text-muted">
                            Please check your appointment management system for complete details.
                        </p>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Note:</strong> This slot was determined to be booked because it's missing from the available slots API response.
                    For detailed appointment information, you may need to integrate with a comprehensive appointments API.
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
            modal.show();
        }

        function displayAppointmentsTable(appointments) {
            const tableBody = document.getElementById('appointmentsTableBody');

            if (appointments.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-calendar-check fa-2x mb-2 text-success"></i><br>
                            <h6 class="text-success">All slots are available!</h6>
                            <p class="mb-0">No appointments booked for this date.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            // Sort appointments by time
            appointments.sort((a, b) => {
                return a.Gio ? a.Gio.localeCompare(b.Gio) : 0;
            });

            tableBody.innerHTML = appointments.map(apt => {
                const statusBadge = getStatusBadge(apt.TrangThai);
                const typeBadge = getTypeBadge(apt.LoaiLichHen);

                return `
                    <tr onclick="showAppointmentDetail('${apt.MaLichHen}')" style="cursor: pointer;">
                        <td><strong>${apt.Gio ? apt.Gio.substring(0, 5) : 'N/A'}</strong></td>
                        <td>
                            <div>
                                <strong>${apt.MaBenhNhan !== 'Unknown' ? 'BN' + apt.MaBenhNhan : 'Patient Info N/A'}</strong>
                                <br><small class="text-muted">${apt.MaBenhNhan !== 'Unknown' ? 'Patient ID: ' + apt.MaBenhNhan : 'Details not available'}</small>
                            </div>
                        </td>
                        <td>${typeBadge}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); showAppointmentDetail('${apt.MaLichHen}')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function getStatusBadge(status) {
            const badges = {
                'DaXacNhan': '<span class="badge bg-success">Confirmed</span>',
                'DaThuTien': '<span class="badge bg-warning">Fees collected</span>',
                'ChoXacNhan': '<span class="badge bg-warning">Pending</span>',
                'DaHuy': '<span class="badge bg-danger">Cancelled</span>',
                'DaKham': '<span class="badge bg-info">Completed</span>',
                'Unknown': '<span class="badge bg-secondary">Unknown</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function getTypeBadge(type) {
            const badges = {
                'KhamMoi': '<span class="badge bg-primary">New Exam</span>',
                'TaiKham': '<span class="badge bg-info">Follow-up</span>',
                'KhanCap': '<span class="badge bg-danger">Emergency</span>',
                'Unknown': '<span class="badge bg-light text-dark">Unknown</span>'
            };
            return badges[type] || `<span class="badge bg-secondary">${type}</span>`;
        }

        function showAppointmentDetail(appointmentId) {
            console.log('Show appointment detail for:', appointmentId);

            // Mock appointment detail since we don't have full data
            const modalContent = document.getElementById('appointmentDetailContent');
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5>Appointment Details</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Appointment Information</h6>
                        <p><strong>ID:</strong> ${appointmentId}</p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Booked</span></p>
                        <p><strong>Type:</strong> <span class="badge bg-primary">Appointment</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Patient Information</h6>
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Patient details not available in current API response.
                        </p>
                        <p class="text-muted">
                            Please check your appointment management system for complete details.
                        </p>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Note:</strong> This is a booked time slot. For detailed patient and appointment information, 
                    you may need to integrate with a more comprehensive appointments API.
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
            modal.show();
        }

        function showLoading(show) {
            if (show) {
                loadingState.classList.remove('d-none');
                scheduleContainer.classList.add('d-none');
                noAppointmentsMessage.classList.add('d-none');
            } else {
                loadingState.classList.add('d-none');
            }
        }

        function showError(message) {
            // TODO: Implement error display
            alert(message);
        }

        // Make functions global for onclick handlers
        window.showAppointmentDetail = showAppointmentDetail;
        window.showBookedSlotDetail = showBookedSlotDetail;

        // Initialize with today's date
        updateDateInfo();
        loadSchedule();
    });
</script>

<style>
    .appointment-slot {
        transition: all 0.3s ease;
        border-width: 2px !important;
    }

    .appointment-slot:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .past-slot {
        opacity: 0.6;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .col-xl-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media print {

        .btn,
        .modal,
        .no-print {
            display: none !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Doctor Schedule - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>