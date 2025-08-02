<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Appointment.php';

class MedicalRecordController extends BaseController
{

    public function doctorRecentMedicalRecord()
    {
        $this->requireLogin();

        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $staffModel = new Staff();
        $medicalRecordModel = new MedicalRecord();

        $doctorId = $staffModel->getStaffById($user['user_id'])['data'][0]['MaNhanVien'];
        $medicalRecords = $medicalRecordModel->getMedicalRecordsByDoctorId($doctorId);
        if ($medicalRecords['status'] !== 200) {
            $this->render('medicalRecords/doctorRecent', [
                'medicalRecords' => []
            ]);
            return;
        }

        $this->render('medicalRecords/doctorRecent', [
            'medicalRecords' => $medicalRecords['data'][0]['MedicalRecord']
        ]);
    }

    private function patientMedicalRecords($user)
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
        $this->render('medicalRecords/patient', [
            'user' => $user,
            'patient' => $patientData,
            'appointments' => $appointments,
            'upcomingAppointments' => $upcomingAppointments,
            'appointmentHistory' => $appointmentHistory,
            'medicalHistory' => $medicalHistory
        ]);
    }

    private function doctorViewMedicalHistory($patientId)
    {
        // Get patient data from the API
        $patientModel = new Patient();
        $patientData = $patientModel->getPatientById($patientId);

        $medicalHistory = [];

        if ($patientData && $patientData['status'] === 200 && isset($patientData['data'][0])) {
            $patientId = $patientData['data'][0]['id'];

            // Use the MedicalRecord model to get medical history
            $medicalRecordModel = new MedicalRecord();
            $medicalHistory = $medicalRecordModel->getMedicalHistory($patientId);
        }

        // Render the patient dashboard with all data
        $this->render('medicalRecords/doctor', [
            'patient' => $patientData,
            'medicalHistory' => $medicalHistory
        ]);
    }

    public function viewDetail($MaGiayKhamBenh)
    {
        $medicalRecordModel = new MedicalRecord();
        $labServiceModel = new Lab();
        $prescriptionModel = new Prescription();

        $medicalRecord = $medicalRecordModel->getMedicalRecordById($MaGiayKhamBenh);
        $labServices = $labServiceModel->getUsedServicesByMedicalRecord($MaGiayKhamBenh);
        $prescription = $prescriptionModel->getPrescriptionsByMedicalRecord($MaGiayKhamBenh);

        $this->render('medicalRecords/detail', [
            'medicalRecord' => $medicalRecord['data'][0],
            'labServices' => $labServices['data'][0],
            'prescription' => $prescription['data'][0]
        ]);
    }

    public function index($patientId = null)
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
                $this->patientMedicalRecords($user);
                break;
            case 'doctor':
                // TODO: Implement doctor dashboard
                $this->doctorViewMedicalHistory($patientId);
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
