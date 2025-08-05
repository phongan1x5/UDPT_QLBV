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

    public function lookupPatientForAppointmentFees()
    {
        $this->requireLogin();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            $this->redirect('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input = $_POST['input'] ?? null;
            $patientModel = new Patient();
            $patient = $patientModel->getPatientByPhone($input);
            error_log("Patient data find by phone");
            error_log(print_r($patient, true));
            if ($patient['status'] !== 200) {
                // If not found by phone then try by patientId
                $patient = $patientModel->getPatientById(intval($input));
                error_log("Patient data find by id");
                error_log(print_r($patient, true));

                if ($patient['status'] !== 200) {
                    $_SESSION['lookup_error'] = 'Wrong patient id';
                    $this->redirect('lookup/patientAppointmentFees');
                    //Wrong again, then no patient found
                }
                $patientId = $patient['data'][0]['id'];
                $this->redirect('appointments/collectFees/' . $patientId);
                return;
            }
            $patientId = $patient['data'][0]['id'];
            $this->redirect('appointments/collectFees/' . $patientId);
            return;
        }

        $this->render('lookup/patientAppointmentFees');
    }
}
