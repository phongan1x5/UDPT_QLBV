<?php
class Lab
{
    private $apiGatewayUrl = 'http://localhost:6000';

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
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return [
                'status' => 500,
                'data' => null,
                'error' => $error
            ];
        }

        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

    // Lab Services methods
    public function getAllServices($skip = 0, $limit = 100)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/services?skip=' . $skip . '&limit=' . $limit, 'GET', null, $headers);
    }

    public function getServiceById($serviceId)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/services/' . $serviceId, 'GET', null, $headers);
    }

    // Used Services methods
    public function getAllUsedServices($skip = 0, $limit = 100)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services?skip=' . $skip . '&limit=' . $limit, 'GET', null, $headers);
    }

    public function getUsedServiceById($usedServiceId)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services/' . $usedServiceId, 'GET', null, $headers);
    }

    public function getUsedServicesByMedicalRecord($medicalRecordId)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services/medical-record/' . $medicalRecordId, 'GET', null, $headers);
    }

    public function createUsedService($usedServiceData)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services', 'POST', $usedServiceData, $headers);
    }

    public function updateUsedService($usedServiceId, $usedServiceData, $file = null)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        // For file uploads, we'll need to use multipart/form-data
        // This would be handled differently in a real implementation
        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services/' . $usedServiceId, 'PUT', $usedServiceData, $headers);
    }

    public function createService($serviceData)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/services', 'POST', $serviceData, $headers);
    }
}
