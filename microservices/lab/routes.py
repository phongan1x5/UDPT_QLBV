from fastapi import APIRouter, Depends, HTTPException, UploadFile, File, Form
from sqlalchemy.orm import Session
import os
import models, schemas, database
from rabbitmq import publish_event


router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

UPLOAD_DIR = "uploads/results"  # Directory to store uploaded files
os.makedirs(UPLOAD_DIR, exist_ok=True)  # Ensure the directory exists

@router.post("/services", response_model=schemas.DichVuResponse)
def create_service(service: schemas.DichVuCreate, db: Session = Depends(get_db)):
    db_service = models.DichVu(**service.dict())
    db.add(db_service)
    db.commit()
    db.refresh(db_service)
    return db_service

@router.get("/services", response_model=list[schemas.DichVuResponse])
def list_services(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.DichVu).offset(skip).limit(limit).all()

@router.get("/services/{service_id}", response_model=schemas.DichVuResponse)
def get_service(service_id: int, db: Session = Depends(get_db)):
    service = db.query(models.DichVu).filter(models.DichVu.MaDichVu == service_id).first()
    if not service:
        raise HTTPException(status_code=404, detail="Service not found")
    return service

@router.post("/used-services", response_model=schemas.DichVuSuDungResponse)
async def create_used_service(
    MaDichVu: int = Form(...),
    MaGiayKhamBenh: int = Form(...),
    ThoiGian: str = Form(...),
    db: Session = Depends(get_db)
):
    # Create the used service record without KetQua or FileKetQua
    db_used_service = models.DichVuSuDung(
        MaDichVu=MaDichVu,
        MaGiayKhamBenh=MaGiayKhamBenh,
        ThoiGian=ThoiGian,
        KetQua=None,  # Initially no result
        FileKetQua=None  # Initially no file
    )
    db.add(db_used_service)
    db.commit()
    db.refresh(db_used_service)
    return db_used_service

@router.put("/used-services/{used_service_id}", response_model=schemas.DichVuSuDungResponse)
async def update_used_service(
    used_service_id: int,
    KetQua: str = Form(...),
    file: UploadFile = File(None),
    db: Session = Depends(get_db)
):
    # Find the used service record
    db_used_service = db.query(models.DichVuSuDung).filter(models.DichVuSuDung.MaDVSD == used_service_id).first()
    if not db_used_service:
        raise HTTPException(status_code=404, detail="Used service not found")

    # Handle file upload
    file_path = None
    if file:
        file_path = os.path.join(UPLOAD_DIR, file.filename)
        with open(file_path, "wb") as f:
            f.write(await file.read())

    # Update the record with KetQua and FileKetQua
    db_used_service.KetQua = KetQua
    db_used_service.FileKetQua = file_path
    db.commit()
    db.refresh(db_used_service)

    # Publish an event to RabbitMQ
    publish_event("LabResultAvailable", f"UsedServiceID:{used_service_id}, Result:{KetQua}")

    return db_used_service

@router.get("/used-services", response_model=list[schemas.DichVuSuDungResponse])
def list_used_services(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.DichVuSuDung).offset(skip).limit(limit).all()

@router.get("/used-services/{used_service_id}", response_model=schemas.DichVuSuDungResponse)
def get_used_service(used_service_id: int, db: Session = Depends(get_db)):
    used_service = db.query(models.DichVuSuDung).filter(models.DichVuSuDung.MaDVSD == used_service_id).first()
    if not used_service:
        raise HTTPException(status_code=404, detail="Used service not found")
    return used_service