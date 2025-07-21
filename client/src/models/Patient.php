<?php
class Patient
{
    private $apiGatewayUrl = 'http://localhost:6000';

    // Helper method to extract numeric patient ID from user ID
    private function extractPatientId($userAuthId)
    {
        // Extract numeric part from IDs like "BN1", "BN100", etc.
        // Remove "BN" prefix and return the number
        if (preg_match('/^BN(\d+)$/', $userAuthId, $matches)) {
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

    public function getPatientById($userAuthId)
    {
        // Get user token from session for authentication
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return [
                'status' => 401,
                'data' => ['error' => 'Authentication required']
            ];
        }

        // Extract the actual patient ID from the auth ID
        $patientId = $this->extractPatientId($userAuthId);

        if (!$patientId) {
            return [
                'status' => 400,
                'data' => ['error' => 'Invalid patient ID format']
            ];
        }

        // Add Authorization header
        $headers = [
            'Authorization: Bearer ' . $token
        ];

        // Call the API Gateway route: GET /patients/{patient_id}
        $response = $this->callApi('/patients/' . $patientId, 'GET', null, $headers);

        return $response;
    }

    // Method to get current user's patient record
    public function getCurrentPatient()
    {
        // Get current user's ID from session
        $userAuthId = $_SESSION['user']['user_id'] ?? null;

        if (!$userAuthId) {
            return [
                'status' => 401,
                'data' => ['error' => 'User not logged in']
            ];
        }

        return $this->getPatientById($userAuthId);
    }

    public function createPatient($patientData)
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

        // Call the API Gateway route: POST /patients
        $response = $this->callApi('/patients', 'POST', $patientData, $headers);

        return $response;
    }

    // public function getAllPatients($skip = 0, $limit = 100)
    // {
    //     // Note: There's no GET /patients route in your API Gateway
    //     // You might need to add this route or use a different endpoint
    //     $token = $_SESSION['user']['token'] ?? null;

    //     if (!$token) {
    //         return [
    //             'status' => 401,
    //             'data' => ['error' => 'Authentication required']
    //         ];
    //     }

    //     $headers = [
    //         'Authorization: Bearer ' . $token
    //     ];

    //     // This route doesn't exist yet - you'd need to add it to API Gateway
    //     $response = $this->callApi('/patients?skip=' . $skip . '&limit=' . $limit, 'GET', null, $headers);

    //     return $response;
    // }
}
