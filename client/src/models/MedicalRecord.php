<?php
class MedicalRecord
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
        curl_close($curl);

        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

    public function getMedicalHistory($profileId)
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

        // Call the API Gateway route: GET /medical-history/{profile_id}
        $response = $this->callApi('/medical-history/' . $profileId, 'GET', null, $headers);

        return $response;
    }

    public function createMedicalProfile($profileData)
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

        return $this->callApi('/medical-profiles', 'POST', $profileData, $headers);
    }

    public function createMedicalRecord($recordData)
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

        return $this->callApi('/medical-records', 'POST', $recordData, $headers);
    }

    /**
     * Get medical profile by patient ID - NEEDED for appointment creation
     */
    public function getMedicalProfileByPatient($patientId)
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

        return $this->callApi("/medical-profiles/patient/{$patientId}", 'GET', null, $headers);
    }

    /**
     * Get medical record by ID
     */
    public function getMedicalRecordById($recordId)
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

        return $this->callApi("/medical-record/{$recordId}", 'GET', null, $headers);
    }

    /**
     * Get medical record by AppointmentId
     */
    public function getMedicalRecordsByAppointmentId($appointmentId)
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

        return $this->callApi("/medical-record/byAppointmentId/{$appointmentId}", 'GET', null, $headers);
    }

    /**
     * Get medical record by AppointmentId
     */
    public function getMedicalRecordsByDoctorId($doctorId)
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

        return $this->callApi("/medical-record/byDoctorId/{$doctorId}", 'GET', null, $headers);
    }

    /**
     * Update medical record
     */
    public function updateMedicalRecord($recordId, $data)
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

        return $this->callApi("/medical-records/{$recordId}", 'PUT', $data, $headers);
    }

    /**
     * Get medical records by appointment ID
     */
    // public function getMedicalRecordsByAppointment($appointmentId)
    // {
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

    //     return $this->callApi("/medical-records/appointment/{$appointmentId}", 'GET', null, $headers);
    // }

    /**
     * Get all medical records for a patient
     */
    public function getMedicalRecordsByPatient($patientId)
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

        return $this->callApi("/medical-records/patient/{$patientId}", 'GET', null, $headers);
    }

    /**
     * Get medical records by doctor
     */
    // public function getMedicalRecordsByDoctor($doctorId)
    // {
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

    //     return $this->callApi("/medical-records/doctor/{$doctorId}", 'GET', null, $headers);
    // }
}
