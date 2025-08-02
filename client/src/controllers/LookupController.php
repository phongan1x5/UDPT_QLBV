<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Lab.php';


class LookupController extends BaseController
{
    //User is a docter, userId = docterId
    public function lookupPatientMedicalHistory()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $patientID = $_POST['id'] ?? null;
            $patientModel = new Patient();
            $patient = $patientModel->getPatientById($patientID);
            if ($patient['status'] !== 200) {
                $_SESSION['lookup_error'] = 'Wrong patient id';
                $this->redirect('lookup/patientMedicalHistory');
            }
            $this->redirect('medicalRecords/' . $patientID);
            return;
        }

        $this->render('lookup/medicalHistory');
    }

    private function viewPatientMedicalHistory($patientId)
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
}
