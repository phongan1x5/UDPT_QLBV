<?php
require_once 'BaseController.php';

class AuthController extends BaseController
{

    private $apiGatewayUrl = 'http://localhost:6000'; //For now we work directly with the auth service

    public function showLogin()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $this->render('auth/login');
    }

    public function showRegister()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $this->render('auth/register');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id'] ?? '');
            $password = $_POST['password'] ?? '';

            // Basic validation
            if (empty($id) || empty($password)) {
                $error = "Please enter both ID and password";
                $this->render('auth/login', ['error' => $error]);
                return;
            }

            // Call microservice API
            $response = $this->callApi('/auth/login', 'POST', [
                'id' => $id,
                'password' => $password
            ]);
            // Debug: Echo the login response
            echo "<h3>Login Response Debug:</h3>";
            echo "<pre>";
            var_dump($response);
            echo "</pre>";

            // After fixing API Gateway, the response should be:
            // {"access_token": "...", "token_type": "bearer"}
            if ($response && $response['status'] === 200 && isset($response['data'][0]['access_token'])) {
                $token = $response['data'][0]['access_token'];
                $tokenType = $response['data'][0]['token_type'];

                // Get user info by validating token
                $userResponse = $this->callApi('/auth/validate-token?token=' . urlencode($token), 'GET');
                echo "<h3>User Response Debug:</h3>";
                echo "<pre>";
                var_dump($userResponse);
                echo "</pre>";
                // After fixing API Gateway, the response should be:
                // {"message": "Token is valid", "user": {"id": "BN100", "role": "patient"}}
                if ($userResponse && $userResponse['status'] === 200 && isset($userResponse['data'][0]['user'])) {
                    // Store in session
                    $_SESSION['user'] = [
                        'token' => $token,
                        'token_type' => $tokenType,
                        'user_id' => $userResponse['data'][0]['user']['id'],
                        'user_role' => $userResponse['data'][0]['user']['role']
                    ];

                    $this->redirect('dashboard');
                } else {
                    $error = "Failed to retrieve user information";
                    $this->render('auth/login', ['error' => $error]);
                }
            } else {
                $error = "Invalid credentials";
                if (isset($response['data']['detail'])) {
                    $error = $response['data']['detail'];
                }
                $this->render('auth/login', ['error' => $error]);
            }
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'patient';

            // Validation
            $errors = [];

            if (empty($id)) {
                $errors[] = "ID is required";
            }

            if (empty($password) || strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }

            if ($password !== $confirmPassword) {
                $errors[] = "Passwords do not match";
            }

            if (!empty($errors)) {
                $this->render('auth/register', ['errors' => $errors]);
                return;
            }

            // Call microservice API
            $response = $this->callApi('/auth/register', 'POST', [ //need to fix the url into auth/register
                'id' => $id,
                'password' => $password,
                'role' => $role
            ]);

            if ($response && ($response['status'] === 200 || $response['status'] === 201)) {
                $success = "Account created successfully! You can now login.";
                $this->render('auth/register', ['success' => $success]);
            } else {
                $error = "Registration failed";
                if (isset($response['data']['detail'])) {
                    $error = $response['data']['detail'];
                }
                $this->render('auth/register', ['error' => $error]);
            }
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('login');
    }

    private function callApi($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->apiGatewayUrl . $endpoint;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
}
