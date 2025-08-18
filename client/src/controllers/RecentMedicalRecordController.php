<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Lab.php';


class RecentMedicalRecord extends BaseController
{
    public function index()
    {
        $this->requireLogin();
        $this->check_doctor();

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

        $this->render('medicalRecords/doctorRecent', [
            'medicalRecords' => $medicalRecords
        ]);
    }
}
