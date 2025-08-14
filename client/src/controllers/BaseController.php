<?php
class BaseController
{
    protected $db;
    private $apiGatewayUrl = 'http://localhost:6000';

    public function __construct()
    {
        // $database = new Database();
        // $this->db = $database->getConnection();
    }

    protected function render($view, $data = [])
    {
        // Extract data to variables
        extract($data);

        // Include the view file
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: " . $view;
        }
    }

    protected function redirect($url)
    {
        // Get base path
        $basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $fullUrl = rtrim($basePath, '/') . '/' . ltrim($url, '/');
        header("Location: " . $fullUrl);
        exit();
    }

    protected function isLoggedIn()
    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['token'])) {
            return $this->verifyToken($_SESSION['user']['token']);
        }
        return false;
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }

    protected function isAdmin()
    {
        return isset($_SESSION['user']['user_role']) && $_SESSION['user']['user_role'] === 'admin';
    }

    private function verifyToken($token)
    {
        $tokenResponse = $this->callApi('/auth/validate-token?token=' . urlencode($token), 'GET');
        if ($tokenResponse && $tokenResponse['status'] === 200 && isset($tokenResponse['data'][0]['user'])) {
            // Store in session
            return true;
        } else {
            $error = "Failed to retrieve user information";
            return false;
        }
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
