<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Lab.php';


class ConsultationController extends BaseController
{
    //User is a docter, userId = docterId

    public function requestLabService()
    {
        $maDichVu = $_POST['MaDichVu'] ?? null;
        $maGiayKhamBenh = $_POST['MaGiayKhamBenh'] ?? null;
        $yeuCauCuThe = $_POST['YeuCauCuThe'] ?? '';

        // Validate required fields
        if (!$maDichVu || !$maGiayKhamBenh) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields: MaDichVu and MaGiayKhamBenh'
            ]);
            return;
        }

        try {
            // Create the lab service request
            $labServiceData = [
                'MaDichVu' => intval($maDichVu),
                'MaGiayKhamBenh' => intval($maGiayKhamBenh),
                'YeuCauCuThe' => $yeuCauCuThe,
                'ThoiGian' => date('c')
            ];

            // Call your Lab model to create the service
            $labModel = new Lab();
            $response = $labModel->createUsedService($labServiceData);

            if ($response['status'] === 200 || $response['status'] === 201) {
                // Success - return the created service data
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Lab service requested successfully',
                    'service' => $response['data']
                ]);
            } else {
                // API call failed
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create lab service request',
                    'error' => $response['data'] ?? 'Unknown error'
                ]);
            }
        } catch (Exception $e) {
            // Handle exceptions
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function submitPrescription()
    {
        $this->requireLogin();

        // Get form data
        $maGiayKhamBenh = $_POST['MaGiayKhamBenh'] ?? null;
        $chanDoan = $_POST['ChanDoan'] ?? null;
        $luuY = $_POST['LuuY'] ?? '';
        $medicinesJson = $_POST['medicines'] ?? '[]';

        // Decode medicines JSON
        $medicines = json_decode($medicinesJson, true);

        // Validate required fields
        if (!$maGiayKhamBenh || !$chanDoan) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields: Medical Record ID and Diagnosis'
            ]);
            return;
        }

        try {
            $medicalRecordModel = new MedicalRecord();
            $prescriptionModel = new Prescription();

            // First, update the medical record with diagnosis and notes
            $medicalRecordUpdateData = [
                'ChanDoan' => $chanDoan,
                'LuuY' => $luuY,
            ];

            $updateResult = $medicalRecordModel->updateMedicalRecord($maGiayKhamBenh, $medicalRecordUpdateData);

            if ($updateResult['status'] !== 200) {
                throw new Exception('Failed to update medical record: ' . ($updateResult['message'] ?? 'Unknown error'));
            }

            // Create prescription with medicines if provided
            $prescriptionId = null;
            if (!empty($medicines)) {
                // Transform medicines to match the API schema
                $thuocTheoToa = [];
                foreach ($medicines as $medicine) {
                    $thuocTheoToa[] = [
                        'MaThuoc' => intval($medicine['MaThuoc']),
                        'SoLuong' => intval($medicine['SoLuong']),
                        'GhiChu' => $medicine['GhiChu']
                    ];
                }

                // Prepare prescription data with medicines included
                $prescriptionData = [
                    'MaGiayKhamBenh' => intval($maGiayKhamBenh),
                    'TrangThaiToaThuoc' => 'Active',
                    'ThuocTheoToa' => $thuocTheoToa  // Medicines are included here
                ];

                // Create the prescription (API will handle adding medicines automatically)
                $prescriptionResult = $prescriptionModel->createPrescription($prescriptionData);

                if ($prescriptionResult['status'] !== 200 && $prescriptionResult['status'] !== 201) {
                    throw new Exception('Failed to create prescription: ' . ($prescriptionResult['message'] ?? 'Unknown error'));
                }

                $prescriptionId = $prescriptionResult['data']['MaToaThuoc'] ?? null;
            }

            // Success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Prescription submitted successfully',
                'data' => [
                    'medical_record_id' => $maGiayKhamBenh,
                    'prescription_id' => $prescriptionId,
                    'medicines_count' => count($medicines),
                    'diagnosis' => $chanDoan
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error in submitPrescription: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function index($appointmentId)
    {
        $this->requireLogin();

        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $prescriptionModel = new Prescription();
        $medicalRecordModel = new MedicalRecord();
        $labServiceModel = new Lab();
        $staffModel = new Staff();
        $patientModel = new Patient();

        // DEBUG: Print the input parameter
        $medicalRecordId = $medicalRecordModel->getMedicalRecordsByAppointmentId($appointmentId)['data'][0]['MedicalRecord']['MaGiayKhamBenh'];
        // echo "<pre>DEBUG - medicalRecordId: ";
        // print_r($medicalRecordId);
        // echo "</pre>";


        $doctor = $staffModel->getStaffById($user['user_id']);
        $medicines = $prescriptionModel->getAllMedicines();
        $medicalRecord = $medicalRecordModel->getMedicalRecordById($medicalRecordId);
        $patient = $patientModel->getPatientById($medicalRecord['data'][0]['MedicalRecord']['MaHSBA']);
        $labServices = $labServiceModel->getAllServices();
        $currentLabServices = $labServiceModel->getUsedServicesByMedicalRecord($medicalRecord['data'][0]['MedicalRecord']['MaGiayKhamBenh']); //In case docter reload the page
        if ($currentLabServices['status'] != 200) {
            $this->render('consulate/doctor', [
                'doctor' => $doctor['data'][0],
                'patient' => $patient['data'][0],
                'medicines' => $medicines['data'][0],
                'medicalRecord' => $medicalRecord['data'][0],
                'labServices' => $labServices['data'][0],
                'currentLabServices' => []
            ]);
        } else {
            $this->render('consulate/doctor', [
                'doctor' => $doctor['data'][0],
                'patient' => $patient['data'][0],
                'medicines' => $medicines['data'][0],
                'medicalRecord' => $medicalRecord['data'][0],
                'labServices' => $labServices['data'][0],
                'currentLabServices' => $currentLabServices['data'][0]
            ]);
        }
    }
}
