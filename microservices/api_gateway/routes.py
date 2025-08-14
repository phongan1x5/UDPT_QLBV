from fastapi import APIRouter, Request, Depends, HTTPException, Response
import httpx
from database import MICROSERVICE_URLS
from fastapi.responses import StreamingResponse
import io

router = APIRouter()

# Helper function

async def forward_request(method: str, url: str, data=None, headers=None, require_auth=True, request: Request = None, stream_binary: bool = False,):
    # Verify token with the auth service if required
    if require_auth:
        if not request:
            raise HTTPException(status_code=400, detail="Request object is required for authentication")
        token = request.headers.get("Authorization")
        if not token:
            raise HTTPException(status_code=401, detail="Authorization token is missing")
        token = token.replace("Bearer ", "")  # Remove "Bearer " prefix if present

        # Call the auth service's validate-token endpoint
        async with httpx.AsyncClient() as client:
            try:
                auth_resp = await client.get(
                    f"{MICROSERVICE_URLS['auth']}/validate-token",
                    params={"token": token}  # Send token as a query parameter
                )
                auth_resp.raise_for_status()
                payload = auth_resp.json()  # Extract user info from the response
                if not headers:
                    headers = {}
                headers["X-User-ID"] = payload.get("user", {}).get("id")
                headers["X-User-Role"] = payload.get("user", {}).get("role")
            except httpx.RequestError as e:
                raise HTTPException(status_code=500, detail=f"Auth service request failed: {str(e)}")
            except httpx.HTTPStatusError as e:
                raise HTTPException(status_code=auth_resp.status_code, detail=auth_resp.text)

    # Forward the request to the microservice
    async with httpx.AsyncClient() as client:
        try:
            if method == "GET":
                resp = await client.get(url, headers=headers)
            elif method == "POST":
                resp = await client.post(url, json=data, headers=headers)
            elif method == "PUT":
                resp = await client.put(url, json=data, headers=headers)
            elif method == "DELETE":
                resp = await client.delete(url, headers=headers)
            else:
                raise ValueError("Unsupported HTTP method")
            resp.raise_for_status()
            if stream_binary:
            # proxy the raw bytes, preserve content-type
                return Response(content=resp.content, media_type=resp.headers.get("content-type"))
            else:
                return resp.json(), resp.status_code
        except httpx.RequestError as e:
            raise HTTPException(status_code=500, detail=f"Request failed: {str(e)}")
        except httpx.HTTPStatusError as e:
            raise HTTPException(status_code=e.response.status_code, detail=e.response.text)

# Auth_service
@router.post("/auth/register")
async def register(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['auth']}/register", data=data, require_auth=False)

@router.post("/auth/login")
async def login(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['auth']}/login", data=data, require_auth=False)

@router.get("/auth/validate-token")
async def validate_token(token: str):
    return await forward_request("GET", f"{MICROSERVICE_URLS['auth']}/validate-token?token={token}", require_auth=False)

# Patients_service
@router.post("/patients")
async def create_patient(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['patient']}/patients", data=data, request=request)

@router.put("/patients/{patient_id}")
async def update_patient(patient_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['patient']}/patients/{patient_id}", data=data, request=request)

@router.get("/patients")
async def get_all_patient(request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['patient']}/patients", request=request)

@router.get("/patients/{patient_id}")
async def get_patient(patient_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['patient']}/patients/{patient_id}", request=request)

@router.get("/patients/forDoctor/{patient_id}")
async def get_patient_for_doctor(patient_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['patient']}/patients/forDoctor/{patient_id}", request=request)

# Staff_service
@router.post("/departments")
async def create_department(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['staff']}/departments", data=data, request=request)

@router.get("/departments")
async def get_all_department(request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['staff']}/departments", request=request)

@router.get("/staff/getDoctors")
async def get_doctors(request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['staff']}/staff/getDoctors", request=request)

@router.get("/staff/by-type/{staff_type}")
async def get_staff_by_type(staff_type: str, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['staff']}/staff/by-type/{staff_type}", request=request)

@router.post("/staff")
async def create_staff(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['staff']}/staff", data=data, request=request)

@router.get("/staff")
async def list_staff(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['staff']}/staff?skip={skip}&limit={limit}", request=request)

@router.get("/staff/{staff_id}")
async def get_staff(staff_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['staff']}/staff/{staff_id}", request=request)

@router.put("/staff/{staff_id}")
async def update_staff(staff_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['staff']}/staff/{staff_id}", data=data, request=request)

@router.delete("/staff/{staff_id}")
async def delete_staff(staff_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['staff']}/staff/{staff_id}", request=request)

