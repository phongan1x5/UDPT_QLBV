from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from datetime import datetime, time
import models, schemas, database
import httpx
from typing import Optional

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

async def create_medical_record(appointment_id: int, patient_id: int, doctor_id: int):
    """Create a medical record (GiayKhamBenh) for the appointment"""
    try:
        async with httpx.AsyncClient() as client:
            # Get the existing medical profile (HoSoBenhAn) for the patient
            try:
                profile_response = await client.get(
                    f"http://api_gateway:6000/medical-profiles/patient/{patient_id}",
                    timeout=10.0
                )
                
                if profile_response.status_code == 200:
                    profile_data = profile_response.json()
                    medical_profile_id = profile_data.get('MaHSBA')
                    print(f"Found medical profile {medical_profile_id} for patient {patient_id}")
                else:
                    print(f"Failed to get medical profile for patient {patient_id}: {profile_response.status_code}")
                    return None
                    
            except Exception as e:
                print(f"Error getting medical profile for patient {patient_id}: {str(e)}")
                return None
            
            # Create the medical record (GiayKhamBenh) with the existing MaHSBA
            if medical_profile_id:
                medical_record_data = {
                    "MaHSBA": medical_profile_id,  # Use the actual medical profile ID
                    "BacSi": doctor_id,  # Doctor ID (MaNhanVien)
                    "MaLichHen": appointment_id,  # Appointment ID
                    "NgayKham": datetime.now().strftime("%Y-%m-%d"),  # Examination date
                    "ChanDoan": "Pending examination",  # Initial diagnosis - will be updated during consultation
                    "LuuY": f"Medical record created for appointment #{appointment_id} on {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}"  # Notes
                }
                
                try:
                    record_response = await client.post(
                        "http://api_gateway:6000/medical-records",
                        json=medical_record_data,
                        timeout=10.0
                    )
                    
                    if record_response.status_code in [200, 201]:
                        record_result = record_response.json()
                        print(f"Successfully created medical record {record_result.get('MaGiayKhamBenh', 'Unknown')} for appointment {appointment_id}")
                        return record_result
                    else:
                        print(f"Failed to create medical record: {record_response.status_code} - {record_response.text}")
                        return None
                        
                except Exception as e:
                    print(f"Error creating medical record: {str(e)}")
                    return None
            else:
                print(f"No medical profile ID found for patient {patient_id}")
                return None
                
    except Exception as e:
        print(f"Error in create_medical_record: {str(e)}")
        return None

# Now update the create_appointment function to use the medical record creation
@router.post("/appointments", response_model=schemas.AppointmentResponse)
async def create_appointment(appointment: schemas.AppointmentCreate, db: Session = Depends(get_db)):
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
    
    # Create medical record for this appointment
    try:
        medical_record = await create_medical_record(
            appointment_id=db_appointment.MaLichHen,
            patient_id=db_appointment.MaBenhNhan,
            doctor_id=db_appointment.MaBacSi
        )
        
        if medical_record:
            print(f"Medical record created successfully for appointment {db_appointment.MaLichHen}")
            print(f"Medical record ID: {medical_record.get('MaGiayKhamBenh', 'Unknown')}")
        else:
            print(f"Warning: Failed to create medical record for appointment {db_appointment.MaLichHen}")
            # Note: We don't fail the appointment creation if medical record creation fails
            
    except Exception as e:
        print(f"Error creating medical record for appointment {db_appointment.MaLichHen}: {str(e)}")
        # Continue with appointment creation even if medical record fails
    
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
    print(appointment_update)
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

@router.get("/appointments/available-slots/{doctor_id}/{date}")
def get_available_slots(doctor_id: int, date: str, db: Session = Depends(get_db)):
    """
    Get available time slots for a doctor on a specific date
    Returns all valid time slots that are not already booked
    """
    try:
        # Parse and validate the date
        appointment_date = datetime.strptime(date, "%Y-%m-%d")
        
        # Check if it's a valid day (Monday to Friday)
        if not is_valid_day(appointment_date):
            raise HTTPException(
                status_code=400, 
                detail="Appointments are only available Monday to Friday."
            )
        
        # Check if the date is in the future
        today = datetime.now().date()
        if appointment_date.date() <= today:
            raise HTTPException(
                status_code=400,
                detail="Appointments can only be booked for future dates."
            )
        
        # Get all booked appointments for this doctor on this date
        booked_appointments = db.query(models.Appointment).filter(
            models.Appointment.MaBacSi == doctor_id,
            models.Appointment.Ngay == date,
            models.Appointment.TrangThai.in_(["ChoXacNhan", "DaXacNhan"])  # Only active appointments
        ).all()
        
        # Extract booked time slots
        booked_times = {appointment.Gio for appointment in booked_appointments}
        
        # Define all valid time slots
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
        
        # Filter out booked slots and format for response
        available_slots = []
        for slot in valid_slots:
            if slot not in booked_times:
                # Format time for display
                display_time = slot.strftime("%I:%M %p")  # e.g., "02:30 PM"
                api_time = slot.strftime("%H:%M")  # e.g., "14:30" for API
                
                available_slots.append({
                    "time": api_time,
                    "display_time": display_time,
                    "available": True
                })
        
        return {
            "status": 200,
            "data": available_slots,
            "doctor_id": doctor_id,
            "date": date,
            "total_available": len(available_slots),
            "total_booked": len(booked_times)
        }
        
    except ValueError:
        raise HTTPException(
            status_code=400,
            detail="Invalid date format. Please use YYYY-MM-DD format."
        )
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Error retrieving available slots: {str(e)}"
        )

@router.get("/appointments/available-slots/{doctor_id}/{date}/summary")
def get_slots_summary(doctor_id: int, date: str, db: Session = Depends(get_db)):
    """
    Get a summary of available vs booked slots for a doctor on a specific date
    """
    try:
        appointment_date = datetime.strptime(date, "%Y-%m-%d")
        
        if not is_valid_day(appointment_date):
            return {
                "status": 400,
                "message": "No appointments available on weekends",
                "available_slots": 0,
                "booked_slots": 0,
                "total_slots": 0
            }
        
        # Get booked appointments
        booked_count = db.query(models.Appointment).filter(
            models.Appointment.MaBacSi == doctor_id,
            models.Appointment.Ngay == date,
            models.Appointment.TrangThai.in_(["scheduled", "confirmed"])
        ).count()
        
        total_slots = 32  # Total number of available time slots per day
        available_slots = total_slots - booked_count
        
        return {
            "status": 200,
            "doctor_id": doctor_id,
            "date": date,
            "available_slots": available_slots,
            "booked_slots": booked_count,
            "total_slots": total_slots,
            "availability_percentage": round((available_slots / total_slots) * 100, 2)
        }
        
    except ValueError:
        raise HTTPException(status_code=400, detail="Invalid date format.")
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))