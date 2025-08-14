<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/MedicalRecord.php'; // Add this line
require_once __DIR__ . '/../controllers/BaseController.php';

class AppointmentController extends BaseController
{
    private $appointmentModel;
    private $medicalRecordModel; // Add this line

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
        $this->medicalRecordModel = new MedicalRecord(); // Add this line
    }

    public function index()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Route based on user role
        switch ($user['user_role']) {
            case 'patient':
                $this->patientAppointments();
                break;
            case 'doctor':
                $this->doctorAppointments();
                break;
            case 'admin':
            case 'staff':
                $this->adminAppointments();
                break;
            default:
                $this->redirect('dashboard');
        }
    }

    private function patientAppointments($patientId = "")
    {
        $user = $_SESSION['user'];

        //Meaning that this is a patient
        if ($patientId == "") $patientId = $this->extractPatientId($user['user_id']);
        $patientId = $this->extractPatientId($user['user_id']);

        if (!$patientId) {
            $this->redirect('dashboard');
            return;
        }

        // Get patient's appointments
        $appointmentsResponse = $this->appointmentModel->getPatientAppointments($patientId);
        $upcomingResponse = $this->appointmentModel->getPatientUpcomingAppointments($patientId);

        $appointments = [];
        $upcomingAppointments = [];

        if ($appointmentsResponse['status'] === 200) {
            $appointments = $appointmentsResponse['data'] ?? [];
        }

        if ($upcomingResponse['status'] === 200) {
            $upcomingAppointments = $upcomingResponse['data'] ?? [];
        }

        $this->render('appointments/patient', [
            'user' => $user,
            'appointments' => $appointments[0] ?? [],
            'upcomingAppointments' => $upcomingAppointments
        ]);
    }

    public function book($patientId = "")
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('appointments');
            return;
        }

        //Meaning that patient book for themselves.
        if ($patientId == "") $patientId = $this->extractPatientId($user['user_id']);

        if (!$patientId) {
            $this->redirect('dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleBookingSubmission($patientId);
            return;
        }

        // Get list of doctors
        $doctorsResponse = $this->appointmentModel->getAllDoctors();
        $doctors = [];

        if ($doctorsResponse['status'] === 200) {
            // Fix: Remove the [0] index - the data should be the array directly
            $doctorsData = $doctorsResponse['data'] ?? [];

            // Handle double-wrapped response
            if (is_array($doctorsData) && isset($doctorsData[0]) && is_array($doctorsData[0])) {
                $doctors = $doctorsData[0]; // Extract from double-wrapped format
            } else {
                $doctors = $doctorsData;
            }
        }

        $this->render('appointments/book', [
            'user' => $user,
            'doctors' => $doctors, // Remove the [0] index
            'patientId' => $patientId
        ]);
    }

    private function handleBookingSubmission($patientId)
    {
        $doctorId = $_POST['doctor_id'] ?? null;
        $date = $_POST['appointment_date'] ?? null;
        $time = $_POST['appointment_time'] ?? null;
        $reason = $_POST['reason'] ?? '';

        // Validate inputs
        if (!$doctorId || !$date || !$time) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            $this->redirect('appointments/book');
            return;
        }

        // Validate date (must be future date)
        $appointmentDate = new DateTime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($appointmentDate <= $today) {
            $_SESSION['error'] = 'Appointment date must be in the future.';
            $this->redirect('appointments/book');
            return;
        }

        // Check if it's a weekday
        $weekday = $appointmentDate->format('N'); // 1 (Monday) to 7 (Sunday)
        if ($weekday > 5) {
            $_SESSION['error'] = 'Appointments can only be booked Monday to Friday.';
            $this->redirect('appointments/book');
            return;
        }

        // Check if the time slot is still available
        $availabilityResponse = $this->appointmentModel->getAvailableSlots($doctorId, $date);
        if ($availabilityResponse['status'] === 200) {
            $availableSlots = $availabilityResponse['data'][0]['data'] ?? [];
            $timeAvailable = false;

            foreach ($availableSlots as $slot) {
                if ($slot['time'] === $time) {
                    $timeAvailable = true;
                    break;
                }
            }

            if (!$timeAvailable) {
                $_SESSION['error'] = 'Selected time slot is no longer available. Please choose another time.';
                $this->redirect('appointments/book');
                return;
            }
        }

        // Prepare appointment data with correct status enum
        $appointmentData = [
            'MaBenhNhan' => intval($patientId),
            'MaBacSi' => intval($doctorId),
            'Ngay' => $date,
            'Gio' => $time . ':00', // Add seconds
            'LoaiLichHen' => $reason ?: 'Khám tổng quát', // Set default reason
            'TrangThai' => 'ChoXacNhan' // Use correct Vietnamese enum value
        ];

        // Create appointment
        $response = $this->appointmentModel->createAppointment($appointmentData);

        if ($response['status'] === 200 || $response['status'] === 201) {
            // Get the created appointment ID
            $appointmentId = null;
            if (isset($response['data']['MaLichHen'])) {
                $appointmentId = $response['data']['MaLichHen'];
            } elseif (isset($response['data'][0]['MaLichHen'])) {
                $appointmentId = $response['data'][0]['MaLichHen'];
            }

            // Create medical record if appointment was created successfully
            if ($appointmentId) {
                $medicalRecordResult = $this->createMedicalRecord($appointmentId, $patientId, $doctorId, $date);

                if ($medicalRecordResult['success']) {
                    $_SESSION['success'] = 'Appointment booked successfully and medical record created! Please wait for confirmation.';
                } else {
                    // Appointment created but medical record failed
                    $_SESSION['success'] = 'Appointment booked successfully! Medical record will be created during consultation.';
                    error_log("Failed to create medical record for appointment {$appointmentId}: " . $medicalRecordResult['message']);
                }
            } else {
                $_SESSION['success'] = 'Appointment booked successfully! Please wait for confirmation.';
                error_log("Could not extract appointment ID from response");
            }

            $this->redirect('appointments');
        } else {
            // Handle error response
            $errorMessage = 'Failed to book appointment. Please try again.';

            if (isset($response['data']) && is_array($response['data'])) {
                if (isset($response['data'][0]['detail'])) {
                    $errorMessage = $response['data'][0]['detail'];
                } elseif (isset($response['data']['detail'])) {
                    $errorMessage = $response['data']['detail'];
                }
            }

            $_SESSION['error'] = $errorMessage;
            $this->redirect('appointments/book');
        }
    }

    /**
     * Create a medical record for the appointment
     */
    private function createMedicalRecord($appointmentId, $patientId, $doctorId, $appointmentDate)
    {
        try {
            // First, get or verify the patient's medical profile (HoSoBenhAn)
            $medicalProfileResponse = $this->medicalRecordModel->getMedicalProfileByPatient($patientId);

            $medicalProfileId = null;

            if ($medicalProfileResponse['status'] === 200 && isset($medicalProfileResponse['data']['MaHSBA'])) {
                $medicalProfileId = $medicalProfileResponse['data']['MaHSBA'];
            } else {
                // If no medical profile exists, we might need to create one
                // For now, we'll use the patient ID as a fallback
                $medicalProfileId = $patientId;
                error_log("Using patient ID {$patientId} as medical profile ID fallback");
            }

            // Prepare medical record data
            $medicalRecordData = [
                'MaHSBA' => $medicalProfileId,
                'BacSi' => intval($doctorId),
                'MaLichHen' => intval($appointmentId),
                'NgayKham' => $appointmentDate,
                'ChanDoan' => '@Pending examination', // Initial placeholder
                'LuuY' => "Medical record created for appointment #{$appointmentId} on " . date('Y-m-d H:i:s')
            ];

            // Create the medical record
            $response = $this->medicalRecordModel->createMedicalRecord($medicalRecordData);

            if ($response['status'] === 200 || $response['status'] === 201) {
                return [
                    'success' => true,
                    'message' => 'Medical record created successfully',
                    'data' => $response['data']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create medical record: ' . ($response['message'] ?? 'Unknown error'),
                    'data' => null
                ];
            }
        } catch (Exception $e) {
            error_log("Exception creating medical record: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getAvailableSlots()
    {
        $this->requireLogin();

        $doctorId = $_GET['doctor_id'] ?? null;
        $date = $_GET['date'] ?? null;

        if (!$doctorId || !$date) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing doctor_id or date']);
            return;
        }

        $response = $this->appointmentModel->getAvailableSlots($doctorId, $date);

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function cancelAppointment($appointmentId)
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (!$appointmentId) {
            header('Content-Type: application/json');
            echo json_encode(
                ['error' => 'Missing appointmentId']
            );
            return;
        }

        $appointmentModel = new Appointment();
        $newStatus = 'DaHuy';
        $response = $appointmentModel->updateAppointmentStatus($appointmentId, $newStatus);
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function doctorAppointments($doctorId = "")
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('appointments');
            return;
        }

        $appointmentModel = new Appointment();
        $doctorAppointments = $appointmentModel->getDoctorAppointments($doctorId);
        error_log(print_r($doctorAppointments, true));

        $this->render("appointments/doctorAppointments", [
            "appointments" => $doctorAppointments['data'][0]
        ]);
    }

    public function doctorConfirmAppointment($appointmentId)
    {
        $appointmentModel = new Appointment();
        $newStatus = 'DaXacNhan';
        $response = $appointmentModel->updateAppointmentStatus($appointmentId, $newStatus);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function adminAppointments()
    {
        // TODO: Implement admin view
        $response = $this->appointmentModel->getAllAppointments();
        $appointments = [];

        if ($response['status'] === 200) {
            $appointments = $response['data'] ?? [];
        }

        $this->render('appointments/admin', [
            'user' => $_SESSION['user'],
            'appointments' => $appointments
        ]);
    }

    private function extractPatientId($userId)
    {
        // Extract numeric part from IDs like "BN1", "BN100", etc.
        if (preg_match('/^BN(\d+)$/', $userId, $matches)) {
            return $matches[1]; // Returns just the number part
        }

        // If it's already numeric, return as is
        if (is_numeric($userId)) {
            return $userId;
        }

        // If no pattern matches, return null
        return null;
    }

    public function collectAppointmentFeesPanel($patientId)
    {
        $user = $_SESSION['user'];
        $appointmentModel = new Appointment();
        $appointments = $appointmentModel->getPatientVerifiedAppointments($patientId);
        $paid_appointments = $appointmentModel->getPatientPaidAppointments($patientId);
        // error_log(print_r($appointments), true);
        $this->render('appointments/collectAppointmentFeesPanel', [
            "user" => $user,
            "appointments" => $appointments['data'][0],
            "paid_appointments" => $paid_appointments['data'][0]
        ]);
    }

    public function staffCollectAppointmentMoney($appointmentId)
    {
        $appointmentModel = new Appointment();
        $newStatus = 'DaThuTien';
        $response = $appointmentModel->updateAppointmentStatus($appointmentId, $newStatus);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function doctorSchedule($doctorId = "")
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        if ($doctorId == "") {
            $doctorId = preg_replace('/^DR/', '', $user['user_id']);
        }

        $this->render("schedule/doctor", [
            "doctorId" => $doctorId
        ]);
    }
}
