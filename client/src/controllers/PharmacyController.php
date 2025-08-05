<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Patient.php';

class PharmacyController extends BaseController
{
    private $PharmacyModel;

    public function __construct()
    {
        $this->PharmacyModel = new Prescription();
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
                $this->staffPharmacys($user);
                break;
            default:
                $this->redirect('dashboard');
        }
    }
    private function staffPharmacys($user)
    {

        // echo '<pre>';
        // print_r($allPharmacys);
        // echo '</pre>';
        // exit();
        $response1 = $this->PharmacyModel->getAllPrescriptions(); // or getPrescriptionById(), etc.

        $prescriptions = [];

        if ($response1['status'] === 200 && isset($response1['data'][0])) {
            $prescriptions = $response1['data'][0];
        }
        $response = $this->PharmacyModel->getAllMedicines();

        $medicines = [];
        if ($response['status'] === 200 && isset($response['data'][0])) {
            $medicines = $response['data'][0];
        }

        $this->render('pharmacy/staff', [
            'user' => $user,
            'prescriptions' => $prescriptions,
            'medicines' => $medicines
        ]);
    }
    private function doctorPharmacys($user)
    {
        // TODO: Implement doctor Pharmacys view
        $this->render('Pharmacys/doctor', [
            'user' => $user,
            'message' => 'Doctor Pharmacys view coming soon!'
        ]);
    }

    private function adminPharmacys($user)
    {
        // Get all Pharmacys for admin
        $allPharmacys = $this->PharmacyModel->getAllPrescriptions();
        $allMedicines = $this->PharmacyModel->getAllMedicines();

        $this->render('Pharmacys/admin', [
            'user' => $user,
            'Pharmacys' => $allPharmacys,
            'medicines' => $allMedicines
        ]);
    }

    public function viewPharmacy($id)
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        // Get Pharmacy details
        $Pharmacy = $this->PharmacyModel->getPrescriptionById($id);

        if (!$Pharmacy || $Pharmacy['status'] !== 200) {
            $this->redirect('Pharmacys');
            return;
        }

        // Get Pharmacy with medicines by medical record
        $PharmacyWithMedicines = null;
        if (isset($Pharmacy['data']['MaGiayKhamBenh'])) {
            $recordPharmacys = $this->PharmacyModel->getPrescriptionsByMedicalRecord($Pharmacy['data']['MaGiayKhamBenh']);
            if (
                $recordPharmacys &&
                $recordPharmacys['status'] === 200 &&
                isset($recordPharmacys['data']) &&
                is_array($recordPharmacys['data'])
            ) {

                foreach ($recordPharmacys['data'] as $rp) {
                    if (isset($rp['MaToaThuoc']) && $rp['MaToaThuoc'] == $id) {
                        $PharmacyWithMedicines = $rp;
                        break;
                    }
                }
            }
        }

        $this->render('Pharmacys/view', [
            'user' => $user,
            'Pharmacy' => $Pharmacy['data'],
            'PharmacyWithMedicines' => $PharmacyWithMedicines
        ]);
    }

    public function updateStatus()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pharmacy/staff');
            return;
        }

        $pharmacyId = $_POST['MaToaThuoc'] ?? null;

        $status = isset($_POST['TrangThai']) ? 'Đã phát' : 'Chưa phát';

        if (!$pharmacyId) {
            $this->redirect('pharmacy/staff');
            return;
        }

        $result = $this->PharmacyModel->updatePrescriptionStatus($pharmacyId, $status);

        $this->redirect('pharmacy/staff');
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
        $medicines = $this->PharmacyModel->getAllMedicines();

        $this->render('Pharmacys/medicines', [
            'user' => $user,
            'medicines' => $medicines
        ]);
    }

    public function addMedicineForm()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        $this->render('pharmacy/addmedicine', [
            'user' => $user
        ]);
    }
    public function addMedicine()
    {
        $medicineData = [
            'TenThuoc' => $_POST['TenThuoc'] ?? '',
            'DonViTinh' => $_POST['DonViTinh'] ?? '',
            'ChiDinh' => $_POST['ChiDinh'] ?? null,
            'SoLuongTonKho' => (int)($_POST['SoLuongTonKho'] ?? 0),
            'GiaTien' => (float)($_POST['GiaTien'] ?? 0),
        ];

        $response = $this->PharmacyModel->createMedicine($medicineData);

        if ($response['status'] === 200 || $response['status'] === 201) {
            header('Location: /pharmacy/staff');
            exit();
        } else {
            echo "Failed to create medicine.";
            var_dump($response);
        }
    }
}