# Appointment_service
@router.post("/appointments")
async def create_appointment(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['appointment']}/appointments", data=data, request=request)

@router.get("/appointments")
async def list_appointments(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments?skip={skip}&limit={limit}", request=request)

# Get verified appointments for a specific patient
@router.get("/appointments/patient/verified/{patient_id}")
async def get_verified_patient_appointments(patient_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/patient/verified/{patient_id}", request=request)

# Get paid appointments for a specific patient
@router.get("/appointments/patient/paid/{patient_id}")
async def get_paid_patient_appointments(patient_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/patient/paid/{patient_id}", request=request)

# Get appointments for a specific patient
@router.get("/appointments/patient/{patient_id}")
async def get_patient_appointments(patient_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/patient/{patient_id}", request=request)


@router.get("/appointments/{appointment_id}")
async def get_appointment(appointment_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/{appointment_id}", request=request)

@router.put("/appointments/{appointment_id}")
async def update_appointment(appointment_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['appointment']}/appointments/{appointment_id}", data=data, request=request)

@router.delete("/appointments/{appointment_id}")
async def delete_appointment(appointment_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['appointment']}/appointments/{appointment_id}", request=request)

@router.get("/appointments/doctor/{doctor_id}")
async def get_appointments_for_doctor(doctor_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/doctor/{doctor_id}", request=request)

@router.get("/appointments/available-slots/{doctor_id}/{date}")
async def get_available_slots(doctor_id: int, date: str, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/available-slots/{doctor_id}/{date}", request=request)

@router.get("/appointments/available-slots/{doctor_id}/{date}/summary")
async def get_slots_summary(doctor_id: int, date: str, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['appointment']}/appointments/available-slots/{doctor_id}/{date}/summary", request=request)

# Lab_service
@router.post("/lab/services")
async def create_service(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['lab']}/services", data=data, request=request)

@router.get("/lab/services")
async def list_services(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/services?skip={skip}&limit={limit}", request=request)

@router.get("/lab/services/{service_id}")
async def get_service(service_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/services/{service_id}", request=request)

@router.get("/lab/used-services/medical-record/{medical_record_id}")
async def get_used_services_by_medical_record(medical_record_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/used-services/medical-record/{medical_record_id}", request=request)

@router.post("/lab/used-services")
async def create_used_service(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['lab']}/used-services", data=data, request=request)

@router.put("/lab/used-services/paid_medical_record/{medical_record_id}")
async def paid_used_service_medical_record(medical_record_id: int, request: Request):
    return await forward_request("PUT", f"{MICROSERVICE_URLS['lab']}/used-services/paid_medical_record/{medical_record_id}", request=request)
# Keep the update route as form handling (for file uploads)
@router.put("/lab/used-services/{used_service_id}")
async def update_used_service(used_service_id: int, request: Request):
    # Get the form data from the request (for file uploads)
    form_data = await request.form()
    
    # Extract the Authorization header
    auth_header = request.headers.get("Authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Authorization token is missing")
    
    # Prepare headers for the microservice request
    headers = {"Authorization": auth_header}
    
    # Forward the form data to the lab microservice
    async with httpx.AsyncClient() as client:
        try:
            # Prepare form data for forwarding
            form_data_dict = {}
            files_dict = {}
            
            for key, value in form_data.items():
                if hasattr(value, 'read'):  # It's a file
                    content = await value.read()
                    files_dict[key] = (value.filename, content, value.content_type)
                else:  # It's regular form field
                    form_data_dict[key] = str(value)
            
            # Make the request to lab microservice with form data
            response = await client.put(
                f"{MICROSERVICE_URLS['lab']}/used-services/{used_service_id}",
                data=form_data_dict,
                files=files_dict if files_dict else None,
                headers=headers,
                timeout=30.0
            )
            
            response.raise_for_status()
            return response.json()
            
        except httpx.RequestError as e:
            raise HTTPException(status_code=500, detail=f"Request failed: {str(e)}")
        except httpx.HTTPStatusError as e:
            raise HTTPException(status_code=e.response.status_code, detail=e.response.text)

@router.get("/lab/used-services")
async def list_used_services(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/used-services?skip={skip}&limit={limit}", request=request)

@router.get("/lab/used-services/{used_service_id}")
async def get_used_service(used_service_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/used-services/{used_service_id}", request=request)


@router.delete("/lab/used-services/{used_service_id}")
async def delete_used_service(used_service_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['lab']}/used-services/{used_service_id}", request=request)

@router.get("/lab/download/{used_service_id}")
async def download_result_file(used_service_id: int, request: Request):
    """
    Download lab result file by service ID through API Gateway
    """
    # Extract the Authorization header
    auth_header = request.headers.get("Authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Authorization token is missing")
    
    # Prepare headers for the microservice request
    headers = {"Authorization": auth_header}
    
    # Forward the request to the lab microservice
    async with httpx.AsyncClient() as client:
        try:
            response = await client.get(
                f"{MICROSERVICE_URLS['lab']}/download/{used_service_id}",
                headers=headers,
                timeout=30.0
            )
            
            response.raise_for_status()
            
            # Get the file content and headers from lab service
            content = response.content
            content_type = response.headers.get("content-type", "application/octet-stream")
            content_disposition = response.headers.get("content-disposition", "")
            
            # Return the file response
            from fastapi.responses import Response
            return Response(
                content=content,
                media_type=content_type,
                headers={
                    "Content-Disposition": content_disposition
                }
            )
            
        except httpx.RequestError as e:
            raise HTTPException(status_code=500, detail=f"Request failed: {str(e)}")
        except httpx.HTTPStatusError as e:
            raise HTTPException(status_code=e.response.status_code, detail=e.response.text)

@router.get("/lab/view/{used_service_id}")
async def view_result_file(used_service_id: int, request: Request):
    """
    View lab result file in browser (inline) by service ID through API Gateway
    """
    # Extract the Authorization header
    auth_header = request.headers.get("Authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Authorization token is missing")
    
    # Prepare headers for the microservice request
    headers = {"Authorization": auth_header}
    
    # Forward the request to the lab microservice
    async with httpx.AsyncClient() as client:
        try:
            response = await client.get(
                f"{MICROSERVICE_URLS['lab']}/view/{used_service_id}",
                headers=headers,
                timeout=30.0
            )
            
            response.raise_for_status()
            
            # Get the file content and headers from lab service
            content = response.content
            content_type = response.headers.get("content-type", "application/octet-stream")
            content_disposition = response.headers.get("content-disposition", "")
            
            # Return the file response
            from fastapi.responses import Response
            return Response(
                content=content,
                media_type=content_type,
                headers={
                    "Content-Disposition": content_disposition
                }
            )
            
        except httpx.RequestError as e:
            raise HTTPException(status_code=500, detail=f"Request failed: {str(e)}")
        except httpx.HTTPStatusError as e:
            raise HTTPException(status_code=e.response.status_code, detail=e.response.text)


@router.get("/lab/used-services/results/{filename}")
async def secure_file(filename: str, request: Request):
    lab_url = f"{MICROSERVICE_URLS['lab']}/used-services/results/{filename}"
    return await forward_request(
        method="GET",
        url=lab_url,
        request=request,
        stream_binary=True,
    )

# === PHARMACY SERVICE ROUTES (CLEANED UP) ===
# Medicine routes
@router.post("/medicines")
async def create_medicine(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['pharmacy']}/medicines", data=data, request=request)

@router.get("/medicines")
async def list_medicines(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/medicines?skip={skip}&limit={limit}", request=request)

@router.get("/medicines/{medicine_id}")
async def get_medicine(medicine_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/medicines/{medicine_id}", request=request)

@router.put("/medicines/{medicine_id}")
async def update_medicine(medicine_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['pharmacy']}/medicines/{medicine_id}", data=data, request=request)

@router.delete("/medicines/{medicine_id}")
async def delete_medicine(medicine_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['pharmacy']}/medicines/{medicine_id}", request=request)

# Prescription routes
@router.post("/prescriptions")
async def create_prescription(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions", data=data, request=request)

@router.get("/prescriptions")
async def list_prescriptions(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions?skip={skip}&limit={limit}", request=request)

@router.get("/prescriptions/with-medicines")
async def list_prescriptions(request: Request, skip: int = 0, limit: int = 1000):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/with-medicines", request=request)

@router.post("/prescriptions/handleMedicinePrescription/{prescriptionId}")
async def handle_medicine_distrubute_prescription(prescriptionId: int, request: Request):
    return await forward_request("POST", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/handleMedicinePrescription/{prescriptionId}", request=request)

@router.get("/prescriptions/detail/{prescriptionId}")
async def get_detail_prescription(prescriptionId: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/detail/{prescriptionId}", request=request)

@router.get("/prescriptions/{prescription_id}")
async def get_prescription(prescription_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/{prescription_id}", request=request)

@router.put("/prescriptions/{prescription_id}")
async def update_prescription(prescription_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/{prescription_id}", data=data, request=request)

@router.delete("/prescriptions/{prescription_id}")
async def delete_prescription(prescription_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/{prescription_id}", request=request)

@router.get("/prescriptions/medical-record/{medical_record_id}")
async def get_prescriptions_by_medical_record(medical_record_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/medical-record/{medical_record_id}", request=request)

@router.put("/prescriptions/{prescription_id}/status")
async def update_prescription_status(prescription_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/{prescription_id}/status", data=data, request=request)

# === MEDICAL RECORD SERVICE ROUTES ===
@router.post("/medical-profiles")
async def create_medical_profile(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles", data=data, request=request, require_auth=False)

@router.get("/medical-profiles")
async def list_medical_profiles(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles?skip={skip}&limit={limit}", request=request)

@router.get("/medical-profiles/{profile_id}")
async def get_medical_profile(profile_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles/{profile_id}", request=request)

@router.put("/medical-profiles/{profile_id}")
async def update_medical_profile(profile_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles/{profile_id}", data=data, request=request)

@router.delete("/medical-profiles/{profile_id}")
async def delete_medical_profile(profile_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles/{profile_id}", request=request)

@router.get("/medical-record/{medical_record_id}/patient")
async def get_patient_by_medical_record(medical_record_id: int, request: Request):
    """
    Get patient data from medical record ID
    Steps:
    1. Get medical record by ID
    2. Extract MaHSBA (patient ID) from medical record
    3. Get patient data using patient ID
    4. Return patient data
    """
    # Extract the Authorization header
    # auth_header = request.headers.get("Authorization")
    # if not auth_header:
    #     raise HTTPException(status_code=401, detail="Authorization token is missing")
    
    # # Prepare headers for microservice requests
    # headers = {"Authorization": auth_header}
    
    async with httpx.AsyncClient() as client:
        try:
            # Step 1: Get medical record by ID
            medical_record_response = await client.get(
                f"{MICROSERVICE_URLS['medical_record']}/medical-record/{medical_record_id}",
                # headers=headers,
                timeout=30.0
            )
            medical_record_response.raise_for_status()
            medical_record_data = medical_record_response.json()
            
            # Step 2: Extract MaHSBA (patient ID) from medical record
            if isinstance(medical_record_data, tuple) and len(medical_record_data) >= 1:
                # Handle tuple response format (data, status_code)
                medical_record = medical_record_data[0]
            else:
                medical_record = medical_record_data
            
            # print("medical record data", medical_record['MedicalRecord'])
            medical_record = medical_record['MedicalRecord']
            patient_id = medical_record.get('MaHSBA')
            # print("patient_id: ", patient_id)
            if not patient_id:
                raise HTTPException(
                    status_code=404, 
                    detail=f"Patient ID (MaHSBA) not found in medical record {medical_record_id}"
                )
            
            # Step 3: Get patient data using patient ID
            patient_response = await client.get(
                f"{MICROSERVICE_URLS['patient']}/patients/{patient_id}",
                # headers=headers,
                timeout=30.0
            )
            # print("patient response", patient_response.json())
            patient_response.raise_for_status()
            patient_data = patient_response.json()
            
            # Step 4: Return patient data with additional context
            if isinstance(patient_data, tuple) and len(patient_data) >= 1:
                patient = patient_data[0]
                status_code = patient_data[1] if len(patient_data) > 1 else 200
            else:
                patient = patient_data
                status_code = 200
            
            # Return enriched response
            return {
                "status": "success",
                "medical_record_id": medical_record_id,
                "patient_id": patient_id,
                "patient": patient
            }, status_code
            
        except httpx.RequestError as e:
            raise HTTPException(status_code=500, detail=f"Request failed: {str(e)}")
        except httpx.HTTPStatusError as e:
            if e.response.status_code == 404:
                raise HTTPException(
                    status_code=404, 
                    detail=f"Medical record {medical_record_id} not found or patient not found"
                )
            raise HTTPException(status_code=e.response.status_code, detail=e.response.text)
        except Exception as e:
            raise HTTPException(status_code=500, detail=f"Processing error: {str(e)}")

@router.post("/medical-records")
async def create_medical_record(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['medical_record']}/medical-records", data=data, request=request)

@router.get("/medical-records")
async def list_medical_records(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-records?skip={skip}&limit={limit}", request=request)

@router.get("/medical-record/byAppointmentId/{appointmentId}")
async def get_medical_record_by_appointmentId(appointmentId: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-record/byAppointmentId/{appointmentId}", request=request)

@router.get("/medical-record/byDoctorId/{doctorId}")
async def get_medical_record_by_appointmentId(doctorId: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-record/byDoctorId/{doctorId}", request=request)

@router.get("/medical-record/{record_id}")
async def get_medical_record(record_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-record/{record_id}", request=request)

@router.put("/medical-records/{record_id}")
async def update_medical_record(record_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['medical_record']}/medical-records/{record_id}", data=data, request=request)

@router.delete("/medical-records/{record_id}")
async def delete_medical_record(record_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['medical_record']}/medical-records/{record_id}", request=request)

@router.get("/medical-history/{profile_id}")
async def get_medical_history(profile_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['medical_record']}/medical-history/{profile_id}", request=request)

# <==========================================>
# Report API

@router.get("/reports/prescriptions-by-year/{year}")
async def get_prescriptions_report_by_year(
    year: int,
    request: Request
):
    """
    Simple prescription report by year
    - Get all medical records, filter by year
    - Get all prescriptions with medicines  
    - Merge by MaGiayKhamBenh
    - Only return medical records that have prescriptions
    """
    # Extract the Authorization header
    auth_header = request.headers.get("Authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Authorization token is missing")
    
    # Prepare headers for microservice requests
    headers = {"Authorization": auth_header}
    
    async with httpx.AsyncClient() as client:
        try:
            # Get all medical records
            medical_records_response = await client.get(
                f"{MICROSERVICE_URLS['medical_record']}/medical-records?skip=0&limit=10000",
                headers=headers,
                timeout=30.0
            )
            medical_records_response.raise_for_status()
            medical_records_data = medical_records_response.json()
            
            # Get all prescriptions with medicines
            prescriptions_response = await client.get(
                f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/with-medicines?skip=0&limit=10000",
                headers=headers,
                timeout=30.0
            )
            prescriptions_response.raise_for_status()
            prescriptions_data = prescriptions_response.json()
            
        except httpx.RequestError as e:
            raise HTTPException(status_code=500, detail=f"Request failed: {str(e)}")
        except httpx.HTTPStatusError as e:
            raise HTTPException(status_code=e.response.status_code, detail=e.response.text)
    
    try:
        from datetime import datetime
        
        # Extract data from responses
        medical_records = medical_records_data if isinstance(medical_records_data, list) else medical_records_data.get('data', [])
        prescriptions = prescriptions_data if isinstance(prescriptions_data, list) else prescriptions_data.get('data', [])
        # print(medical_records)
        # print(prescriptions)
        
        # Filter medical records by year
        filtered_medical_records = []
        for record in medical_records:
            # Get date from medical record
            record_date = None
            date_fields = ['NgayKham']
            
            for date_field in date_fields:
                if record.get(date_field):
                    record_date = record.get(date_field)
                    break
            
            if record_date:
                try:
                    # Parse date
                    if isinstance(record_date, str):
                        date_formats = ['%Y-%m-%d', '%d/%m/%Y', '%Y-%m-%d %H:%M:%S', '%d/%m/%Y %H:%M:%S']
                        parsed_date = None
                        for date_format in date_formats:
                            try:
                                parsed_date = datetime.strptime(record_date.split(' ')[0], date_format)
                                break
                            except ValueError:
                                continue
                    else:
                        parsed_date = record_date
                    
                    if parsed_date and parsed_date.year == year:
                        filtered_medical_records.append(record)
                        
                except (ValueError, TypeError):
                    continue
        
        # Create prescription lookup by MaGiayKhamBenh
        prescription_lookup = {}
        for prescription in prescriptions:
            ma_giay_kham_benh = prescription.get('MaGiayKhamBenh')
            if ma_giay_kham_benh:
                prescription_lookup[ma_giay_kham_benh] = prescription
        
        # Merge medical records with prescriptions
        merged_results = []
        for medical_record in filtered_medical_records:
            ma_giay_kham_benh = medical_record.get('MaGiayKhamBenh')
            
            # Only include if medical record has a prescription
            if ma_giay_kham_benh in prescription_lookup:
                merged_record = {
                    'medical_record': medical_record,
                    'prescription': prescription_lookup[ma_giay_kham_benh],
                    'MaGiayKhamBenh': ma_giay_kham_benh
                }
                merged_results.append(merged_record)
        
        return {
            'status': 'success',
            'year': year,
            'total_records': len(merged_results),
            'data': merged_results
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Data processing error: {str(e)}")

# <================================>
# Notifcation service route
@router.get("/user_notification/{user_id}")
async def get_medical_record(user_id: str, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['notification']}/user_notification/{user_id}", request=request)

@router.post("/send-email-and-store")
async def send_email_and_store(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['notification']}/send-email-and-store", data=data, request=request)

def setup_routes(app):
    app.include_router(router)
