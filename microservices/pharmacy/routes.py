from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
import models, schemas, database
from typing import List
from rabbitmq import publish_event  # ✅ Import RabbitMQ publisher
import requests  # ✅ Import requests for API calls

router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

# ✅ Helper functions to get patient data
def get_patient_data_by_medical_record(medical_record_id: int):
    """Get patient data from medical record ID via API Gateway"""
    try:
        response = requests.get(f"http://api_gateway_service:6000/medical-record/{medical_record_id}/patient", timeout=10)
        if response.status_code == 200:
            patient_data_raw = response.json()
            print("🔍 Raw patient data:", patient_data_raw)
            
            # Handle the tuple/list format [data, status_code]
            if isinstance(patient_data_raw, (list, tuple)) and len(patient_data_raw) >= 1:
                patient_data = patient_data_raw[0]  # Extract the actual data
            else:
                patient_data = patient_data_raw
            
            print("✅ Processed patient data:", patient_data)
            return patient_data
        else:
            print(f"❌ Failed to get patient data for medical record {medical_record_id}: {response.status_code}")
            return None
    except Exception as e:
        print(f"❌ Error getting patient data: {e}")
        return None

def get_patient_email_by_medical_record(medical_record_id: int):
    """Extract patient email from medical record"""
    try:
        patient_data = get_patient_data_by_medical_record(medical_record_id)
        if patient_data and 'patient' in patient_data:
            patient_info = patient_data['patient']
            email = patient_info.get('Email') or patient_info.get('email')
            if email:
                print(f"✅ Found patient email: {email}")
                return email
        print("❌ No email found in patient data")
        return None
    except Exception as e:
        print(f"❌ Error extracting patient email: {e}")
        return None

def get_patient_id_by_medical_record(medical_record_id: int):
    """Extract patient ID from medical record"""
    try:
        patient_data = get_patient_data_by_medical_record(medical_record_id)
        if patient_data:
            patient_id = patient_data.get('patient_id')
            if patient_id:
                print(f"✅ Found patient ID: {patient_id}")
                return str(patient_id)
        print("❌ No patient ID found in data")
        return None
    except Exception as e:
        print(f"❌ Error extracting patient ID: {e}")
        return None

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

@router.get("/prescriptions/with-medicines")
def list_prescriptions_with_medicines(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    """
    Get all prescriptions with their associated medicines
    """
    # Get prescriptions with pagination
    prescriptions = db.query(models.ToaThuoc).offset(skip).limit(limit).all()
    total_count = db.query(models.ToaThuoc).count()
    
    prescriptions_with_medicines = []
    
    for prescription in prescriptions:
        # Get medicines for this prescription
        medicines = db.query(models.ThuocTheoToa, models.Thuoc).join(
            models.Thuoc, models.ThuocTheoToa.MaThuoc == models.Thuoc.MaThuoc
        ).filter(models.ThuocTheoToa.MaToaThuoc == prescription.MaToaThuoc).all()
        
        prescription_data = {
            "MaToaThuoc": prescription.MaToaThuoc,
            "MaGiayKhamBenh": prescription.MaGiayKhamBenh,
            "TrangThaiToaThuoc": prescription.TrangThaiToaThuoc,
            "medicines": [
                {
                    "MaThuoc": medicine.Thuoc.MaThuoc,
                    "TenThuoc": medicine.Thuoc.TenThuoc,
                    "DonViTinh": medicine.Thuoc.DonViTinh,
                    "SoLuong": medicine.ThuocTheoToa.SoLuong,
                    "GhiChu": medicine.ThuocTheoToa.GhiChu,
                    "GiaTien": medicine.Thuoc.GiaTien,
                    "ThanhTien": float(medicine.Thuoc.GiaTien) * medicine.ThuocTheoToa.SoLuong if medicine.Thuoc.GiaTien else 0
                }
                for medicine in medicines
            ],
            "total_medicines": len(medicines),
            "total_cost": sum(
                float(medicine.Thuoc.GiaTien) * medicine.ThuocTheoToa.SoLuong 
                for medicine in medicines 
                if medicine.Thuoc.GiaTien
            )
        }
        
        prescriptions_with_medicines.append(prescription_data)
    # print(prescriptions_with_medicines)
    
    return {
        "data": prescriptions_with_medicines,
        "total": total_count,
        "skip": skip,
        "limit": limit
    }

@router.post("/prescriptions/handleMedicinePrescription/{prescription_id}")
def handle_medicine_prescription(prescription_id: int, db: Session = Depends(get_db)):
    """
    Handle medicine prescription by subtracting prescribed quantities from stock.
    This is called when medicines are dispensed to the patient.
    """
    try:
        # Find the prescription
        prescription = db.query(models.ToaThuoc).filter(
            models.ToaThuoc.MaToaThuoc == prescription_id
        ).first()
        
        if not prescription:
            raise HTTPException(status_code=404, detail=f"Prescription {prescription_id} not found")
        
        # Get all medicines in this prescription
        prescription_medicines = db.query(models.ThuocTheoToa).filter(
            models.ThuocTheoToa.MaToaThuoc == prescription_id
        ).all()
        
        if not prescription_medicines:
            raise HTTPException(status_code=404, detail=f"No medicines found for prescription {prescription_id}")
        
        updated_medicines = []
        insufficient_stock = []
        
        # Check stock availability first
        for pmed in prescription_medicines:
            medicine = db.query(models.Thuoc).filter(
                models.Thuoc.MaThuoc == pmed.MaThuoc
            ).first()
            
            if not medicine:
                raise HTTPException(status_code=404, detail=f"Medicine {pmed.MaThuoc} not found")
            
            # Check if sufficient stock
            if medicine.SoLuongTonKho < pmed.SoLuong:
                insufficient_stock.append({
                    "MaThuoc": medicine.MaThuoc,
                    "TenThuoc": medicine.TenThuoc,
                    "required": pmed.SoLuong,
                    "available": medicine.SoLuongTonKho
                })
        
        # If insufficient stock, return error
        if insufficient_stock:
            raise HTTPException(
                status_code=400, 
                detail={
                    "message": "Insufficient stock for some medicines",
                    "insufficient_medicines": insufficient_stock
                }
            )
        
        # All medicines have sufficient stock, proceed with subtraction
        for pmed in prescription_medicines:
            medicine = db.query(models.Thuoc).filter(
                models.Thuoc.MaThuoc == pmed.MaThuoc
            ).first()
            
            # Subtract the prescribed quantity from stock
            old_stock = medicine.SoLuongTonKho
            medicine.SoLuongTonKho -= pmed.SoLuong
            
            updated_medicines.append({
                "MaThuoc": medicine.MaThuoc,
                "TenThuoc": medicine.TenThuoc,
                "prescribed_quantity": pmed.SoLuong,
                "old_stock": old_stock,
                "new_stock": medicine.SoLuongTonKho
            })
        
        # Commit all changes
        db.commit()
        
        return {
            "message": "Medicines exported successfully",
            "prescription_id": prescription_id,
            "updated_medicines": updated_medicines
        }
        
    except HTTPException:
        db.rollback()
        raise
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=500, detail=f"Error exporting medicines: {str(e)}")


