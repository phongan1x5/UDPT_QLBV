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


    public function deleteUsedService($serviceId)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/lab/used-services/' . $serviceId, 'DELETE', null, $headers);
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

    public function downloadResultFile($usedServiceId)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $url = $this->apiGatewayUrl . '/lab/download/' . $usedServiceId;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60, // Longer timeout for file downloads
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Accept: application/octet-stream, application/pdf, image/*'
            ],
            CURLOPT_HEADERFUNCTION => function ($curl, $header) {
                // Capture response headers
                if (stripos($header, 'Content-Disposition:') === 0) {
                    $_SESSION['download_filename'] = $this->extractFilenameFromHeader($header);
                }
                if (stripos($header, 'Content-Type:') === 0) {
                    $_SESSION['download_content_type'] = trim(substr($header, 13));
                }
                return strlen($header);
            }
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return [
                'status' => 500,
                'data' => null,
                'error' => $error
            ];
        }

        if ($httpCode === 200) {
            $this->serveFileDownload($response, $_SESSION['view_filename'] ?? 'lab_result_' . $usedServiceId . '.pdf');
            // return [
            //     'status' => $httpCode,
            //     // 'data' => $response, // Binary file data
            //     'content_type' => $contentType,
            //     'filename' => $_SESSION['download_filename'] ?? 'lab_result_' . $usedServiceId . '.pdf'
            // ];
        } else {
            // Try to decode error response
            $errorData = json_decode($response, true);
            return [
                'status' => $httpCode,
                'data' => null,
                'error' => $errorData['detail'] ?? 'Download failed'
            ];
        }
    }

    public function viewResultFile($usedServiceId)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $url = $this->apiGatewayUrl . '/lab/view/' . $usedServiceId;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Accept: application/pdf, image/*, text/plain'
            ],
            CURLOPT_HEADERFUNCTION => function ($curl, $header) {
                if (stripos($header, 'Content-Disposition:') === 0) {
                    $_SESSION['view_filename'] = $this->extractFilenameFromHeader($header);
                }
                if (stripos($header, 'Content-Type:') === 0) {
                    $_SESSION['view_content_type'] = trim(substr($header, 13));
                }
                return strlen($header);
            }
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return [
                'status' => 500,
                'data' => null,
                'error' => $error
            ];
        }

        if ($httpCode === 200) {
            return [
                'status' => $httpCode,
                'data' => $response, // Binary file data
                'content_type' => $contentType,
                'filename' => $_SESSION['view_filename'] ?? 'lab_result_' . $usedServiceId . '.pdf',
                'disposition' => 'inline' // For viewing in browser
            ];
        } else {
            $errorData = json_decode($response, true);
            return [
                'status' => $httpCode,
                'data' => null,
                'error' => $errorData['detail'] ?? 'View failed'
            ];
        }
    }

    // Helper function to extract filename from Content-Disposition header
    private function extractFilenameFromHeader($header)
    {
        if (preg_match('/filename[^;=\n]*=(([\'"]).*?\2|[^;\n]*)/', $header, $matches)) {
            $filename = trim($matches[1], '"\'');
            return $filename;
        }
        return null;
    }

    // Utility function to serve file download to browser
    public function serveFileDownload($fileData, $filename, $contentType = 'application/octet-stream')
    {
        // Clean any output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers for file download
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($fileData));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Output the file data
        echo $fileData;
        exit;
    }

    // Utility function to serve file for viewing in browser
    public function serveFileView($fileData, $filename, $contentType = 'application/pdf')
    {
        // Clean any output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers for inline viewing
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($fileData));
        header('Cache-Control: public, max-age=3600'); // Cache for 1 hour

        // Output the file data
        echo $fileData;
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
