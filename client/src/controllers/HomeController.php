<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/MedicalRecord.php';

class HomeController extends BaseController
{
    private function patientDashboard($user)
    {
        // Get patient data from the API
        $patientModel = new Patient();
        $patientData = $patientModel->getCurrentPatient();

        // Initialize data arrays
        $appointments = [];
        $upcomingAppointments = [];
        $appointmentHistory = [];
        $medicalHistory = [];

        if ($patientData && $patientData['status'] === 200 && isset($patientData['data'][0])) {
            $patientId = $patientData['data'][0]['id'];

            // Use the Appointment model methods
            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->getPatientAppointments($patientId);
            $upcomingAppointments = $appointmentModel->getPatientUpcomingAppointments($patientId);
            $appointmentHistory = $appointmentModel->getPatientAppointmentHistory($patientId, 0, 5);

            // Use the MedicalRecord model to get medical history
            $medicalRecordModel = new MedicalRecord();
            $medicalHistory = $medicalRecordModel->getMedicalHistory($patientId);
        }

        // Render the patient dashboard with all data
        $this->render('dashboard/patient', [
            'user' => $user,
            'patient' => $patientData,
            'appointments' => $appointments,
            'upcomingAppointments' => $upcomingAppointments,
            'appointmentHistory' => $appointmentHistory,
            'medicalHistory' => $medicalHistory
        ]);
    }

    private function doctorDashboard($user)
    {
        // Get staff data from the API
        $staffModel = new Staff();
        $staffData = $staffModel->getStaffById($user['user_id']);

        // Initialize data arrays
        $appointments = [];
        $upcomingAppointments = [];
        $appointmentHistory = [];
        $medicalHistory = [];

        if ($staffData && $staffData['status'] === 200 && isset($staffData['data'][0])) {
            $staffId = $staffData['data'][0]['MaNhanVien']; // This is the doctor's ID

            // Use the Appointment model methods for DOCTOR appointments
            $appointmentModel = new Appointment();

            // Fix: Use doctor-specific methods instead of patient methods
            $appointments = $appointmentModel->getDoctorAppointments($staffId);
            $upcomingAppointments = $appointmentModel->getDoctorUpcomingAppointments($staffId);
            $appointmentHistory = $appointmentModel->getDoctorAppointmentHistory($staffId, 0, 5);

            // Medical history is not relevant for doctor dashboard
            // Remove or modify this for doctor-specific medical records they've created
        }

        // Render the doctor dashboard with all data
        $this->render('dashboard/doctor', [
            'user' => $user,
            'doctor' => $staffData,
            'appointments' => $appointments,
            'upcomingAppointments' => $upcomingAppointments,
            'appointmentHistory' => $appointmentHistory,
            'medicalHistory' => $medicalHistory
        ]);
    }

    public function dashboard()
    {
        $this->requireLogin();

        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Handle different roles
        switch ($user['user_role']) {
            case 'patient':
                $this->patientDashboard($user);
                break;
            case 'doctor':
                // TODO: Implement doctor dashboard
                $this->doctorDashboard($user);
                break;
            case 'admin':
                // TODO: Implement admin dashboard
                $this->render('dashboard', ['user' => $user]);
                break;
            default:
                $this->render('dashboard', ['user' => $user]);
                break;
        }
    }
}
