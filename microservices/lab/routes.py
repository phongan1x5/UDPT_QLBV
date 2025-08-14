from fastapi import APIRouter, Depends, HTTPException, UploadFile, File, Form
from fastapi.responses import FileResponse
from sqlalchemy.orm import Session
import os
import models, schemas, database
from rabbitmq import publish_event
from fastapi.responses import FileResponse
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
def create_used_service(used_service: schemas.DichVuSuDungCreate, db: Session = Depends(get_db)):
    # Create the used service record without KetQua or FileKetQua (initially)
    db_used_service = models.DichVuSuDung(
        MaDichVu=used_service.MaDichVu,
        MaGiayKhamBenh=used_service.MaGiayKhamBenh,
        ThoiGian=used_service.ThoiGian,
        YeuCauCuThe = used_service.YeuCauCuThe,
        KetQua=None,  # Initially no result
        FileKetQua=None,  # Initially no file
        TrangThai="ChoThuTien" #Initially is not paid yet.
    )
    db.add(db_used_service)
    db.commit()
    db.refresh(db_used_service)
    return db_used_service

# Paid for all service of a medical record
@router.put("/used-services/paid_medical_record/{medicalRecordId}", response_model=list[schemas.DichVuSuDungResponse])
async def paid_used_service_medical_record(
    medicalRecordId: int,
    db: Session = Depends(get_db)
):
    # Fetch all services with the given medical record ID
    services = db.query(models.DichVuSuDung).filter(
        models.DichVuSuDung.MaGiayKhamBenh == medicalRecordId
    ).all()

    # Update their status
    for service in services:
        service.TrangThai = "DaThuTien"

    # Commit the changes
    db.commit()

    # Optionally refresh the instances if needed
    for service in services:
        db.refresh(service)

    return services

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
    db_used_service.TrangThai = "DaCoKetQua"  # Update status to indicate results are available
    db.commit()
    db.refresh(db_used_service)

    # 🔥 NEW: Publish event to RabbitMQ for notification service
    try:
        # We need to get patient info - you'll need to modify this based on your patient data structure
        # For now, we'll use placeholder values
        patient_email = get_patient_email_by_medical_record(db_used_service.MaGiayKhamBenh, db)
        patient_id = get_patient_id_by_medical_record(db_used_service.MaGiayKhamBenh, db)
        
        if patient_email and patient_id:
            # Create notification message
            notification_message = f"🧪 Your lab results are ready! Service ID: {used_service_id}. Result: {KetQua}"
            
            # Publish event to RabbitMQ
            payload = f"UserID:BN{patient_id}, UserEmail:{patient_email}, SourceSystem:Laboratory, Message:{notification_message}"
            publish_event("LabResultAvailable", payload)
            print(f"✅ Published lab result notification for patient {patient_id}")
        else:
            print(f"⚠️ Could not find patient info for medical record {db_used_service.MaGiayKhamBenh}")
            
    except Exception as e:
        print(f"❌ Error publishing notification event: {e}")
        # Don't fail the lab result update if notification fails

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

@router.get("/used-services/results/{filename}")
def secure_file(filename: str):
    print("UPLOAD_DIR =", UPLOAD_DIR)

    file_path = os.path.join(UPLOAD_DIR, filename)
    if not os.path.exists(file_path):
        raise HTTPException(404, "Not found")
    # insert auth check here if needed
    return FileResponse(file_path, media_type="application/pdf")

@router.get("/used-services/medical-record/{medical_record_id}")
def get_used_services_by_medical_record(medical_record_id: int, db: Session = Depends(get_db)):
    """Get all lab services used for a specific medical record"""
    used_services = db.query(models.DichVuSuDung).filter(
        models.DichVuSuDung.MaGiayKhamBenh == medical_record_id
    ).all()
    
    if not used_services:
        raise HTTPException(status_code=404, detail=f"No lab services found for medical record {medical_record_id}")
    
    # Get service details for each used service
    result = []
    for used_service in used_services:
        # Get the service details
        service = db.query(models.DichVu).filter(
            models.DichVu.MaDichVu == used_service.MaDichVu
        ).first()
        
        service_data = {
            "MaDVSD": used_service.MaDVSD,
            "MaDichVu": used_service.MaDichVu,
            "MaGiayKhamBenh": used_service.MaGiayKhamBenh,
            "ThoiGian": used_service.ThoiGian,
            "KetQua": used_service.KetQua,
            "FileKetQua": used_service.FileKetQua,
            "TrangThai": used_service.TrangThai,
            "Service": {
                "TenDichVu": service.TenDichVu if service else "Unknown Service",
                "NoiDungDichVu": service.NoiDungDichVu if service else "",
                "DonGia": service.DonGia if service else 0
            } if service else None
        }
        result.append(service_data)
    
    return result

#Remove a DVSD
@router.delete("/used-services/{used_service_id}", response_model=schemas.DichVuSuDungResponse)
async def delete_used_service(
    used_service_id: int,
    db: Session = Depends(get_db)
):
    # Find the service by ID
    service = db.query(models.DichVuSuDung).filter(
        models.DichVuSuDung.MaDVSD == used_service_id
    ).first()

    # If not found, raise an error
    if not service:
        raise HTTPException(status_code=404, detail="Used service not found")

    # Delete the service
    db.delete(service)
    db.commit()

    return service

