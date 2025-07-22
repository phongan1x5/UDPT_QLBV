<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Lab.php';
require_once __DIR__ . '/../models/MedicalRecord.php';

class LabController extends BaseController
{
    private $labModel;
    private $medicalRecordModel;

    public function __construct()
    {
        $this->labModel = new Lab();
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
                $this->patientLabServices($user);
                break;
            case 'doctor':
                $this->doctorLabServices($user);
                break;
            case 'admin':
                $this->adminLabServices($user);
                break;
            default:
                $this->redirect('dashboard');
        }
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

    private function patientLabServices($user)
    {
        $patientId = $this->extractPatientId($user['user_id']);
        $medicalRecordModel = new MedicalRecord();
        $medicalHistory = $medicalRecordModel->getMedicalHistory($patientId);

        $maGiayKhamBenhArray = [];

        foreach ($medicalHistory['data'][0]['MedicalRecords'] as $record) {
            $maGiayKhamBenhArray[] = $record['MaGiayKhamBenh'];
        }

        // print_r($maGiayKhamBenhArray);
        $DichVuSuDungAll = [];
        $labModel = new Lab();
        foreach ($maGiayKhamBenhArray as $MaGiayKhamBenh) {
            $KiemTraDichVuSuDung = $labModel->getUsedServicesByMedicalRecord($MaGiayKhamBenh);
            // print_r($KiemTraDichVuSuDung);
            if ($KiemTraDichVuSuDung['status'] == 200) {
                $CacDichVuSuDung = $KiemTraDichVuSuDung['data'][0];
                foreach ($CacDichVuSuDung as $DichVuSuDung) {
                    $DichVuSuDungAll[] = $DichVuSuDung;
                }
            }
        }
        // print_r($DichVuSuDungAll);

        $this->render('lab/patient', [
            'user' => $user,
            'labServices' => $DichVuSuDungAll
        ]);
    }

    private function doctorLabServices($user)
    {
        // TODO: Implement doctor lab services view
        $this->render('lab/doctor', [
            'user' => $user,
            'message' => 'Doctor lab services view coming soon!'
        ]);
    }

    private function adminLabServices($user)
    {
        // Get all lab services for admin
        $allUsedServices = $this->labModel->getAllUsedServices();
        $allServices = $this->labModel->getAllServices();

        $this->render('lab/admin', [
            'user' => $user,
            'usedServices' => $allUsedServices,
            'services' => $allServices
        ]);
    }

    public function viewLabService($id)
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Get lab service details
        $labService = $this->labModel->getUsedServiceById($id);

        if (!$labService || $labService['status'] !== 200) {
            $this->redirect('lab');
            return;
        }

        // Get service details
        $serviceDetails = null;
        if (isset($labService['data']['MaDichVu'])) {
            $serviceResponse = $this->labModel->getServiceById($labService['data']['MaDichVu']);
            if ($serviceResponse && $serviceResponse['status'] === 200) {
                $serviceDetails = $serviceResponse['data'];
            }
        }

        $this->render('lab/view', [
            'user' => $user,
            'labService' => $labService['data'],
            'serviceDetails' => $serviceDetails
        ]);
    }

    public function services()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Get all available services
        $services = $this->labModel->getAllServices();

        $this->render('lab/services', [
            'user' => $user,
            'services' => $services
        ]);
    }

    public function downloadResult($id)
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Get lab service details
        $labService = $this->labModel->getUsedServiceById($id);

        if (!$labService || $labService['status'] !== 200) {
            $this->redirect('lab');
            return;
        }

        $labServiceData = $labService['data'];

        // Check if user has permission to download this result
        if ($user['user_role'] === 'patient') {
            // Extract patient ID and verify ownership
            $patientId = $this->extractPatientId($user['user_id']);

            if (!$patientId) {
                $this->redirect('lab');
                return;
            }

            // Get patient's medical history to verify ownership
            $medicalHistory = $this->medicalRecordModel->getMedicalHistory($patientId);

            $hasAccess = false;
            if (
                $medicalHistory &&
                $medicalHistory['status'] === 200 &&
                isset($medicalHistory['data'][0]['MedicalRecords'])
            ) {

                // Check if this lab service belongs to any of the patient's medical records
                foreach ($medicalHistory['data'][0]['MedicalRecords'] as $record) {
                    if ($record['MaGiayKhamBenh'] == $labServiceData['MaGiayKhamBenh']) {
                        $hasAccess = true;
                        break;
                    }
                }
            }

            if (!$hasAccess) {
                $_SESSION['error'] = 'You do not have permission to access this lab result.';
                $this->redirect('lab');
                return;
            }
        }

        // Handle file download
        if (isset($labServiceData['FileKetQua']) && !empty($labServiceData['FileKetQua'])) {
            $filePath = $labServiceData['FileKetQua'];

            // Check if file exists (assuming files are stored in a uploads directory)
            $fullFilePath = __DIR__ . '/../../uploads/lab_results/' . basename($filePath);

            if (file_exists($fullFilePath)) {
                // Set headers for file download
                $fileInfo = pathinfo($fullFilePath);
                $fileName = 'lab_result_' . $id . '.' . ($fileInfo['extension'] ?? 'pdf');

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Length: ' . filesize($fullFilePath));
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                // Clear output buffer
                ob_clean();
                flush();

                // Read and output file
                readfile($fullFilePath);
                exit;
            } else {
                $_SESSION['error'] = 'Result file not found on server.';
                $this->redirect('lab');
            }
        } else {
            $_SESSION['error'] = 'No result file available for download.';
            $this->redirect('lab');
        }
    }
}
