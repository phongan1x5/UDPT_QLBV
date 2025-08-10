<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Patient.php';

class PrescriptionController extends BaseController
{
    private $prescriptionModel;
    private $medicalRecordModel;

    public function __construct()
    {
        $this->prescriptionModel = new Prescription();
        $this->medicalRecordModel = new MedicalRecord();
    }

    public function index()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Handle different roles
        switch ($user['user_role']) {
            case 'patient':
                $this->patientPrescriptions($user);
                break;
            case 'doctor':
                $this->doctorPrescriptions($user);
                break;
            case 'admin':
                $this->adminPrescriptions($user);
                break;
            default:
                $this->redirect('dashboard');
        }
    }

    private function extractPatientId($userId)
    {
        // Extract numeric part from IDs like "BN1", "BN100", etc.
        // Remove "BN" prefix and return the number
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

    private function patientPrescriptions($user)
    {
        $patientId = $this->extractPatientId($user['user_id']);

        // Initialize arrays
        $patientPrescriptions = [];
        $medicalHistory = null;

        // Get patient's medical history
        if ($patientId) {
            $medicalHistory = $this->medicalRecordModel->getMedicalHistory($patientId);
        }

        // Check if we have valid medical history data
        if (
            $medicalHistory &&
            isset($medicalHistory['status']) &&
            $medicalHistory['status'] === 200 &&
            isset($medicalHistory['data']) &&
            is_array($medicalHistory['data']) &&
            count($medicalHistory['data']) > 0 &&
            isset($medicalHistory['data'][0]['MedicalRecords'])
        ) {

            foreach ($medicalHistory['data'][0]['MedicalRecords'] as $record) {
                // Get prescription for this medical record (single prescription per medical record)
                $prescriptionResponse = $this->prescriptionModel->getPrescriptionsByMedicalRecord($record['MaGiayKhamBenh']);

                // Check if prescription response is valid
                if (
                    $prescriptionResponse &&
                    isset($prescriptionResponse['status'])
                ) {

                    if (
                        $prescriptionResponse['status'] === 200 &&
                        isset($prescriptionResponse['data']) &&
                        is_array($prescriptionResponse['data'])
                    ) {

                        // Since there's only one prescription per medical record, add it directly
                        $prescriptionWithRecord = [
                            'prescription' => $prescriptionResponse['data'],
                            'MedicalRecord' => $record
                        ];
                        $patientPrescriptions[] = $prescriptionWithRecord;
                    } elseif ($prescriptionResponse['status'] === 404) {
                        // No prescription found for this medical record - this is okay
                        // Some medical records might not have prescriptions
                        continue;
                    }
                }
            }
        }

        // Sort prescriptions by most recent first (only if we have prescriptions)
        if (!empty($patientPrescriptions)) {
            usort($patientPrescriptions, function ($a, $b) {
                // error_log("Value of a: " . print_r($a, true));
                $aId = isset($a['prescription'][0]['MaToaThuoc']) ? $a['prescription'][0]['MaToaThuoc'] : 0;
                $bId = isset($b['prescription'][0]['MaToaThuoc']) ? $b['prescription'][0]['MaToaThuoc'] : 0;
                // error_log("Sorting: aId = $aId, bId = $bId");

                return $bId - $aId;
            });
        }

        // Get all medicines for reference
        $medicines = $this->prescriptionModel->getAllMedicines();

        $this->render('prescriptions/patient', [
            'user' => $user,
            'prescriptions' => $patientPrescriptions,
            'medicines' => $medicines,
            'medicalHistory' => $medicalHistory,
            'patientId' => $patientId
        ]);
    }

    private function doctorPrescriptions($user)
    {
        // TODO: Implement doctor prescriptions view
        $this->render('prescriptions/doctor', [
            'user' => $user,
            'message' => 'Doctor prescriptions view coming soon!'
        ]);
    }

    private function adminPrescriptions($user)
    {
        // Get all prescriptions for admin
        $allPrescriptions = $this->prescriptionModel->getAllPrescriptions();
        $allMedicines = $this->prescriptionModel->getAllMedicines();

        $this->render('prescriptions/admin', [
            'user' => $user,
            'prescriptions' => $allPrescriptions,
            'medicines' => $allMedicines
        ]);
    }

    public function viewDetailPrescription($prescriptionId)
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $prescriptionModel = new Prescription();
        $detailPrescriptionData = $prescriptionModel->getDetailPrescriptionById($prescriptionId);

        $this->render('prescriptions/detail', [
            "prescriptionDetail" => $detailPrescriptionData['data'][0]
        ]);
    }

    public function updateStatus()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('prescriptions');
            return;
        }

        $prescriptionId = $_POST['prescription_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$prescriptionId || !$status) {
            $this->redirect('prescriptions');
            return;
        }

        $result = $this->prescriptionModel->updatePrescriptionStatus($prescriptionId, $status);

        $this->redirect('prescriptions');
    }

    public function medicines()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Get all medicines
        $medicines = $this->prescriptionModel->getAllMedicines();

        $this->render('prescriptions/medicines', [
            'user' => $user,
            'medicines' => $medicines
        ]);
    }
}
