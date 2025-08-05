<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Lab.php';


class DeskStaffController extends BaseController
{
    //User is a docter, userId = docterId
    public function createPatient()
    {
        $this->requireLogin();
        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $this->render('deskStaff/createPatient', [
            "user" => $user
        ]);
    }

    public function submitCreatePatient()
    {
        $this->requireLogin();
        error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
        error_log("POST data: " . print_r($_POST, true));
        error_log("Raw input: " . file_get_contents('php://input'));

        $patientData = [
            'HoTen' => trim($_POST['HoTen']),
            'NgaySinh' => ($_POST['NgaySinh']),
            'GioiTinh' => trim($_POST['GioiTinh']),
            'Email' => trim($_POST['Email']),
            'SoDienThoai' => trim($_POST['SoDienThoai']),
            'SoDinhDanh' => trim($_POST['SoDinhDanh']),
            'Password' => trim($_POST['Password']),
            'TienSu' => trim($_POST['TienSu'])
        ];

        $diaChi = trim($_POST['DiaChi']);
        if ($diaChi !== '') {
            $patientData['DiaChi'] = $diaChi;
        }
        $BaoHiemYTe = trim($_POST['BaoHiemYTe']);
        if ($BaoHiemYTe !== '') {
            $patientData['BaoHiemYTe'] = $BaoHiemYTe;
        }


        error_log("Patient data: " . print_r($patientData, true));

        try {
            $patientModel = new Patient();

            $createResult = $patientModel->createPatient($patientData);
            error_log("created result data: " . print_r($createResult, true));
            error_log("created result data status: " . print_r($createResult['status'], true));
            // error_log("created result data status: " . gettype($createResult['status']));

            if ($createResult['status'] !== 200) {
                throw new Exception('Failed to create patient: ' . ($createResult['message'] ?? 'Unknown error'));
            }

            // Success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Patient created successfully',
                'data' => [
                    'newPatient' => $createResult
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error in submitCreatePatient: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function findPatient() {}
}
