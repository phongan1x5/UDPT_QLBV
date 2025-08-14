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
require_once 'src/controllers/AdminController.php';
require_once 'src/controllers/DeskStaffController.php';
require_once 'src/controllers/NotificationController.php';

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

    case 'notifications':
        $controller = new NotificationController();
        $controller->index();
        break;

    case 'dashboard':
        $controller = new HomeController();
        $user = $_SESSION['user'] ?? null;
        if ($user['user_role'] == "lab_staff") {
            $pharmarcyController = new LabController();
            $pharmarcyController->index();
            break;
        }
        if ($user['user_role'] == "pharmacist") {
            $pharmarcyController = new PharmacyController();
            $pharmarcyController->index();
            break;
        }
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
                if (isset($pathParts[2])) {
                    //When a staff book for patient
                    $controller->book($pathParts[2]);
                    break;
                }
                $controller->book();
                break;
            }

            if ($pathParts[1] == 'cancel') {
                if (isset($pathParts[2])) {
                    //When a staff book for patient
                    $controller->cancelAppointment($pathParts[2]);
                    break;
                }
                break;
            }

            if ($pathParts[1] == 'available-slots') {
                $controller->getAvailableSlots();
                break;
            }
            if ($pathParts[1] == 'confirm') {
                if (isset($pathParts[2])) {
                    $controller->doctorConfirmAppointment($pathParts[2]);
                    break;
                }

                break;
            }
            if ($pathParts[1] == 'collectFees') {
                if (isset($pathParts[2])) {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $controller->staffCollectAppointmentMoney($pathParts[2]);
                        break;
                    }
                    $controller->collectAppointmentFeesPanel($pathParts[2]);
                    break;
                }
            }

            if ($pathParts[1] == 'doctorSchedule') {
                if (isset($pathParts[2])) {
                    $controller->doctorSchedule($pathParts[2]);
                    break;
                } else {
                    $controller->doctorSchedule();
                    break;
                }
            }

            if ($pathParts[1] == 'doctorAppointments') {
                if (isset($pathParts[2])) {
                    $controller->doctorAppointments($pathParts[2]);
                }
                break;
            }

            $controller->index();
            break;
        }
        $controller->index();
        break;

    case 'prescriptions':
        $controller = new PrescriptionController();
        if (isset($pathParts[1]) && $pathParts[1] === 'view-detail') {
            if (isset($pathParts[2])) {
                $controller->viewDetailPrescription($pathParts[2]);
            }
            break;
        }
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

        if (isset($pathParts[1]) && $pathParts[1] === 'updateHistory') {
            if (isset($pathParts[2])) { //MaHSBA
                $controller->updateHistory(intval($pathParts[2]));
            }
            break;
        }

        if (isset($pathParts[1])) {
            $controller->index($pathParts[1]);
            break;
        }
        $controller->index();
        break;

    case 'lab':
        $controller = new LabController();
        if (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateUsedService();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'paid' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateUsedServicePaidStatus();
        } elseif (
            isset($pathParts[1], $pathParts[2]) &&
            $pathParts[1] === 'used-services' &&
            $pathParts[2] === 'results'
        ) {
            $controller->downloadResultFile($pathParts[3]);
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'view' && isset($pathParts[2]) && is_numeric($pathParts[2])) {
            $controller->viewUsedService($pathParts[2]);
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->createService();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->createUsedService();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'addservice') {
            $controller->addServiceForm();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && $pathParts[2] === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->searchService();
        } else if (isset($pathParts[1]) && $pathParts[1] === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->searchUsedServices();
        } else if (isset($pathParts[1]) && $pathParts[1] === 'addusedservice') {
            $controller->addUsedServiceForm();
        } else {
            $controller->index();
        }
        break;

    case 'labResults':
        $controller = new LabController();
        // if (isset($pathParts[1]) && $pathParts[1] === 'used-services' && isset($pathParts[2]) && $pathParts[2] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        //     $controller->updateUsedService();
        // } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        //     $controller->createService();
        // } elseif (isset($pathParts[1]) && $pathParts[1] === 'addservice') {
        //     $controller->addServiceForm();
        // } elseif (isset($pathParts[1]) && $pathParts[1] === 'services' && $pathParts[2] === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        //     $controller->searchService();
        // } else {
        //     $controller->index();
        // }
        if (isset($pathParts[1]) && $pathParts[1] === 'download' && isset($pathParts[2])) {
            $controller->downloadLabResult($pathParts[2]);
            return;
        }
        $controller->index();
        break;

    case 'pharmacy':
        $controller = new PharmacyController();

        if (isset($pathParts[1]) && $pathParts[1] === 'medicine' && isset($pathParts[2]) && $pathParts[2] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->addMedicine();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'addmedicine') {
            $controller->addMedicineForm();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'paidPrescription') {
            $controller->paidPrescription();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'handleMedicine') {
            $controller->handleMedicine();
        } elseif (isset($pathParts[1]) && $pathParts[1] === 'updatemedicine') {
            $controller->updateMedicineForm();
        } elseif (
            isset($pathParts[1], $pathParts[2]) &&
            $pathParts[1] === 'medicines' &&
            $pathParts[2] === 'update' &&
            $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $controller->updateMedicine();
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

        if (isset($pathParts[1]) && $pathParts[1] === 'remove-lab-service') {
            if (isset($pathParts[2])) {
                $controller->removeLabService($pathParts[2]);
            }
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
        if (isset($pathParts[1]) && $pathParts[1] === 'patientAppointmentFees') {
            $controller->lookupPatientForAppointmentFees();
            break;
        }
        if (isset($pathParts[1]) && $pathParts[1] === 'doctorAppointments') {
            $controller->lookupDoctorAppointments();
            break;
        }

    case 'admin':
        $controller = new AdminController();
        if (isset($pathParts[1]) && $pathParts[1] === 'createStaff') {
            $controller->createStaff();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'createDepartment') {
            $controller->createDepartment();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'submitCreateStaff') {
            $controller->submitCreateStaff();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'viewAllStaff') {
            $controller->viewAllStaff();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'viewAllDepartment') {
            $controller->viewAllDepartment();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'viewAllPatient') {
            $controller->viewAllPatient();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'findStaff') {
            if (isset($pathParts[2])) {
                $controller->getStaffById($pathParts[2]);
                break;
            }
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'updateStaff') {
            if (isset($pathParts[2])) {
                $controller->updateStaff($pathParts[2]);
                break;
            }
            $controller->updateStaffView();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'findPatient') {
            if (isset($pathParts[2])) {
                $controller->getPatientById($pathParts[2]);
                break;
            }
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'updatePatient') {
            if (isset($pathParts[2])) {
                $controller->updatePatient($pathParts[2]);
                break;
            }
            $controller->updatePatientView();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'broadcastNotificationForStaff') {
            $controller->broadcastNotificationToStaffView();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'broadcast-notification') {
            $controller->broadcastNotificationToStaff();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'prescriptionReport') {
            $controller->prescriptionReport();
            break;
        }

        if (isset($pathParts[1]) && $pathParts[1] === 'patientReport') {
            $controller->patientReport();
            break;
        }

        break;

    case 'deskStaff':
        $controller = new DeskStaffController();
        if (isset($pathParts[1]) && $pathParts[1] === 'createPatient') {
            $controller->createPatient();
            break;
        }
        if (isset($pathParts[1]) && $pathParts[1] === 'submitCreatePatient') {
            $controller->submitCreatePatient();
            break;
        }
        break;


    default:
        http_response_code(404);
        echo "Page not found - Path: " . $path;
        break;
}
