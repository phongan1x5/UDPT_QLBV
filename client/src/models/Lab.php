<?php
class Lab
{
    private $apiGatewayUrl = 'http://localhost:6000';

    private function callApi($endpoint, $method = 'GET', $data = null, $headers = [], $isBinary = false)
    {
        $url = $this->apiGatewayUrl . $endpoint;
        $curl = curl_init();

        $defaultHeaders = [
            'Accept: application/pdf, application/octet-stream, */*',
        ];

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

        // If response is binary (e.g. PDF), return raw data
        return $isBinary
            ? ['status' => $httpCode, 'data' => $response]  // raw binary data
            : ['status' => $httpCode, 'data' => json_decode($response, true)];
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

    public function updateUsedService($usedServiceId, $usedServiceData, $file)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $url = $this->apiGatewayUrl . '/lab/used-services/' . $usedServiceId;

        $headers = [
            'Authorization: Bearer ' . $token,
        ];

        // Prepare multipart form data
        $postFields = [
            'KetQua' => $usedServiceData['KetQua'],
        ];

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $postFields['file'] = new CURLFile(
                $file['tmp_name'],
                $file['type'],
                $file['name']
            );
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
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

    public function getResultFile(string $filename)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];

        $endpoint = "/lab/used-services/results/{$filename}";
        $resp = $this->callApi($endpoint, 'GET', null, $headers, true);

        if ($resp['status'] === 200 && $resp['data'] !== null) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="'.$filename.'"');
            echo $resp['data'];
            exit;
        }

        // error handling:
        http_response_code($resp['status']);
        echo $resp['error'] ?? 'Unknown error';
        exit;
    }
    public function updatePaidUsedService($medicalRecordId)
    {        
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }
        // echo '<pre>';
        // print_r($medicalRecordId);
        // echo '</pre>';
        // exit();
        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services/paid_medical_record/' . $medicalRecordId , 'PUT', null, $headers);
    }
    // public function updatePaidUsedService(int $medicalRecordId): array
    // {
    //     $token = $_SESSION['user']['token'] ?? null;
    //     if (!$token) {
    //         return [
    //             'status' => 401,
    //             'data'   => null,
    //             'error'  => 'Authentication required'
    //         ];
    //     }
    //     // ⚠️ Make sure the endpoint exactly matches your gateway routing:
    //     $url = $this->apiGatewayUrl
    //          . '/lab/used-services/paid_medical_record/' 
    //          . $medicalRecordId;

    //     $headers = [
    //       'Authorization: Bearer ' . $token
    //     ];

    //     $ch = curl_init($url);
    //     curl_setopt_array($ch, [
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_CUSTOMREQUEST  => 'PUT',
    //         CURLOPT_HTTPHEADER     => $headers,
    //         CURLOPT_TIMEOUT        => 30,
    //     ]);

    //     $response   = curl_exec($ch);
    //     $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $error      = curl_error($ch);
    //     curl_close($ch);

    //     if ($error) {
    //         return [
    //             'status' => 500,
    //             'data'   => null,
    //             'error'  => $error
    //         ];
    //     }

    //     // Try to decode JSON response
    //     $decoded = json_decode($response, true);
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return [
    //             'status' => $httpCode,
    //             'data'   => null,
    //             'error'  => 'JSON decode error: ' . json_last_error_msg()
    //         ];
    //     }
    //     return [
    //         'status' => $httpCode,
    //         'data'   => $decoded,
    //         'error'  => null
    //     ];
    // }
}
