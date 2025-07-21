<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Patient.php';

class ProfileController extends BaseController
{
    private function patientProfile($user)
    {
        $patientModel = new Patient();
        $patient = $patientModel->getPatientById($user['user_id']);

        $this->render('profile/patient', ['patient' => $patient]);
    }
    public function profile()
    {
        $this->requireLogin();

        // Get user data from session
        $user = $_SESSION['user'] ?? null;
        if ($user['user_role'] == 'patient') {
            $this->patientProfile($user);
        }
    }
}
