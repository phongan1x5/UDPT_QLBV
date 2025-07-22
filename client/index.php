<?php
session_start();

// Include necessary files
require_once 'src/config/database.php';
require_once 'src/controllers/BaseController.php';
require_once 'src/controllers/AuthController.php';
require_once 'src/controllers/HomeController.php';
require_once 'src/controllers/ProfileController.php';
require_once 'src/controllers/PrescriptionController.php';
require_once 'src/controllers/MedicalRecordController.php';
require_once 'src/controllers/LabController.php';
require_once 'src/controllers/AppointmentController.php';

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Get the base path dynamically
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('index.php', '', $scriptName);

// For direct access to index.php, get the path after the script name
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Remove leading slash if present
$path = ltrim($path, '/');

// Handle query parameters for fallback
if (empty($path) && isset($_GET['action'])) {
    $path = $_GET['action'];
}

// Handle POST parameters for fallback
if (empty($path) && isset($_POST['action'])) {
    $path = $_POST['action'];
}

$pathParts = explode('/', $path); // e.g., ['paper', '123']

// Route handling
switch ($pathParts[0]) {
    case '':
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;

    case 'register':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        $controller = new HomeController();
        $controller->dashboard();
        break;

    case 'profile':
        $controller = new ProfileController();
        $controller->profile();
        break;

    case 'appointments':
        $controller = new AppointmentController();
        if (isset($pathParts[1])) {
            if ($pathParts[1] == 'book') {
                $controller->book();
                break;
            }
            break;
        }
        $controller->index();
        break;

    case 'prescriptions':
        $controller = new PrescriptionController();
        $controller->index();
        break;

    case 'medicalRecords':
        $controller = new MedicalRecordController();
        $controller->index();
        break;

    case 'labResults':
        $controller = new LabController();
        $controller->index();
        break;

    default:
        http_response_code(404);
        echo "Page not found - Path: " . $path;
        break;
}
