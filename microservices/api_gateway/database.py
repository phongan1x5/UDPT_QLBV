MICROSERVICE_URLS = {
    'auth': 'http://auth_service:6001',
    'patient': 'http://patient_service:6002',
    'staff': 'http://staff_service:6003',
    'appointment': 'http://appointment_service:6004',
    'lab': 'http://lab_service:6005',
    'pharmacy': 'http://pharmacy_service:6006',  # Fixed typo
    'medical_record': 'http://medical_record_service:6007',
    'notification': 'http://notification_service:6008',
}

#this for locally (no Docker)
# MICROSERVICE_URLS = {
#     'auth': 'http://localhost:6001',
#     'patient': 'http://localhost:6002',
#     'staff': 'http://localhost:6003',
#     'appointment': 'http://localhost:6004',
#     'lab': 'http://localhost:6005',
#     'phamarcy': 'http://localhost:6006',
#     'medical_record': 'http://localhost:6007',
#     'notification': 'http://localhost:6008',#
#     'prescription': 'http://localhost:6009',
#     'admin': 'http://localhost:6010',
# }

