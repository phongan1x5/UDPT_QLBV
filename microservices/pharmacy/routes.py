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

@router.post("/medicines", response_model=schemas.ThuocResponse)
def create_medicine(medicine: schemas.ThuocCreate, db: Session = Depends(get_db)):
    db_medicine = models.Thuoc(**medicine.dict())
    db.add(db_medicine)
    db.commit()
    db.refresh(db_medicine)
    return db_medicine

@router.get("/medicines", response_model=List[schemas.ThuocResponse])
def list_medicines(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.Thuoc).offset(skip).limit(limit).all()

@router.post("/prescriptions", response_model=schemas.ToaThuocResponse)
def create_prescription(prescription: schemas.ToaThuocCreate, db: Session = Depends(get_db)):
    db_prescription = models.ToaThuoc(
        MaGiayKhamBenh=prescription.MaGiayKhamBenh,
        TrangThaiToaThuoc=prescription.TrangThaiToaThuoc
    )
    db.add(db_prescription)
    db.commit()
    db.refresh(db_prescription)

    for medicine in prescription.ThuocTheoToa:
        db_medicine_in_prescription = models.ThuocTheoToa(
            MaToaThuoc=db_prescription.MaToaThuoc,
            MaThuoc=medicine.MaThuoc,
            SoLuong=medicine.SoLuong,
            GhiChu=medicine.GhiChu
        )
        db.add(db_medicine_in_prescription)

    db.commit()
    return db_prescription

@router.get("/prescriptions/{prescription_id}", response_model=schemas.ToaThuocResponse)
def get_prescription(prescription_id: int, db: Session = Depends(get_db)):
    prescription = db.query(models.ToaThuoc).filter(models.ToaThuoc.MaToaThuoc == prescription_id).first()
    if not prescription:
        raise HTTPException(status_code=404, detail="Prescription not found")
    return prescription