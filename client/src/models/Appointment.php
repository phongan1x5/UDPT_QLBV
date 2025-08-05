<?php
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

    // Create a new appointment
    public function createAppointment($appointmentData)
    {
        $token = $_SESSION['user']['token'] ?? null;

        if (!$token) {
            return ['status' => 401, 'data' => ['error' => 'Authentication required']];
        }

        $headers = ['Authorization: Bearer ' . $token];
        return $this->callApi('/appointments', 'POST', $appointmentData, $headers);
    }

    // Get all appointments (admin/staff)
    public function getAllAppointments($skip = 0, $limit = 100)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments?skip=' . $skip . '&limit=' . $limit, 'GET', null, $headers);
    }

    // Get available time slots for a specific doctor and date
    public function getAvailableSlots($doctorId, $date)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/available-slots/' . $doctorId . '/' . $date, 'GET', null, $headers);
    }

    // Get all doctors
    public function getAllDoctors()
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        // Add missing leading slash to the endpoint
        return $this->callApi('/staff/getDoctors', 'GET', null, $headers);
    }

    public function getPatientAppointments($patientId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/patient/' . $patientId, 'GET', null, $headers);
    }


    public function getPatientVerifiedAppointments($patientId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/patient/verified/' . $patientId, 'GET', null, $headers);
    }

    //get appointments that have already being paid by patient
    public function getPatientPaidAppointments($patientId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/patient/paid/' . $patientId, 'GET', null, $headers);
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

    // Cancel an appointment
    public function cancelAppointment($appointmentId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/' . $appointmentId . '/cancel', 'PUT', null, $headers);
    }

    // Update appointment status
    public function updateAppointmentStatus($appointmentId, $status)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        $data = ['TrangThai' => $status];
        return $this->callApi('/appointments/' . $appointmentId, 'PUT', $data, $headers);
    }

    // Get appointment by ID
    public function getAppointmentById($appointmentId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/' . $appointmentId, 'GET', null, $headers);
    }

    // Get all appointments for a specific doctor
    public function getDoctorAppointments($doctorId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        return $this->callApi('/appointments/doctor/' . $doctorId, 'GET', null, $headers);
    }

    // Get upcoming appointments for a specific doctor
    public function getDoctorUpcomingAppointments($doctorId)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        // Get all doctor appointments and filter for upcoming ones
        $response = $this->callApi('/appointments/doctor/' . $doctorId, 'GET', null, $headers);

        if ($response['status'] === 200 && isset($response['data'])) {
            $today = date('Y-m-d');
            $upcomingAppointments = [];

            // Handle double-wrapped response
            $appointmentsData = $response['data'];
            if (is_array($appointmentsData) && isset($appointmentsData[0]) && is_array($appointmentsData[0])) {
                $appointmentsData = $appointmentsData[0];
            }

            foreach ($appointmentsData as $appointment) {
                if ($appointment['Ngay'] >= $today) {
                    $upcomingAppointments[] = $appointment;
                }
            }

            return [
                'status' => 200,
                'data' => $upcomingAppointments
            ];
        }

        return $response;
    }

    // Get appointment history for a specific doctor
    public function getDoctorAppointmentHistory($doctorId, $skip = 0, $limit = 100)
    {
        $token = $_SESSION['user']['token'] ?? null;
        $headers = ['Authorization: Bearer ' . $token];

        // Get all doctor appointments and filter for completed/past ones
        $response = $this->callApi('/appointments/doctor/' . $doctorId, 'GET', null, $headers);

        if ($response['status'] === 200 && isset($response['data'])) {
            $today = date('Y-m-d');
            $historyAppointments = [];

            // Handle double-wrapped response
            $appointmentsData = $response['data'];
            if (is_array($appointmentsData) && isset($appointmentsData[0]) && is_array($appointmentsData[0])) {
                $appointmentsData = $appointmentsData[0];
            }

            foreach ($appointmentsData as $appointment) {
                // Include past appointments or completed appointments
                if ($appointment['Ngay'] < $today || $appointment['TrangThai'] === 'HoanThanh') {
                    $historyAppointments[] = $appointment;
                }
            }

            // Sort by date (most recent first)
            usort($historyAppointments, function ($a, $b) {
                $dateCompare = strtotime($b['Ngay']) - strtotime($a['Ngay']);
                if ($dateCompare === 0) {
                    return strtotime($b['Gio']) - strtotime($a['Gio']);
                }
                return $dateCompare;
            });

            // Apply pagination
            $historyAppointments = array_slice($historyAppointments, $skip, $limit);

            return [
                'status' => 200,
                'data' => $historyAppointments
            ];
        }

        return $response;
    }
}
