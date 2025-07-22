<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../controllers/BaseController.php';

class AppointmentController extends BaseController
{
    private $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
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

    private function patientAppointments()
    {
        $user = $_SESSION['user'];
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
            'appointments' => $appointments[0],
            'upcomingAppointments' => $upcomingAppointments
        ]);
    }

    public function book()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user || $user['user_role'] !== 'patient') {
            $this->redirect('appointments');
            return;
        }

        $patientId = $this->extractPatientId($user['user_id']);

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
            $doctors = $doctorsResponse['data'] ?? [];
        }

        $this->render('appointments/book', [
            'user' => $user,
            'doctors' => $doctors,
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

        if ($appointmentDate < $today) {
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

        // Prepare appointment data
        $appointmentData = [
            'MaBenhNhan' => $patientId,
            'MaBacSi' => $doctorId,
            'Ngay' => $date,
            'Gio' => $time . ':00', // Add seconds
            'LyDo' => $reason,
            'TrangThai' => 'scheduled'
        ];

        // Create appointment
        $response = $this->appointmentModel->createAppointment($appointmentData);

        if ($response['status'] === 200 || $response['status'] === 201) {
            $_SESSION['success'] = 'Appointment booked successfully!';
            $this->redirect('appointments');
        } else {
            $errorMessage = $response['data']['detail'] ?? 'Failed to book appointment. Please try again.';
            $_SESSION['error'] = $errorMessage;
            $this->redirect('appointments/book');
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

    public function cancel($appointmentId)
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user || $user['user_role'] !== 'patient') {
            $this->redirect('appointments');
            return;
        }

        $response = $this->appointmentModel->cancelAppointment($appointmentId);

        if ($response['status'] === 200) {
            $_SESSION['success'] = 'Appointment cancelled successfully.';
        } else {
            $_SESSION['error'] = 'Failed to cancel appointment.';
        }

        $this->redirect('appointments');
    }

    private function doctorAppointments()
    {
        // TODO: Implement doctor view
        $this->view('appointments/doctor', [
            'user' => $_SESSION['user'],
            'message' => 'Doctor appointments view coming soon!'
        ]);
    }

    private function adminAppointments()
    {
        // TODO: Implement admin view
        $response = $this->appointmentModel->getAllAppointments();
        $appointments = [];

        if ($response['status'] === 200) {
            $appointments = $response['data'] ?? [];
        }

        $this->view('appointments/admin', [
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
}
