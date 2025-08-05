from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
import models, schemas, database
import requests

router = APIRouter()

def get_db():
    db = database.SessionLocal()
    try:
        yield db
    finally:
        db.close()

@router.post("/staff", response_model=schemas.StaffResponse)
def create_staff(staff: schemas.StaffCreate, db: Session = Depends(get_db)):
    # Check if a staff member with the same SoDinhDanh exists
    existing_staff = db.query(models.Staff).filter(models.Staff.SoDinhDanh == staff.SoDinhDanh).first()
    if existing_staff:
        raise HTTPException(
            status_code=400,
            detail=f"A staff with the SoDinhDanh {staff.SoDinhDanh} already exists."
        )
    
    # Create the staff member in the database
    db_staff = models.Staff(**staff.model_dump(exclude={"Password"}))
    db.add(db_staff)
    db.commit()
    db.refresh(db_staff)

    # Generate user_id and role for the auth service based on LoaiNhanVien
    staff_type_mapping = {
        "doctor": "DR",
        "desk_staff": "DS",
        "lab_staff": "LS",
        "nurse": "NS",
        "pharmacist": "PH",
        "admin": "AD"
    }
    role = staff.LoaiNhanVien.lower()
    if role not in staff_type_mapping:
        raise HTTPException(
            status_code=400,
            detail=f"Invalid staff type: {staff.LoaiNhanVien}. Allowed types are {', '.join(staff_type_mapping.keys())}."
        )
    user_id = f"{staff_type_mapping[role]}{db_staff.MaNhanVien}"

    # Prepare the payload for the auth service
    auth_payload = {
        "id": user_id,
        "password": staff.Password,
        "role": role
    }

    # Make a request to the auth service to create the user
    auth_service_url = "http://api_gateway:6000/auth/register"  # Replace with the actual auth service URL
    try:
        response = requests.post(auth_service_url, json=auth_payload)
        response.raise_for_status()  # Raise an exception for HTTP errors
    except requests.exceptions.RequestException as e:
        # Rollback the staff creation if the auth service fails
        db.delete(db_staff)
        db.commit()
        raise HTTPException(
            status_code=500,
            detail=f"Failed to create user in auth service: {str(e)}"
        )

    return db_staff

@router.get("/staff/getDoctors", response_model=list[schemas.StaffResponse])
def get_doctors(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    """
    Get all staff members with LoaiNhanVien = 'doctor'
    """
    doctors = db.query(models.Staff).filter(
        models.Staff.LoaiNhanVien == "doctor"
    ).offset(skip).limit(limit).all()
    
    return doctors

@router.get("/staff", response_model=list[schemas.StaffResponse])
def list_staff(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    return db.query(models.Staff).offset(skip).limit(limit).all()

@router.get("/staff/{staff_id}", response_model=schemas.StaffResponse)
def get_staff(staff_id: int, db: Session = Depends(get_db)):
    staff = db.query(models.Staff).filter(models.Staff.MaNhanVien == staff_id).first()
    if not staff:
        raise HTTPException(status_code=404, detail="Staff not found")
    return staff

@router.put("/staff/{staff_id}", response_model=schemas.StaffResponse)
def update_staff(staff_id: int, staff_update: schemas.StaffUpdate, db: Session = Depends(get_db)):
    staff = db.query(models.Staff).filter(models.Staff.MaNhanVien == staff_id).first()
    if not staff:
        raise HTTPException(status_code=404, detail="Staff not found")
    for var, value in vars(staff_update).items():
        if value is not None:
            setattr(staff, var, value)
    db.commit()
    db.refresh(staff)
    return staff

@router.delete("/staff/{staff_id}", status_code=204)
def delete_staff(staff_id: int, db: Session = Depends(get_db)):
    staff = db.query(models.Staff).filter(models.Staff.MaNhanVien == staff_id).first()
    if not staff:
        raise HTTPException(status_code=404, detail="Staff not found")
    db.delete(staff)
    db.commit()
    return None

@router.post("/departments", response_model=schemas.DepartmentResponse)
def create_department(department: schemas.DepartmentCreate, db: Session = Depends(get_db)):
    # Create the department in the database
    db_department = models.Department(**department.model_dump())
    db.add(db_department)
    db.commit()
    db.refresh(db_department)
    return db_department

@router.get("/departments", response_model=list[schemas.DepartmentResponse])
def list_departments(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    # Retrieve all departments with pagination
    return db.query(models.Department).offset(skip).limit(limit).all()

@router.get("/departments/{department_id}", response_model=schemas.DepartmentResponse)
def get_department(department_id: int, db: Session = Depends(get_db)):
    # Retrieve a specific department by ID
    department = db.query(models.Department).filter(models.Department.IDPhongBan == department_id).first()
    if not department:
        raise HTTPException(status_code=404, detail="Department not found")
    return department

@router.put("/departments/{department_id}", response_model=schemas.DepartmentResponse)
def update_department(department_id: int, department_update: schemas.DepartmentUpdate, db: Session = Depends(get_db)):
    # Update a specific department by ID
    department = db.query(models.Department).filter(models.Department.IDPhongBan == department_id).first()
    if not department:
        raise HTTPException(status_code=404, detail="Department not found")
    for var, value in vars(department_update).items():
        if value is not None:
            setattr(department, var, value)
    db.commit()
    db.refresh(department)
    return department

@router.delete("/departments/{department_id}", status_code=204)
def delete_department(department_id: int, db: Session = Depends(get_db)):
    # Delete a specific department by ID
    department = db.query(models.Department).filter(models.Department.IDPhongBan == department_id).first()
    if not department:
        raise HTTPException(status_code=404, detail="Department not found")
    db.delete(department)
    db.commit()
    return None

# Add this route for more flexibility:
@router.get("/staff/by-type/{staff_type}", response_model=list[schemas.StaffResponse])
def get_staff_by_type(staff_type: str, skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    """
    Get all staff members by their type (doctor, desk_staff, lab_staff, pharmacist, admin)
    """
    valid_types = ["doctor", "desk_staff", "lab_staff", "pharmacist", "admin"]
    
    if staff_type.lower() not in valid_types:
        raise HTTPException(
            status_code=400,
            detail=f"Invalid staff type. Valid types are: {', '.join(valid_types)}"
        )
    
    staff_members = db.query(models.Staff).filter(
        models.Staff.LoaiNhanVien == staff_type.lower()
    ).offset(skip).limit(limit).all()
    
    return staff_members
