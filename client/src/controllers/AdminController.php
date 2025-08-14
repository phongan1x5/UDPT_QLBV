<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Lab.php';


class AdminController extends BaseController
{
    public function createDepartment()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $staffModel = new Staff();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $departmentName = $_POST['department_name'] ?? null;

            $departmentData = [
                'TenPhongBan' => $departmentName
            ];

            $response = $staffModel->createDepartment($departmentData);
            header('Content-Type: application/json');
            echo json_encode($response);

            return;
        }

        $currentAllDepartments = $staffModel->getAllDepartments();

        $this->render('admin/createDepartment', [
            "user" => $user,
            "currentAllDepartments" => $currentAllDepartments['data'][0]
        ]);
    }

    //User is a docter, userId = docterId
    public function createStaff()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $departmentModel = new Staff();
        $departments = $departmentModel->getAllDepartments();

        $this->render('admin/createStaff', [
            "user" => $user,
            "departments" => $departments['data'][0] ?? []
        ]);
    }

    public function submitCreateStaff()
    {
        $this->requireLogin();
        error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
        error_log("POST data: " . print_r($_POST, true));
        error_log("Raw input: " . file_get_contents('php://input'));

        $staffData = [
            'IDPhongBan' => intval(trim($_POST['IDPhongBan'])),
            'HoTen' => trim($_POST['HoTen']),
            'NgaySinh' => trim($_POST['NgaySinh']),
            'DiaChi' => trim($_POST['DiaChi']),
            'SoDienThoai' => trim($_POST['SoDienThoai']),
            'SoDinhDanh' => trim($_POST['SoDinhDanh']),
            'LoaiNhanVien' => trim($_POST['LoaiNhanVien']),
            'Password' => trim($_POST['Password'])
        ];

        error_log("Staff data: " . print_r($staffData, true));

        try {
            $staffModel = new Staff();

            $createResult = $staffModel->createStaff($staffData);
            error_log("created result data: " . print_r($createResult, true));
            error_log("created result data status: " . print_r($createResult['status'], true));
            // error_log("created result data status: " . gettype($createResult['status']));

            if ($createResult['status'] !== 200) {
                throw new Exception('Failed to create staff: ' . ($createResult['message'] ?? 'Unknown error'));
            }

            // Success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Staff created successfully',
                'data' => [
                    'newStaff' => $createResult
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error in submitCreateStaff: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function viewAllStaff()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $staffModel = new Staff();
        $staffs = $staffModel->getAllStaff();

        $this->render('admin/viewAllStaff', [
            'staffs' => $staffs['data'][0]
        ]);
    }

    public function viewAllDepartment()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $staffModel = new Staff();
        $departments = $staffModel->getAllDepartments();

        $this->render('admin/viewAllDepartment', [
            'departments' => $departments['data'][0]
        ]);
    }

    public function viewAllPatient()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $patientModel = new Patient();
        $patients = $patientModel->getAllPatients();

        $this->render('admin/viewAllPatient', [
            'patients' => $patients['data'][0]
        ]);
    }

    public function prescriptionReport()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'] ?? null;
            $prescriptionModel = new Prescription;
            $response = $prescriptionModel->getReport($year);
            if ($response['status'] !== 200) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Server error: '
                ]);
                return;
            }
            header('Content-Type: application/json');
            echo json_encode($response['data']);
            return;
        }

        $this->render('admin/prescriptionReport');
    }

    public function patientReport()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'] ?? null;
            $medicalRecordModel = new MedicalRecord;
            $response = $medicalRecordModel->getAllMedicalRecords();
            error_log(print_r($response, true));
            if ($response['status'] !== 200) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Server error: '
                ]);
                return;
            }
            header('Content-Type: application/json');
            echo json_encode($response['data']);
            return;
        }

        $this->render('admin/patientReport');
    }


    public function getStaffById($staffId)
    {
        $staffModel = new Staff();
        $response = $staffModel->getStaffByIdDirect($staffId);

        header('Content-Type: application/json');

        if ($response['status'] === 200) {
            echo json_encode([
                'success' => true,
                'staff' => $response['data']
            ]);
        } else {
            http_response_code($response['status']);
            echo json_encode([
                'success' => false,
                'message' => $response['data']['error'] ?? 'Staff not found'
            ]);
        }
    }

    public function updateStaff($staffId)
    {
        $staffModel = new Staff();
        $updateData = json_decode(file_get_contents('php://input'), true);

        $response = $staffModel->updateStaff($staffId, $updateData);

        header('Content-Type: application/json');

        if ($response['status'] === 200) {
            echo json_encode([
                'success' => true,
                'message' => 'Staff updated successfully',
                'staff' => $response['data']
            ]);
        } else {
            http_response_code($response['status']);
            echo json_encode([
                'success' => false,
                'message' => $response['data']['error'] ?? 'Failed to update staff'
            ]);
        }
    }

    public function updateStaffView()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        $staffModel = new Staff();
        $departments = $staffModel->getAllDepartments();


        if (!$user) {
            $this->redirect('login');
            return;
        }

        $this->render('admin/updateStaffView', [
            'departments' => $departments['data'][0] ?? []
        ]);
    }


    public function getPatientById($patientId)
    {
        $patientModel = new Patient();
        $response = $patientModel->getPatientById($patientId);

        header('Content-Type: application/json');

        if ($response['status'] === 200) {
            echo json_encode([
                'success' => true,
                'patient' => $response['data']
            ]);
        } else {
            http_response_code($response['status']);
            echo json_encode([
                'success' => false,
                'message' => $response['data']['error'] ?? 'Patient not found'
            ]);
        }
    }

    public function updatePatient($patientId)
    {
        $patientModel = new Patient();
        $updateData = json_decode(file_get_contents('php://input'), true);
        error_log(intval($patientId));
        error_log(print_r($updateData, true));

        $response = $patientModel->updatePatient($patientId, $updateData);

        header('Content-Type: application/json');

        if ($response['status'] === 200) {
            echo json_encode([
                'success' => true,
                'message' => 'Patient updated successfully',
                'patient' => $response['data']
            ]);
        } else {
            http_response_code($response['status']);
            echo json_encode([
                'success' => false,
                'message' => $response['data']['error'] ?? 'Failed to update patient'
            ]);
        }
    }

    public function updatePatientView()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        // $staffModel = new Staff();
        // $departments = $staffModel->getAllDepartments();


        if (!$user) {
            $this->redirect('login');
            return;
        }

        $this->render('admin/updatePatientView');
    }

    public function broadcastNotificationToStaffView()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $staffModel = new Staff();
        $staffs = $staffModel->getAllStaff();

        $this->render('admin/broadcastNotificationForStaff', [
            'staffs' => $staffs['data'][0]
        ]);
    }

    public function broadcastNotificationToStaff()
    {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON input'
                ]);
                return;
            }

            // Validate required fields
            if (empty($input['sourceSystem']) || empty($input['users']) || empty($input['message'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required fields: sourceSystem, users, message'
                ]);
                return;
            }

            // Initialize Staff model
            $staffModel = new Staff();

            // Call the broadcast function
            $response = $staffModel->broadcastNotificationToStaff(
                $input['sourceSystem'],
                $input['users'],
                $input['message']
            );

            header('Content-Type: application/json');

            if ($response['status'] === 200 || $response['status'] === 201) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Broadcast notification sent successfully',
                    'data' => $response['data'],
                    'totalUsers' => count($input['users']),
                    'successfullyQueued' => $response['data']['successfullyQueued'] ?? count($input['users']),
                    'failedToQueue' => $response['data']['failedToQueue'] ?? 0
                ]);
            } else {
                http_response_code($response['status']);
                echo json_encode([
                    'success' => false,
                    'message' => $response['data']['error'] ?? 'Failed to send broadcast notification',
                    'details' => $response['data']
                ]);
            }
        } catch (Exception $e) {
            error_log('Broadcast notification error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ]);
        }
    }
}
