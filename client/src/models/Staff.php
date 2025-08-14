<?php
class Staff
{
    private $apiGatewayUrl = 'http://localhost:6000';

    // Helper method to extract numeric patient ID from user ID
    private function extractStaffId($userAuthId)
    {
        // Extract numeric part from IDs like "BN1", "BN100", etc.
        // Remove "BN" prefix and return the number
        if (preg_match('/^DR(\d+)$/', $userAuthId, $matches)) {
            return $matches[1]; // Returns just the number part
        }

        if (preg_match('/^DS(\d+)$/', $userAuthId, $matches)) {
            return $matches[1]; // Returns just the number part
        }

        if (preg_match('/^LS(\d+)$/', $userAuthId, $matches)) {
            return $matches[1]; // Returns just the number part
        }

        if (preg_match('/^PH(\d+)$/', $userAuthId, $matches)) {
            return $matches[1]; // Returns just the number part
        }

        if (preg_match('/^AD(\d+)$/', $userAuthId, $matches)) {
            return $matches[1]; // Returns just the number part
        }

        // If it's already numeric, return as is
        if (is_numeric($userAuthId)) {
            return $userAuthId;
        }

        // If no pattern matches, return null
        return null;
    }

    private function callApi($endpoint, $method = 'GET', $data = null, $headers = [])
    {
        $url = $this->apiGatewayUrl . $endpoint;

        $curl = curl_init();

        // Default headers
        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Merge with additional headers (like Authorization)
        $allHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $allHeaders
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

    public function getStaffById($userAuthId)
    {
        // Get user token from session for authentication
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $staffId = $this->extractStaffId($userAuthId);

        if (!$staffId) {
            return [
                'status' => 400,
                'data' => ['error' => 'Invalid staff ID format']
            ];
        }

        // Add Authorization header
        $headers = [
            'Authorization: Bearer ' . $token
        ];

        // Call the API Gateway route: GET /patients/{patient_id}
        $response = $this->callApi('/staff/' . $staffId, 'GET', null, $headers);

        return $response;
    }

    public function createStaff($staffData)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $token
        ];

        return $this->callApi('/staff', 'POST', $staffData, $headers);
    }

    public function getAllStaff()
    {
        // Get user token from session for authentication
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        // Add Authorization header
        $headers = [
            'Authorization: Bearer ' . $token
        ];

        // Call the API Gateway route: GET /patients/{patient_id}
        $response = $this->callApi('/staff', 'GET', null, $headers);

        return $response;
    }

    public function createDepartment($departmentData)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $token
        ];

        return $this->callApi('/departments', 'POST', $departmentData, $headers);
    }


    public function getAllDepartments()
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $token
        ];

        return $this->callApi('/departments', 'GET', null, $headers);
    }

    public function updateStaff($staffId, $staffData)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $token
        ];

        // Call the API Gateway route: PUT /staff/{staff_id}
        return $this->callApi('/staff/' . $staffId, 'PUT', $staffData, $headers);
    }

    public function getStaffByIdDirect($staffId)
    {
        // Get staff by direct ID (not auth ID format)
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $token
        ];

        return $this->callApi('/staff/' . $staffId, 'GET', null, $headers);
    }

    public function broadcastNotificationToStaff($sourceSystem, $users, $message)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $token
        ];

        // Send individual notifications using existing endpoint
        $results = [];
        $successful = 0;
        $failed = 0;

        foreach ($users as $user) {
            $notificationData = [
                'userId' => $user['userId'],
                'userEmail' => $user['userEmail'],
                'sourceSystem' => $sourceSystem,
                'message' => $message
            ];

            $response = $this->callApi('/send-email-and-store', 'POST', $notificationData, $headers);

            if ($response['status'] >= 200 && $response['status'] < 300) {
                $successful++;
            } else {
                $failed++;
            }

            $results[] = $response;
        }

        return [
            'status' => 200,
            'data' => [
                'successfullyQueued' => $successful,
                'failedToQueue' => $failed,
                'total' => count($users),
                'details' => $results
            ]
        ];
    }
}
