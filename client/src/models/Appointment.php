<?php
// In your Patient model or a new Appointment model
class Appointment
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

    public function getPatientAppointments($patientId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/patient/' . $patientId, 'GET', null, $headers);
    }

    public function getPatientUpcomingAppointments($patientId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/patient/' . $patientId . '/upcoming', 'GET', null, $headers);
    }

    public function getPatientAppointmentHistory($patientId, $skip = 0, $limit = 100)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/patient/' . $patientId . '/history?skip=' . $skip . '&limit=' . $limit, 'GET', null, $headers);
    }
}
