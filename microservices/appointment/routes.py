from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from datetime import datetime, time
import models, schemas, database

router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Helper function to check if a time slot is valid
def is_valid_time_slot(appointment_time: time):
    valid_slots = [
        time(hour=7, minute=30), time(hour=7, minute=45), time(hour=8, minute=0),
        time(hour=8, minute=15), time(hour=8, minute=30), time(hour=8, minute=45),
        time(hour=9, minute=0), time(hour=9, minute=15), time(hour=9, minute=30),
        time(hour=9, minute=45), time(hour=10, minute=0), time(hour=10, minute=15),
        time(hour=10, minute=30), time(hour=10, minute=45), time(hour=11, minute=0),
        time(hour=11, minute=15), time(hour=13, minute=30), time(hour=13, minute=45),
        time(hour=14, minute=0), time(hour=14, minute=15), time(hour=14, minute=30),
        time(hour=14, minute=45), time(hour=15, minute=0), time(hour=15, minute=15),
        time(hour=15, minute=30), time(hour=15, minute=45), time(hour=16, minute=0),
        time(hour=16, minute=15), time(hour=16, minute=30), time(hour=16, minute=45),
        time(hour=17, minute=0), time(hour=17, minute=15)
    ]
    return appointment_time in valid_slots

# Helper function to check if a day is valid for a doctor
def is_valid_day(appointment_date: datetime):
    weekday = appointment_date.weekday()  # Monday = 0, Sunday = 6
    return weekday in range(0, 5)  # Monday to Friday

@router.post("/appointments", response_model=schemas.AppointmentResponse)
def create_appointment(appointment: schemas.AppointmentCreate, db: Session = Depends(get_db)):
    # Validate appointment date and time
    appointment_date = datetime.strptime(appointment.Ngay, "%Y-%m-%d")
    appointment_time = datetime.strptime(appointment.Gio, "%H:%M:%S").time()

    if not is_valid_day(appointment_date):
        raise HTTPException(status_code=400, detail="Doctors only work Monday to Friday.")

    if not is_valid_time_slot(appointment_time):
        raise HTTPException(status_code=400, detail="Invalid time slot. Appointments must be scheduled in 15-minute intervals.")

    # Check if the slot is already occupied
    existing_appointment = db.query(models.Appointment).filter(
        models.Appointment.MaBacSi == appointment.MaBacSi,
        models.Appointment.Ngay == appointment.Ngay,
        models.Appointment.Gio == appointment.Gio
    ).first()

    if existing_appointment:
        raise HTTPException(status_code=400, detail="This time slot is already occupied.")

    # Create the appointment
    db_appointment = models.Appointment(**appointment.model_dump())
    db.add(db_appointment)
    db.commit()
    db.refresh(db_appointment)
    return db_appointment

@router.get("/appointments", response_model=list[schemas.AppointmentResponse])
def list_appointments(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.Appointment).offset(skip).limit(limit).all()

@router.get("/appointments/patient/{patient_id}", response_model=list[schemas.AppointmentResponse])
def get_appointments_for_patient(patient_id: int, db: Session = Depends(get_db)):
    """Get all appointments for a specific patient"""
    appointments = db.query(models.Appointment).filter(models.Appointment.MaBenhNhan == patient_id).all()
    
    if not appointments:
        # You can choose to return empty list or raise an exception
        # For better UX, returning empty list is usually better
        return []
    
    return appointments

@router.get("/appointments/{appointment_id}", response_model=schemas.AppointmentResponse)
def get_appointment(appointment_id: int, db: Session = Depends(get_db)):
    appointment = db.query(models.Appointment).filter(models.Appointment.MaLichHen == appointment_id).first()
    if not appointment:
        raise HTTPException(status_code=404, detail="Appointment not found")
    return appointment

@router.put("/appointments/{appointment_id}", response_model=schemas.AppointmentResponse)
def update_appointment(appointment_id: int, appointment_update: schemas.AppointmentUpdate, db: Session = Depends(get_db)):
    appointment = db.query(models.Appointment).filter(models.Appointment.MaLichHen == appointment_id).first()
    if not appointment:
        raise HTTPException(status_code=404, detail="Appointment not found")

    # Validate updated date and time
    if appointment_update.Ngay or appointment_update.Gio:
        updated_date = datetime.strptime(appointment_update.Ngay, "%Y-%m-%d") if appointment_update.Ngay else appointment.Ngay
        updated_time = datetime.strptime(appointment_update.Gio, "%H:%M:%S").time() if appointment_update.Gio else appointment.Gio

        if not is_valid_day(updated_date):
            raise HTTPException(status_code=400, detail="Doctors only work Monday to Friday.")

        if not is_valid_time_slot(updated_time):
            raise HTTPException(status_code=400, detail="Invalid time slot. Appointments must be scheduled in 15-minute intervals.")

        # Check if the updated slot is already occupied
        existing_appointment = db.query(models.Appointment).filter(
            models.Appointment.MaBacSi == appointment.MaBacSi,
            models.Appointment.Ngay == updated_date,
            models.Appointment.Gio == updated_time,
            models.Appointment.MaLichHen != appointment_id
        ).first()

        if existing_appointment:
            raise HTTPException(status_code=400, detail="This time slot is already occupied.")

    # Update the appointment
    for var, value in vars(appointment_update).items():
        if value is not None:
            setattr(appointment, var, value)
    db.commit()
    db.refresh(appointment)
    return appointment

@router.delete("/appointments/{appointment_id}", status_code=204)
def delete_appointment(appointment_id: int, db: Session = Depends(get_db)):
    appointment = db.query(models.Appointment).filter(models.Appointment.MaLichHen == appointment_id).first()
    if not appointment:
        raise HTTPException(status_code=404, detail="Appointment not found")
    db.delete(appointment)
    db.commit()
    return None

@router.get("/appointments/doctor/{doctor_id}", response_model=list[schemas.AppointmentResponse])
def get_appointments_for_doctor(doctor_id: int, db: Session = Depends(get_db)):
    return db.query(models.Appointment).filter(models.Appointment.MaBacSi == doctor_id).all()