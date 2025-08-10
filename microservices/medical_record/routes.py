from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import and_
import models, schemas, database

router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Create Medical Profile - HoSoBenhAn
@router.post("/medical-profiles", response_model=schemas.HoSoBenhAnResponse)
def create_medical_profile(profile: schemas.HoSoBenhAnCreate, db: Session = Depends(get_db)):
    db_profile = models.HoSoBenhAn(**profile.dict())
    db.add(db_profile)
    db.commit()
    db.refresh(db_profile)
    return db_profile

# Update Medical Profile
@router.put("/medical-profiles/{profile_id}", response_model=schemas.HoSoBenhAnResponse)
def update_medical_profile(profile_id: int, profile: schemas.HoSoBenhAnCreate, db: Session = Depends(get_db)):
    db_profile = db.query(models.HoSoBenhAn).filter(models.HoSoBenhAn.MaHSBA == profile_id).first()
    if not db_profile:
        raise HTTPException(status_code=404, detail="Medical profile not found")
    for key, value in profile.dict().items():
        setattr(db_profile, key, value)
    db.commit()
    db.refresh(db_profile)
    return db_profile

# Delete Medical Profile (set is_active to False)
@router.delete("/medical-profiles/{profile_id}", response_model=schemas.HoSoBenhAnResponse)
def delete_medical_profile(profile_id: int, db: Session = Depends(get_db)):
    db_profile = db.query(models.HoSoBenhAn).filter(models.HoSoBenhAn.MaHSBA == profile_id).first()
    if not db_profile:
        raise HTTPException(status_code=404, detail="Medical profile not found")
    db_profile.is_active = False
    db.commit()
    db.refresh(db_profile)
    return db_profile

@router.get("/medical-profiles/{profile_id}")
def get_medical_profile(profile_id: int, db: Session = Depends(get_db)):
    # Get Medical Profile
    profile = db.query(models.HoSoBenhAn).filter(models.HoSoBenhAn.MaHSBA == profile_id).first()
    if not profile:
        raise HTTPException(status_code=404, detail="Medical profile not found")

    return {
        "MedicalProfile": profile,
    }

# Create Medical Record - GiayKhamBenh
@router.post("/medical-records", response_model=schemas.GiayKhamBenhResponse)
def create_medical_record(record: schemas.GiayKhamBenhCreate, db: Session = Depends(get_db)):
    db_record = models.GiayKhamBenh(**record.dict())
    db.add(db_record)
    db.commit()
    db.refresh(db_record)
    return db_record

@router.get("/medical-record/byAppointmentId/{appointmentId}")
def get_medical_record_by_appointmentId(appointmentId: int, db: Session = Depends(get_db)):
    # Get Medical Profile
    record = db.query(models.GiayKhamBenh).filter(models.GiayKhamBenh.MaLichHen == appointmentId).first()
    if not record:
        raise HTTPException(status_code=404, detail="Medical profile not found")

    return {
        "MedicalRecord": record,
    }

# This is for doctors to get their patient medical records.
@router.get("/medical-record/byDoctorId/{doctorId}")
def get_medical_record_by_doctorId(doctorId: int, db: Session = Depends(get_db)):
    record = db.query(models.GiayKhamBenh).filter(and_(models.GiayKhamBenh.BacSi == doctorId,
            models.GiayKhamBenh.ChanDoan != '@Pending examination'
        )
    ).all()
    if not record:
        raise HTTPException(status_code=404, detail="Medical profile not found")

    return {
        "MedicalRecord": record,
    }

@router.get("/medical-record/{record_id}")
def get_medical_record(record_id: int, db: Session = Depends(get_db)):
    # Get Medical Profile
    record = db.query(models.GiayKhamBenh).filter(models.GiayKhamBenh.MaGiayKhamBenh == record_id).first()
    if not record:
        raise HTTPException(status_code=404, detail="Medical profile not found")

    return {
        "MedicalRecord": record,
    }

# Update Medical Record
@router.put("/medical-records/{record_id}", response_model=schemas.GiayKhamBenhResponse)
def update_medical_record(record_id: int, record: schemas.GiayKhamBenhUpdate, db: Session = Depends(get_db)):
    db_record = db.query(models.GiayKhamBenh).filter(models.GiayKhamBenh.MaGiayKhamBenh == record_id).first()
    if not db_record:
        raise HTTPException(status_code=404, detail="Medical record not found")
    for key, value in record.dict().items():
        setattr(db_record, key, value)
    db.commit()
    db.refresh(db_record)
    return db_record

# Delete Medical Record
@router.delete("/medical-records/{record_id}", response_model=schemas.GiayKhamBenhResponse)
def delete_medical_record(record_id: int, db: Session = Depends(get_db)):
    db_record = db.query(models.GiayKhamBenh).filter(models.GiayKhamBenh.MaGiayKhamBenh == record_id).first()
    if not db_record:
        raise HTTPException(status_code=404, detail="Medical record not found")
    db.delete(db_record)
    db.commit()
    return db_record

# Get Medical History (Medical Profile + Medical Records)
@router.get("/medical-history/{profile_id}")
def get_medical_history(profile_id: int, db: Session = Depends(get_db)):
    # Get Medical Profile
    profile = db.query(models.HoSoBenhAn).filter(models.HoSoBenhAn.MaHSBA == profile_id).first()
    if not profile:
        raise HTTPException(status_code=404, detail="Medical profile not found")

    # Query Medical Records
    medical_records = db.query(models.GiayKhamBenh).filter(models.GiayKhamBenh.MaHSBA == profile_id).all()

    return {
        "MedicalProfile": profile,
        "MedicalRecords": medical_records
    }

@router.get("/medical-profiles")
def list_medical_profiles(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    """
    Get all medical profiles with pagination
    """
    profiles = db.query(models.HoSoBenhAn).filter(
        models.HoSoBenhAn.is_active == True
    ).offset(skip).limit(limit).all()
    
    total_count = db.query(models.HoSoBenhAn).filter(
        models.HoSoBenhAn.is_active == True
    ).count()
    
    return {
        "data": profiles,
        "total": total_count,
        "skip": skip,
        "limit": limit
    }

@router.get("/medical-records")
def list_medical_records(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    """
    Get all medical records with pagination
    """
    records = db.query(models.GiayKhamBenh).offset(skip).limit(limit).all()
    
    total_count = db.query(models.GiayKhamBenh).count()
    
    return {
        "data": records,
        "total": total_count,
        "skip": skip,
        "limit": limit
    }