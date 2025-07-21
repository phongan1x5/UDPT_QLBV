from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
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

# Create Medical Record - GiayKhamBenh
@router.post("/medical-records", response_model=schemas.GiayKhamBenhResponse)
def create_medical_record(record: schemas.GiayKhamBenhCreate, db: Session = Depends(get_db)):
    db_record = models.GiayKhamBenh(**record.dict())
    db.add(db_record)
    db.commit()
    db.refresh(db_record)
    return db_record

# Update Medical Record
@router.put("/medical-records/{record_id}", response_model=schemas.GiayKhamBenhResponse)
def update_medical_record(record_id: int, record: schemas.GiayKhamBenhCreate, db: Session = Depends(get_db)):
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