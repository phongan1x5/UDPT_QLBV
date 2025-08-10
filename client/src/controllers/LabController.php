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

    public function downloadLabResult($usedServiceId)
    {
        $labModel = new Lab();
        $result = $labModel->downloadResultFile($usedServiceId);
        error_log(print_r($result));
    }
}
