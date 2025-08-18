from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
import models, schemas, database
from typing import List
import requests
from rabbitmq import publish_event
import json
import secrets

router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

@router.post("/patients", response_model=schemas.PatientResponse)
def create_patient(patient: schemas.PatientCreate, db: Session = Depends(get_db)):
    # Check if a patient with the same SoDinhDanh exists
    existing_patient = db.query(models.Patient).filter(models.Patient.SoDinhDanh == patient.SoDinhDanh).first()
    if existing_patient:
        raise HTTPException(
            status_code=400,
            detail=f"A patient with the SoDinhDanh {patient.SoDinhDanh} already exists."
        )

    # Create the patient in the database
    db_patient = models.Patient(**patient.model_dump(exclude={"Password", "TienSu"})) #Database patient khong giu password

    db.add(db_patient)
    db.commit()
    db.refresh(db_patient)

    #Replace input password with a secure random UID
    patient.Password = secrets.token_hex(3)

    # Generate user_id for the auth service
    user_id = f"BN{db_patient.id}"

    # Prepare the payload for the auth service
    auth_payload = {
        "id": user_id,
        "password": patient.Password,
        "role": "patient"
    }

    HoSoBenhAn_payload = {
        "MaBenhNhan": int(user_id.replace("BN", "", 1)),
        "TienSu": patient.TienSu
    }
    print(HoSoBenhAn_payload)

    # Make a request to the auth service to create the user
    auth_service_url = "http://api_gateway:6000/auth/register"
    medicalRecord_service_url = "http://api_gateway:6000/medical-profiles"
    
    try:
        response = requests.post(auth_service_url, json=auth_payload)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        # Rollback the patient creation if the auth service fails
        db.delete(db_patient)
        db.commit()
        raise HTTPException(
            status_code=500,
            detail=f"Failed to create user in auth service: {str(e)}"
        )
    
    try:
        response = requests.post(medicalRecord_service_url, json=HoSoBenhAn_payload)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        # Rollback the patient creation if the auth service fails
        db.delete(db_patient)
        db.commit()
        raise HTTPException(
            status_code=500,
            detail=f"Failed to create user HSBA: {str(e)}"
        )
    
    # 🔧 Send welcome email with login credentials via RabbitMQ (Updated format)
    try:
        # Prepare welcome email message
        welcome_message = f"""Welcome to Hospital Management System!

Dear {db_patient.HoTen},

Your patient account has been successfully created. Here are your login credentials:

🆔 User ID: {user_id}
🔑 Password: {patient.Password}

You can use these credentials to:
• View your medical records
• Check lab results
• Schedule appointments
• Access prescription information

Please keep these credentials safe and change your password after your first login.

For any questions, please contact our support team.

Best regards,
Hospital Management Team"""

        payload = f"UserID:BN{user_id}, UserEmail:{db_patient.Email}, SourceSystem:Patient Registration, Message:{welcome_message}"

        # Publish using the same pattern as appointment service
        publish_event("patient_welcome_email", payload)
        
        print(f"✅ Welcome email queued for patient {user_id} ({db_patient.Email})")

    except Exception as e:
        # Don't fail the entire registration if email fails
        print(f"⚠️ Warning: Failed to queue welcome email for patient {user_id}: {str(e)}")
        # Could optionally log this to a monitoring system

    return db_patient

@router.get("/patients", response_model=List[schemas.MaskedPatientResponse])
def list_patients(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    patients = db.query(models.Patient).offset(skip).limit(limit).all()
    return [patient.masked() for patient in patients]

@router.get("/patients/by_phone/{patient_phone}", response_model=schemas.PatientResponse)
def get_patient_by_phone(patient_phone: str, db: Session = Depends(get_db)):
    patient = db.query(models.Patient).filter(models.Patient.SoDienThoai == patient_phone).first()
    if not patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    return patient.masked()

@router.get("/patients/{patient_id}", response_model=schemas.PatientResponse)
def get_patient(patient_id: int, db: Session = Depends(get_db)):
    patient = db.query(models.Patient).filter(models.Patient.id == patient_id).first()
    if not patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    return patient

@router.get("/patients/forDoctor/{patient_id}", response_model=schemas.MaskedPatientResponse)
def get_patient_for_doctor(patient_id: int, db: Session = Depends(get_db)):
    patient = db.query(models.Patient).filter(models.Patient.id == patient_id).first()
    if not patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    return patient.masked()

@router.put("/patients/{patient_id}", response_model=schemas.PatientResponse)
def update_patient(patient_id: int, patient_update: schemas.PatientUpdate, db: Session = Depends(get_db)):
    patient = db.query(models.Patient).filter(models.Patient.id == patient_id).first()
    if not patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    for var, value in vars(patient_update).items():
        if value is not None:
            setattr(patient, var, value)
    db.commit()
    db.refresh(patient)
    return patient

@router.delete("/patients/{patient_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_patient(patient_id: int, db: Session = Depends(get_db)):
    #Instead of actually deleting a patient, we just unactive that patient
    patient = db.query(models.Patient).filter(models.Patient.id == patient_id).first()
    if not patient:
        raise HTTPException(status_code=404, detail="Patient not found")
    patient.is_active = False
    db.commit()
    db.refresh(patient)
    return None
