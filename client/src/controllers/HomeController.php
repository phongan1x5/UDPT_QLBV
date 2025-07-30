<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Lab.php';

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
                $this->render('dashboard', ['user' => $user]);
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
