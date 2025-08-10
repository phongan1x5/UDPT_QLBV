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

        $this->render('admin/createStaff', [
            "user" => $user
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
}