@router.get("/prescriptions/detail/{prescriptionId}")
def get_detail_prescription_by_prescriptionId(prescriptionId: int, db: Session = Depends(get_db)):
    """Get the prescription for a specific medical record with medicine details"""
    prescription = db.query(models.ToaThuoc).filter(
        models.ToaThuoc.MaToaThuoc == prescriptionId
    ).first()
    
    if not prescription:
        raise HTTPException(status_code=404, detail=f"No prescription found with MaToaThuoc: {prescriptionId}")
    
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
    """Update prescription status and send notification if completed"""
    prescription = db.query(models.ToaThuoc).filter(models.ToaThuoc.MaToaThuoc == prescription_id).first()
    if not prescription:
        raise HTTPException(status_code=404, detail="Prescription not found")
    
    print(f"📋 Updating prescription {prescription_id} status")
    print(f"📋 Request data: {request_data}")
    
    # Get the old and new status
    old_status = prescription.TrangThaiToaThuoc
    new_status = request_data.get("status", prescription.TrangThaiToaThuoc)
    
    # Update the prescription status
    prescription.TrangThaiToaThuoc = new_status
    db.commit()
    db.refresh(prescription)
    
    print(f"✅ Prescription {prescription_id} status updated: {old_status} → {new_status}")
    
    # 🔥 NEW: Send notification if status is "Completed"
    if new_status.lower() == "completed":
        try:
            print(f"💊 Prescription completed - sending notification for prescription {prescription_id}")
            
            # Get patient data
            patient_email = get_patient_email_by_medical_record(prescription.MaGiayKhamBenh)
            patient_id = get_patient_id_by_medical_record(prescription.MaGiayKhamBenh)
            
            if patient_email and patient_id:
                # Get prescription medicines for detailed message
                medicines = db.query(models.ThuocTheoToa, models.Thuoc).join(
                    models.Thuoc, models.ThuocTheoToa.MaThuoc == models.Thuoc.MaThuoc
                ).filter(models.ThuocTheoToa.MaToaThuoc == prescription_id).all()
                
                medicine_names = [medicine.Thuoc.TenThuoc for medicine in medicines[:3]]  # First 3 medicines
                medicine_list = ", ".join(medicine_names)
                if len(medicines) > 3:
                    medicine_list += f" and {len(medicines) - 3} more"
                
                # Create notification message
                notification_message = f"💊 Your prescription is ready for pickup! Prescription #{prescription_id} containing {medicine_list} is now available at the pharmacy. Please bring your ID when collecting."
                
                # Publish event to RabbitMQ
                payload = f"UserID:BN{patient_id}, UserEmail:{patient_email}, SourceSystem:Pharmacy, Message:{notification_message}"
                
                success = publish_event("PrescriptionReady", payload)
                if success:
                    print(f"✅ Sent prescription ready notification for prescription {prescription_id} to {patient_email}")
                else:
                    print(f"❌ Failed to send prescription notification for prescription {prescription_id}")
            else:
                print(f"⚠️ Could not find patient contact info for prescription {prescription_id}")
                
        except Exception as e:
            print(f"❌ Error sending prescription notification: {e}")
            import traceback
            print(f"❌ Traceback: {traceback.format_exc()}")
            # Don't fail the status update if notification fails
    
    return prescription

# ✅ Add manual testing endpoint
@router.post("/test-prescription-notification/{prescription_id}")
def test_prescription_notification(prescription_id: int, db: Session = Depends(get_db)):
    """Test notification for a specific prescription"""
    try:
        prescription = db.query(models.ToaThuoc).filter(
            models.ToaThuoc.MaToaThuoc == prescription_id
        ).first()
        
        if not prescription:
            raise HTTPException(status_code=404, detail="Prescription not found")
        
        # Create test notification
        test_message = f"💊 TEST: Your prescription #{prescription_id} is ready for pickup at the pharmacy!"
        payload = f"UserID:test, UserEmail:phongan105@gmail.com, SourceSystem:Pharmacy, Message:{test_message}"
        
        success = publish_event("PrescriptionReady", payload)
        
        return {
            "status": "success" if success else "failed",
            "prescription_id": prescription_id,
            "message": "Test notification sent" if success else "Failed to send notification"
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error: {str(e)}")