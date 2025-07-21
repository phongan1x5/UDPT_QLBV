from fastapi import APIRouter, Request, Depends, HTTPException
import httpx
from database import MICROSERVICE_URLS

router = APIRouter()

# Helper function
async def forward_request(method: str, url: str, data=None, headers=None, require_auth=True, request: Request = None):
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

@router.get("/patients/{patient_id}")
async def get_patient(patient_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['patient']}/patients/{patient_id}", request=request)

# Staff_service
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

@router.post("/lab/used-services")
async def create_used_service(request: Request):
    form_data = await request.form()
    file = form_data.get("file")
    data = {key: form_data[key] for key in form_data if key != "file"}
    return await forward_request("POST", f"{MICROSERVICE_URLS['lab']}/used-services", data=data, request=request)

@router.get("/lab/used-services")
async def list_used_services(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/used-services?skip={skip}&limit={limit}", request=request)

@router.get("/lab/used-services/{used_service_id}")
async def get_used_service(used_service_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['lab']}/used-services/{used_service_id}", request=request)

# Pharmacy_service
@router.post("/pharmacy/medicines")
async def create_medicine(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['pharmacy']}/medicines", data=data, request=request)

@router.get("/pharmacy/medicines")
async def list_medicines(request: Request, skip: int = 0, limit: int = 100):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/medicines?skip={skip}&limit={limit}", request=request)

@router.post("/pharmacy/prescriptions")
async def create_prescription(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions", data=data, request=request)

@router.get("/pharmacy/prescriptions/{prescription_id}")
async def get_prescription(prescription_id: int, request: Request):
    return await forward_request("GET", f"{MICROSERVICE_URLS['pharmacy']}/prescriptions/{prescription_id}", request=request)

# MedicalRecord_service
@router.post("/medical-profiles")
async def create_medical_profile(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles", data=data, request=request)

@router.put("/medical-profiles/{profile_id}")
async def update_medical_profile(profile_id: int, request: Request):
    data = await request.json()
    return await forward_request("PUT", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles/{profile_id}", data=data, request=request)

@router.delete("/medical-profiles/{profile_id}")
async def delete_medical_profile(profile_id: int, request: Request):
    return await forward_request("DELETE", f"{MICROSERVICE_URLS['medical_record']}/medical-profiles/{profile_id}", request=request)

@router.post("/medical-records")
async def create_medical_record(request: Request):
    data = await request.json()
    return await forward_request("POST", f"{MICROSERVICE_URLS['medical_record']}/medical-records", data=data, request=request)

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

def setup_routes(app):
    app.include_router(router)
