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
require_once 'src/controllers/PharmacyController.php';
require_once 'src/controllers/ConsultationController.php';
require_once 'src/controllers/LookupController.php';

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
            if ($pathParts[1] == 'available-slots') {
                $controller->getAvailableSlots();
                break;
            }
            if ($pathParts[1] == 'confirm') {
                if (isset($pathParts[2])) {
                    $controller->doctorConfirmAppointment($pathParts[2]);
                }

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
        if (isset($pathParts[1]) && $pathParts[1] === 'view-detail') {
            if (isset($pathParts[2])) { //MaGiayKhamBenh
                $controller->viewDetail(intval($pathParts[2]));
            }
            break;
        }
        if (isset($pathParts[1]) && $pathParts[1] === 'doctorRecents') {
            $controller->doctorRecentMedicalRecord();
            break;
        }
        if (isset($pathParts[1])) {
            $controller->index($pathParts[1]);
        }
        break;


    // case 'labResults':
    //     $controller = new LabController();
    //     if (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $controller->updateUsedService();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $controller->createService();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'addservice') {
    //         $controller->addServiceForm();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && $pathParts[2] === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    //         $controller->searchService();
    //     } else {
    //         $controller->index();
    //     }
    //     break;

    // case 'lab':
    //     $controller = new LabController();
    //     if (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $controller->updateUsedService();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $controller->createService();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $controller->createUsedService();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'addservice') {
    //         $controller->addServiceForm();
    //     } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && $pathParts[2] === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    //         $controller->searchService();
    //     } else if (isset($pathParts[2]) && $pathParts[2] === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    //         $controller->searchUsedServices();
    //     } else if (isset($pathParts[1]) && $pathParts[1] === 'addusedservice') {
    //         $controller->addUsedServiceForm();
    //     } else {
    //         $controller->index();
    //     }
    //     break;
    case 'pharmacy':
        $controller = new PharmacyController();
        if (isset($pathParts[1]) && $pathParts[1] === 'medicine' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->addMedicine();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'addmedicine') {
            $controller->addMedicineForm();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'update-status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateStatus();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'medicines') {
            $controller->medicines();
        } else {
            $controller->index();
        }
        break;

    case 'consultation':
        // /consultation/request-lab-service
        $controller = new ConsultationController();
        if (isset($pathParts[1]) && $pathParts[1] === 'request-lab-service') {
            $controller->requestLabService();
            break;
        }
        if (isset($pathParts[1]) && $pathParts[1] === 'submit-prescription') {
            $controller->submitPrescription();
            break;
        }
        if (isset($pathParts[1])) {
            $controller->index(intval($pathParts[1]));
        }
        break;

    case 'lookup':
        $controller = new LookupController();
        if (isset($pathParts[1]) && $pathParts[1] === 'patientMedicalHistory') {
            $controller->lookupPatientMedicalHistory();
            break;
        }

    default:
        http_response_code(404);
        echo "Page not found - Path: " . $path;
        break;
}
