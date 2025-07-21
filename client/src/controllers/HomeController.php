<?php
require_once 'BaseController.php';

class HomeController extends BaseController
{
    private function patientDashboard($user)
    {

        $this->render('dashboard/patient', ['user' => $user]);
    }

    public function dashboard()
    {
        $this->requireLogin();

        // Get user data from session
        $user = $_SESSION['user'] ?? null;

        if ($user['user_role'] == 'patient') {
            $this->patientDashboard($user);
        }
    }
}