@router.get("/download/{used_service_id}")
async def download_result_file(used_service_id: int, db: Session = Depends(get_db)):
    """
    Download lab result file by service ID
    """
    # Find the used service record
    used_service = db.query(models.DichVuSuDung).filter(
        models.DichVuSuDung.MaDVSD == used_service_id
    ).first()
    
    if not used_service:
        raise HTTPException(status_code=404, detail="Used service not found")
    
    if not used_service.FileKetQua:
        raise HTTPException(status_code=404, detail="No result file available for this service")
    
    # Check if file exists
    if not os.path.exists(used_service.FileKetQua):
        raise HTTPException(status_code=404, detail="Result file not found on server")
    
    # Get file info
    filename = os.path.basename(used_service.FileKetQua)
    
    # Determine media type based on file extension
    file_extension = os.path.splitext(filename)[1].lower()
    media_type_map = {
        '.pdf': 'application/pdf',
        '.jpg': 'image/jpeg',
        '.jpeg': 'image/jpeg',
        '.png': 'image/png',
        '.doc': 'application/msword',
        '.docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.txt': 'text/plain'
    }
    
    media_type = media_type_map.get(file_extension, 'application/octet-stream')
    
    return FileResponse(
        path=used_service.FileKetQua,
        filename=f"lab_result_{used_service_id}_{filename}",
        media_type=media_type
    )

@router.get("/view/{used_service_id}")
async def view_result_file(used_service_id: int, db: Session = Depends(get_db)):
    """
    View lab result file in browser (inline) by service ID
    """
    # Find the used service record
    used_service = db.query(models.DichVuSuDung).filter(
        models.DichVuSuDung.MaDVSD == used_service_id
    ).first()
    
    if not used_service:
        raise HTTPException(status_code=404, detail="Used service not found")
    
    if not used_service.FileKetQua:
        raise HTTPException(status_code=404, detail="No result file available for this service")
    
    # Check if file exists
    if not os.path.exists(used_service.FileKetQua):
        raise HTTPException(status_code=404, detail="Result file not found on server")
    
    # Get file info
    filename = os.path.basename(used_service.FileKetQua)
    file_extension = os.path.splitext(filename)[1].lower()
    
    # Determine media type
    media_type_map = {
        '.pdf': 'application/pdf',
        '.jpg': 'image/jpeg',
        '.jpeg': 'image/jpeg',
        '.png': 'image/png',
        '.txt': 'text/plain'
    }
    
    media_type = media_type_map.get(file_extension, 'application/octet-stream')
    
    # For viewing in browser, we don't want to force download
    response = FileResponse(
        path=used_service.FileKetQua,
        media_type=media_type
    )
    
    # Add headers to display inline (view in browser) instead of download
    if file_extension in ['.pdf', '.jpg', '.jpeg', '.png', '.txt']:
        response.headers["Content-Disposition"] = f"inline; filename={filename}"
    else:
        response.headers["Content-Disposition"] = f"attachment; filename={filename}"
    
    return response

def get_patient_email_by_medical_record(medical_record_id: int, db: Session):
    """
    Get patient email from medical record ID
    """
    try:
        import requests
        response = requests.get(f"http://api_gateway_service:6000/medical-record/{medical_record_id}/patient")
        if response.status_code == 200:
            patient_data_raw = response.json()
            print("🔍 Raw patient data:", patient_data_raw)
            
            # Handle the tuple/list format [data, status_code]
            if isinstance(patient_data_raw, (list, tuple)) and len(patient_data_raw) >= 1:
                patient_data = patient_data_raw[0]  # Extract the actual data
            else:
                patient_data = patient_data_raw
            
            print("✅ Processed patient data:", patient_data)
            
            # Extract email from the nested structure
            if 'patient' in patient_data:
                patient_info = patient_data['patient']
                email = patient_info.get('Email') or patient_info.get('email')
                if email:
                    print(f"✅ Found patient email: {email}")
                    return email
            else:
                print("❌ 'patient' key not found in response")
                
    except Exception as e:
        print(f"❌ Error getting patient email: {e}")
        import traceback
        print(f"❌ Traceback: {traceback.format_exc()}")
    
    # Fallback
    fallback_email = f"patient_{medical_record_id}@hospital.com"
    print(f"🔄 Using fallback email: {fallback_email}")
    return fallback_email

def get_patient_id_by_medical_record(medical_record_id: int, db: Session):
    """
    Get patient ID from medical record ID
    """
    try:
        import requests
        response = requests.get(f"http://api_gateway_service:6000/medical-record/{medical_record_id}/patient")
        if response.status_code == 200:
            patient_data_raw = response.json()
            print("🔍 Raw patient data for ID:", patient_data_raw)
            
            # Handle the tuple/list format [data, status_code]
            if isinstance(patient_data_raw, (list, tuple)) and len(patient_data_raw) >= 1:
                patient_data = patient_data_raw[0]  # Extract the actual data
            else:
                patient_data = patient_data_raw
            
            print("✅ Processed patient data for ID:", patient_data)
            
            # Extract patient ID from the response structure
            patient_id = patient_data.get('patient_id')
            if patient_id:
                print(f"✅ Found patient ID: {patient_id}")
                return str(patient_id)
            else:
                print("❌ 'patient_id' key not found in response")
                
    except Exception as e:
        print(f"❌ Error getting patient ID: {e}")
        import traceback
        print(f"❌ Traceback: {traceback.format_exc()}")
    
    # Fallback
    fallback_id = str(medical_record_id)
    print(f"🔄 Using fallback patient ID: {fallback_id}")
    return fallback_id