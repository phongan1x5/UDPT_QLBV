<?php
ob_start();
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
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Appointment Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/appointments/book" id="bookingForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="doctor_id" class="form-label">
                                    <i class="fas fa-user-md"></i> Select Doctor *
                                </label>
                                <select class="form-select" id="doctor_id" name="doctor_id" required>
                                    <option value="">Choose a doctor...</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?php echo $doctor['MaBacSi']; ?>">
                                            Dr. <?php echo htmlspecialchars($doctor['TenBacSi']); ?>
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
                                <input type="text" class="form-control" id="reason" name="reason"
                                    placeholder="e.g., Regular checkup, Follow-up, etc.">
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
        const timeLoading = document.getElementById('time-loading');
        const submitBtn = document.getElementById('submitBtn');

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
                return;
            }

            timeLoading.classList.remove('d-none');
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/appointments/available-slots?doctor_id=${doctorId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    timeLoading.classList.add('d-none');

                    if (data.status === 200 && data.data && data.data.length > 0) {
                        timeSelect.innerHTML = '<option value="">Select a time slot</option>';
                        data.data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.time;
                            option.textContent = slot.display_time;
                            timeSelect.appendChild(option);
                        });
                        timeSelect.disabled = false;
                    } else {
                        timeSelect.innerHTML = '<option value="">No available slots</option>';
                        timeSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading time slots:', error);
                    timeLoading.classList.add('d-none');
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                    timeSelect.disabled = true;
                });
        }

        // Generate default time slots if API doesn't provide them
        function generateDefaultTimeSlots() {
            const slots = [];

            // Morning slots: 7:30 AM - 11:15 AM
            for (let hour = 7; hour <= 11; hour++) {
                for (let minute of [0, 15, 30, 45]) {
                    if (hour === 7 && minute < 30) continue;
                    if (hour === 11 && minute > 15) continue;

                    const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    const displayTime = formatTime(hour, minute);
                    slots.push({
                        time,
                        display_time: displayTime
                    });
                }
            }

            // Afternoon slots: 1:30 PM - 5:15 PM
            for (let hour = 13; hour <= 17; hour++) {
                for (let minute of [0, 15, 30, 45]) {
                    if (hour === 13 && minute < 30) continue;
                    if (hour === 17 && minute > 15) continue;

                    const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    const displayTime = formatTime(hour, minute);
                    slots.push({
                        time,
                        display_time: displayTime
                    });
                }
            }

            return slots;
        }

        function formatTime(hour, minute) {
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
            return `${displayHour}:${minute.toString().padStart(2, '0')} ${ampm}`;
        }
    });
</script>

<?php
$content = ob_get_clean();
$title = 'Book Appointment - Hospital Management System';
include __DIR__ . '/../layouts/main.php';
?>