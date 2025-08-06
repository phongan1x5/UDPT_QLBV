from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
import models, schemas, database
from typing import List

router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

# === MEDICINE ROUTES ===
@router.post("/medicines", response_model=schemas.ThuocResponse)
def create_medicine(medicine: schemas.ThuocCreate, db: Session = Depends(get_db)):
    db_medicine = models.Thuoc(**medicine.dict())
    db.add(db_medicine)
    db.commit()
    db.refresh(db_medicine)
    return db_medicine

@router.put("/medicines/{medicine_id}")
def update_medicine(medicine_id: int, medicine: schemas.ThuocUpdate, db: Session = Depends(get_db)):    
    db_medicine = db.query(models.Thuoc).filter(models.Thuoc.MaThuoc == medicine_id).first()
    if not db_medicine:
        raise HTTPException(status_code=404, detail="Medicine not found")
    
    for key, value in medicine.dict().items():
        setattr(db_medicine, key, value)
    
    db.commit()
    db.refresh(db_medicine)
    return db_medicine

@router.get("/medicines", response_model=List[schemas.ThuocResponse])
def list_medicines(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.Thuoc).offset(skip).limit(limit).all()

@router.get("/medicines/{medicine_id}", response_model=schemas.ThuocResponse)
def get_medicine(medicine_id: int, db: Session = Depends(get_db)):
    medicine = db.query(models.Thuoc).filter(models.Thuoc.MaThuoc == medicine_id).first()
    if not medicine:
        raise HTTPException(status_code=404, detail="Medicine not found")
    return medicine

# === PRESCRIPTION ROUTES ===
@router.post("/prescriptions", response_model=schemas.ToaThuocResponse)  # Simple response
def create_prescription(prescription: schemas.ToaThuocCreate, db: Session = Depends(get_db)):
    try:
        # Create the prescription first
        db_prescription = models.ToaThuoc(
            MaGiayKhamBenh=prescription.MaGiayKhamBenh,
            TrangThaiToaThuoc="Active"
        )
        db.add(db_prescription)
        db.commit()
        db.refresh(db_prescription)

        # Add medicines to prescription
        for medicine in prescription.ThuocTheoToa:
            # Verify medicine exists
            medicine_exists = db.query(models.Thuoc).filter(models.Thuoc.MaThuoc == medicine.MaThuoc).first()
            if not medicine_exists:
                raise HTTPException(status_code=404, detail=f"Medicine with ID {medicine.MaThuoc} not found")
                
            db_medicine_in_prescription = models.ThuocTheoToa(
                MaToaThuoc=db_prescription.MaToaThuoc,
                MaThuoc=medicine.MaThuoc,
                SoLuong=medicine.SoLuong,
                GhiChu=medicine.GhiChu
            )
            db.add(db_medicine_in_prescription)

        db.commit()
        return db_prescription  # Return simple prescription object
        
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=500, detail=f"Error creating prescription: {str(e)}")

@router.get("/prescriptions", response_model=List[schemas.ToaThuocResponse])  # Simple response
def list_prescriptions(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.ToaThuoc).offset(skip).limit(limit).all()

@router.get("/prescriptions/{prescription_id}", response_model=schemas.ToaThuocResponse)  # Simple response
def get_prescription(prescription_id: int, db: Session = Depends(get_db)):
    prescription = db.query(models.ToaThuoc).filter(models.ToaThuoc.MaToaThuoc == prescription_id).first()
    if not prescription:
        raise HTTPException(status_code=404, detail="Prescription not found")
    return prescription

# Custom route for detailed prescription with medicines (no response model)
@router.get("/prescriptions/medical-record/{medical_record_id}")
def get_prescription_by_medical_record(medical_record_id: int, db: Session = Depends(get_db)):
    """Get the prescription for a specific medical record with medicine details"""
    prescription = db.query(models.ToaThuoc).filter(
        models.ToaThuoc.MaGiayKhamBenh == medical_record_id
    ).first()
    
    if not prescription:
        raise HTTPException(status_code=404, detail=f"No prescription found for medical record {medical_record_id}")
    
    # Get medicines for this prescription
    medicines = db.query(models.ThuocTheoToa, models.Thuoc).join(
        models.Thuoc, models.ThuocTheoToa.MaThuoc == models.Thuoc.MaThuoc
    ).filter(models.ThuocTheoToa.MaToaThuoc == prescription.MaToaThuoc).all()
    
    prescription_data = {
        "MaToaThuoc": prescription.MaToaThuoc,
        "MaGiayKhamBenh": prescription.MaGiayKhamBenh,
        "TrangThaiToaThuoc": prescription.TrangThaiToaThuoc,
        "Medicines": [
            {
                "MaThuoc": medicine.Thuoc.MaThuoc,
                "TenThuoc": medicine.Thuoc.TenThuoc,
                "DonViTinh": medicine.Thuoc.DonViTinh,
                "SoLuong": medicine.ThuocTheoToa.SoLuong,
                "GhiChu": medicine.ThuocTheoToa.GhiChu,
                "GiaTien": medicine.Thuoc.GiaTien
            }
            for medicine in medicines
        ]
    }
    
    return prescription_data

@router.put("/prescriptions/{prescription_id}/status")
def update_prescription_status(prescription_id: int, request_data: dict, db: Session = Depends(get_db)):
    """Update prescription status"""
    prescription = db.query(models.ToaThuoc).filter(models.ToaThuoc.MaToaThuoc == prescription_id).first()
    if not prescription:
        raise HTTPException(status_code=404, detail="Prescription not found")
    
    prescription.TrangThaiToaThuoc = request_data.get("status", prescription.TrangThaiToaThuoc)
    db.commit()
    db.refresh(prescription)
    return prescription