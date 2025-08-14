<?php
require_once __DIR__ . '/../../helper/url_parsing.php';
ob_start();
// echo '<pre>';
// print_r($doctors[0]);
// print_r("patientId: " . $patientId);
// echo '</pre>';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-calendar-plus"></i> Book New Appointment</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/appointments">Appointments</a></li>
                        <li class="breadcrumb-item active">Book</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Appointment Details: Booking for Patient:
                        <?php echo htmlspecialchars("BN" . $patientId); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo url('appointments/book'); ?>" id="bookingForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="doctor_id" class="form-label">
                                    <i class="fas fa-user-md"></i> Select Doctor *
                                </label>
                                <select class="form-select" id="doctor_id" name="doctor_id" required>
                                    <option value="">Choose a doctor...</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?php echo $doctor['MaNhanVien']; ?>">
                                            Dr. <?php echo htmlspecialchars($doctor['HoTen']); ?>
                                            <?php if (isset($doctor['ChuyenKhoa'])): ?>
                                                - <?php echo htmlspecialchars($doctor['ChuyenKhoa']); ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="appointment_date" class="form-label">
                                    <i class="fas fa-calendar"></i> Appointment Date *
                                </label>
                                <input type="date" class="form-control" id="appointment_date"
                                    name="appointment_date" required
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                <small class="form-text text-muted">
                                    Appointments available Monday to Friday only
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointment_time" class="form-label">
                                    <i class="fas fa-clock"></i> Appointment Time *
                                </label>
                                <select class="form-select" id="appointment_time" name="appointment_time" required disabled>
                                    <option value="">Select date and doctor first</option>
                                </select>
                                <div id="time-loading" class="d-none">
                                    <small class="text-muted">
                                        <i class="fas fa-spinner fa-spin"></i> Loading available times...
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reason" class="form-label">
                                    <i class="fas fa-comment"></i> Reason for Visit
                                </label>
                                <select class="form-select" id="reason" name="reason" required disabled>
                                    <option value="">Select reason</option>
                                    <option value="KhamMoi">Khám mới</option>
                                    <option value="TaiKham">Tái khám</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Important Information:</h6>
                                    <ul class="mb-0">
                                        <li>Appointments are available Monday to Friday only</li>
                                        <li>Available time slots: 7:30 AM - 11:15 AM and 1:30 PM - 5:15 PM</li>
                                        <li>Please arrive 15 minutes before your scheduled time</li>
                                        <li>Cancellations must be made at least 24 hours in advance</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/appointments" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Appointments
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-calendar-check"></i> Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const doctorSelect = document.getElementById('doctor_id');
        const dateInput = document.getElementById('appointment_date');
        const timeSelect = document.getElementById('appointment_time');
        const reasonSelect = document.getElementById('reason');
        const timeLoading = document.getElementById('time-loading');
        const submitBtn = document.getElementById('submitBtn');

        // Get the base URL from PHP
        const baseUrl = '<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>';

        // Disable weekends
        dateInput.addEventListener('input', function() {
            const selectedDate = new Date(this.value);
            const dayOfWeek = selectedDate.getDay();

            if (dayOfWeek === 0 || dayOfWeek === 6) { // Sunday = 0, Saturday = 6
                alert('Appointments are only available Monday to Friday. Please select a weekday.');
                this.value = '';
                return;
            }

            loadAvailableSlots();
        });

        doctorSelect.addEventListener('change', loadAvailableSlots);

        function loadAvailableSlots() {
            const doctorId = doctorSelect.value;
            const date = dateInput.value;

            if (!doctorId || !date) {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Select date and doctor first</option>';
                reasonSelect.disabled = true; // Disable reason until doctor and date are selected
                return;
            }

            // Enable reason dropdown when doctor and date are selected
            reasonSelect.disabled = false;

            timeLoading.classList.remove('d-none');
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Loading...</option>';

            const apiUrl = `${baseUrl}/appointments/available-slots?doctor_id=${doctorId}&date=${date}`;
            console.log('Fetching URL:', apiUrl);

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
                    console.log('Raw API Response:', data);
                    timeLoading.classList.add('d-none');

                    // Handle the double-wrapped response format
                    let responseData = data;

                    // Check if it's wrapped in {"status":200,"data":[{actual_data}, 200]}
                    if (data.status === 200 && Array.isArray(data.data) && data.data.length >= 1) {
                        responseData = data.data[0]; // Extract the first element which contains the actual data
                    }

                    console.log('Processed responseData:', responseData);

                    if (responseData && responseData.status === 200 && responseData.data && responseData.data.length > 0) {
                        timeSelect.innerHTML = '<option value="">Select a time slot</option>';
                        responseData.data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.time;
                            option.textContent = slot.display_time;
                            timeSelect.appendChild(option);
                        });
                        timeSelect.disabled = false;

                        // Show availability info
                        showAvailabilityInfo(responseData.total_available, responseData.total_booked);
                    } else if (responseData && responseData.status === 400) {
                        // Handle validation errors (weekends, past dates, etc.)
                        timeSelect.innerHTML = '<option value="">Invalid date selected</option>';
                        timeSelect.disabled = true;
                        showError(responseData.detail || 'Invalid date selected');
                    } else {
                        // No available slots or other error
                        timeSelect.innerHTML = '<option value="">No available slots</option>';
                        timeSelect.disabled = true;
                        showAvailabilityInfo(0, responseData?.total_booked || 0);
                    }
                })
                .catch(error => {
                    console.error('Error loading time slots:', error);
                    timeLoading.classList.add('d-none');
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                    timeSelect.disabled = true;
                    showError('Failed to load available time slots. Please try again.');
                });
        }

        function showAvailabilityInfo(available, booked) {
            let infoDiv = document.getElementById('availability-info');
            if (!infoDiv) {
                infoDiv = document.createElement('div');
                infoDiv.id = 'availability-info';
                infoDiv.className = 'mt-2';
                timeSelect.parentNode.appendChild(infoDiv);
            }

            const total = available + booked;
            const availabilityPercentage = total > 0 ? Math.round((available / total) * 100) : 0;

            infoDiv.innerHTML = `
                <div class="row text-center">
                    <div class="col-4">
                        <small class="text-success">
                            <i class="fas fa-check-circle"></i><br>
                            ${available} Available
                        </small>
                    </div>
                    <div class="col-4">
                        <small class="text-danger">
                            <i class="fas fa-times-circle"></i><br>
                            ${booked} Booked
                        </small>
                    </div>
                    <div class="col-4">
                        <small class="text-info">
                            <i class="fas fa-percentage"></i><br>
                            ${availabilityPercentage}% Available
                        </small>
                    </div>
                </div>
            `;
        }

        function showError(message) {
            let infoDiv = document.getElementById('availability-info');
            if (!infoDiv) {
                infoDiv = document.createElement('div');
                infoDiv.id = 'availability-info';
                infoDiv.className = 'mt-2';
                timeSelect.parentNode.appendChild(infoDiv);
            }

            infoDiv.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        // Form submission validation
        const bookingForm = document.getElementById('bookingForm');
        bookingForm.addEventListener('submit', function(e) {
            const doctorId = doctorSelect.value;
            const date = dateInput.value;
            const time = timeSelect.value;
            const reason = reasonSelect.value;

            if (!doctorId || !date || !time || !reason) {
                e.preventDefault();
                alert('Please fill in all required fields: Doctor, Date, Time, and Reason.');
                return false;
            }

            // Double-check if it's a weekday
            const selectedDate = new Date(date);
            const dayOfWeek = selectedDate.getDay();

            if (dayOfWeek === 0 || dayOfWeek === 6) {
                e.preventDefault();
                alert('Appointments are only available Monday to Friday.');
                return false;
            }

            // Check if date is in the future
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);

            if (selectedDate <= today) {
                e.preventDefault();
                alert('Please select a future date.');
                return false;
            }

            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';

            return true;
        });

        // Reset form state if user navigates back
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-calendar-check"></i> Book Appointment';
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
$title = 'Book Appointment - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>