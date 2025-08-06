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
            case 'staff':
                $this->staffLabServices($user);
                break;
            case 'doctor':
                $this->doctorLabServices($user);
                break;
            case 'admin':
                $this->adminLabServices($user);
                break;
            case 'patient':
                $this->patientLabServices($user);
                break;
            default:
                $this->redirect('dashboard');
        }
    }

    public function downloadResultFile(string $filename): void
    {
        // Ensure the filename ends with ".pdf"
        if (!str_ends_with($filename, '.pdf')) {
            $filename .= '.pdf';
        }

        $safeFilename = basename($filename);

        $this->labModel->getResultFile($safeFilename);
        exit();
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

        $this->render('lab/patient', [
            'user' => $user,
            'labServices' => $DichVuSuDungAll
        ]);
    }

    private function doctorLabServices($user)
    {
        $rawUsedServices = $this->labModel->getAllUsedServices();
        $rawServices = $this->labModel->getAllServices();

        $usedServices = $rawUsedServices['data'][0];  
        $services = $rawServices['data'][0];         

        $serviceMap = [];
        foreach ($services as $s) {
            $serviceMap[$s['MaDichVu']] = $s;
        }

        foreach ($usedServices as &$us) {
            $ma = $us['MaDichVu'];
            if (isset($serviceMap[$ma])) {
                $us['TenDichVu'] = $serviceMap[$ma]['TenDichVu'];
                $us['NoiDungDichVu'] = $serviceMap[$ma]['NoiDungDichVu'];
                $us['DonGia'] = $serviceMap[$ma]['DonGia'];
                $filepath = $us['FileKetQua'] ?? '';
                $us['FileKetQua'] = pathinfo($filepath, PATHINFO_FILENAME);
            }
        }

        $this->render('lab/doctor', [
            'user' => $user,
            'usedServices' => $usedServices,
            'services' => $services
        ]);
    }

    private function staffLabServices($user)
    {

        $rawUsedServices = $this->labModel->getAllUsedServices();
        $rawServices = $this->labModel->getAllServices();

        $usedServices = $rawUsedServices['data'][0];  
        $services = $rawServices['data'][0];         

        $serviceMap = [];
        foreach ($services as $s) {
            $serviceMap[$s['MaDichVu']] = $s;
        }

        foreach ($usedServices as &$us) {
            $ma = $us['MaDichVu'];
            if (isset($serviceMap[$ma])) {
                $us['TenDichVu'] = $serviceMap[$ma]['TenDichVu'];
                $us['NoiDungDichVu'] = $serviceMap[$ma]['NoiDungDichVu'];
                $us['DonGia'] = $serviceMap[$ma]['DonGia'];
                $filepath = $us['FileKetQua'] ?? '';
                $us['FileKetQua'] = pathinfo($filepath, PATHINFO_FILENAME);
            }
        }
        $this->render('lab/staff', [
            'user' => $user,
            'usedServices' => $usedServices,
            'services' => $services
        ]);

    }

    private function adminLabServices($user)
    {

        $rawUsedServices = $this->labModel->getAllUsedServices();
        $rawServices = $this->labModel->getAllServices();

        $usedServices = $rawUsedServices['data'][0];  
        $services = $rawServices['data'][0];         

        $serviceMap = [];
        foreach ($services as $s) {
            $serviceMap[$s['MaDichVu']] = $s;
        }

        foreach ($usedServices as &$us) {
            $ma = $us['MaDichVu'];
            if (isset($serviceMap[$ma])) {
                $us['TenDichVu'] = $serviceMap[$ma]['TenDichVu'];
                $us['NoiDungDichVu'] = $serviceMap[$ma]['NoiDungDichVu'];
                $us['DonGia'] = $serviceMap[$ma]['DonGia'];
                
                $filepath = $us['FileKetQua'] ?? '';
                $us['FileKetQua'] = pathinfo($filepath, PATHINFO_FILENAME);
            }
        }

        $this->render('lab/admin', [
            'user' => $user,
            'usedServices' => $usedServices,
            'services' => $services
        ]);

    }

    public function viewUsedService($usedServiceId)
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        if (!$usedServiceId) {
            $this->redirect('lab');
            return;
        }

        $response = $this->labModel->getUsedServiceById($usedServiceId);

        // Filter only array items
        $validRecords = array_filter($response['data'] ?? [], 'is_array');
        $usedService = reset($validRecords);

        // Redirect if invalid or not found
        if (!$response || $response['status'] !== 200 || empty($usedService)) {
            $this->redirect('lab');
            return;
        }

        $filepath = $usedService['FileKetQua'] ?? '';
        $usedService['FileKetQua'] = pathinfo($filepath, PATHINFO_FILENAME);

        // Get service details
        $serviceDetails = null;
        if (!empty($usedService['MaDichVu'])) {
            $serviceResponse = $this->labModel->getServiceById($usedService['MaDichVu']);
            if ($serviceResponse && $serviceResponse['status'] === 200) {
                $serviceDetails = $serviceResponse['data'];
            }
        }

        // Render view
        $this->render('lab/viewusedservice', [
            'user' => $user,
            'usedService' => $usedService,
            'serviceDetails' => $serviceDetails
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

    public function updateUsedServicePaidStatus()
    {
        $medicalRecordId = $_POST['medicalRecordId'] ?? null;
        $this->requireLogin();

        // echo '<pre>';
        // print_r($medicalRecordId);
        // echo '</pre>';
        // exit();
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->redirect('login');
            return;
        }

        if (empty($medicalRecordId)) {
            $_SESSION['error'] = 'Medical Record ID is required.';
            $this->redirect('lab');
            return;
        }

$response = $this->labModel->updatePaidUsedService($medicalRecordId);

if ($response['status'] === 200 && is_array($response['data'])) {
    $_SESSION['success'] = 'All services marked as paid.';
} else {
    $_SESSION['error'] = $response['error'] 
        ?? "Failed to update payment status. HTTP {$response['status']}";
}

header('Location: /lab/staff#paid-panel');
exit();

    }


    public function updateUsedService()
    {
        $id = $_POST['MaDVSD'];
        $ketQua = $_POST['KetQua'];
        $fileKetQua = $_POST['FileKetQua'] ?? null;

        $usedServiceData = [
            'KetQua' => $ketQua,
            'FileKetQua' => $fileKetQua
        ]; 
        // Optional file upload
        $file = $_FILES['file_upload'] ?? null; 

        $updated = $this->labModel->updateUsedService($id, $usedServiceData, $file);

        if ($updated) {
            header("Location: /lab/staff");
            exit();
        } else {
            echo "Failed to update used service.";
        }
    }

    public function addServiceForm() {
        $this->render('lab/addservice');
    }

    public function addUsedServiceForm() {
        $this->render('lab/addusedservice');
    }

    public function searchService()
    {
        $query = trim($_GET['query'] ?? '');
        $user = $_SESSION['user'] ?? [];
        $usedServices = [];

        if ($query === '') {
            $response = $this->labModel->getAllServices();
        } else {
            $response = $this->labModel->getServiceById($query);
        }

        if ($response['status'] !== 200 || empty($response['data'])) {
            return $this->render($this->getLabViewByRole($user['user_role']), [
                'services' => [],
                'user' => $user,
                'usedServices' => $usedServices
            ]);
        }

        $firstItem = $response['data'][0];

        $services = isset($firstItem['MaDichVu']) ? [$firstItem] : $firstItem;

        $this->render($this->getLabViewByRole($user['user_role']), [
            'services' => $services,
            'user' => $user,
            'usedServices' => $usedServices
        ]);
    }

    public function getLabViewByRole(string $role): string
    {
        return match ($role) {
            'staff' => 'lab/staff',
            'doctor' => 'lab/doctor',
            'admin' => 'lab/admin',
            'patient' => 'lab/admin', 
            default => 'dashboard'
        };
    }


    public function createService()
    {
        $serviceData = [
            'TenDichVu' => $_POST['TenDichVu'] ?? '',
            'NoiDungDichVu' => $_POST['NoiDungDichVu'] ?? '',
            'DonGia' => $_POST['DonGia'] ?? 0,
        ];

        $labModel = new Lab();
        $response = $labModel->createService($serviceData);

        if ($response['status'] == 200 || $response['status'] == 201) {
            header("Location: /lab/staff");
            exit;
        } else {
            echo "Failed to create service: " . ($response['data']['error'] ?? 'Unknown error');
        }
    }

    public function searchUsedServices()
    {
        $medicalRecordId = $_GET['medicalRecordId'] ?? null;
        $user = $_SESSION['user'] ?? [];
        $labModel = new Lab();

        if (!empty($medicalRecordId)) {
            // If input provided, search by medical record ID
            $response = $labModel->getUsedServicesByMedicalRecord($medicalRecordId);
        } else {
            // If no input, fetch all used services
            $response = $labModel->getAllUsedServices();
        }

        $usedServices = [];

        if ($response['status'] === 200 && isset($response['data'][0])) {
            $usedServices = $response['data'][0]; // works for both cases (search or all)
        }

        // Optional: get all services for display or linking
        $servicesResponse = $labModel->getAllServices();
        $services = $servicesResponse['status'] === 200 && isset($servicesResponse['data'][0])
            ? $servicesResponse['data'][0]
            : [];

        // Determine the view based on user role
        $view = $this->getLabViewByRole($user['user_role'] ?? '');

        // If fallback view is 'dashboard', redirect instead of rendering
        if ($view === 'dashboard') {
            return $this->redirect('dashboard');
        }

        $this->render($view, [
            'user' => $user,
            'usedServices' => $usedServices,
            'services' => $services
        ]);
    }


    public function createUsedService()
    {
        $data = [
            'MaDichVu' => $_POST['MaDichVu'],
            'MaGiayKhamBenh' => $_POST['MaGiayKhamBenh'],
            'ThoiGian' => $_POST['ThoiGian'],
            'YeuCauCuThe' => $_POST['YeuCau'] ?? '',
            'KetQua' => $_POST['KetQua'] ?? '',
            'TrangThai' => "ChoThuTien"
        ];
        // Handle file upload
        if (!empty($_FILES['FileKetQua']['name'])) {
            $uploadDir = 'uploads/results/';
            $fileName = basename($_FILES['FileKetQua']['name']);
            $targetPath = $uploadDir . time() . '_' . $fileName;

            if (move_uploaded_file($_FILES['FileKetQua']['tmp_name'], $targetPath)) {
                $data['FileKetQua'] = $targetPath;
            }
        }

        $response = $this->labModel->createUsedService($data);

        if ($response['status'] === 200) {
            header('Location: /lab/staff#used-panel');
            exit;
        }
        echo "Failed to create used service.";
    }



}
