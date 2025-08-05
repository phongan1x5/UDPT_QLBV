from pydantic import BaseModel, EmailStr
from typing import Optional
from datetime import date

class PatientBase(BaseModel):
    HoTen: str
    NgaySinh: date
    GioiTinh: str
    SoDienThoai: str
    SoDinhDanh: str
    Email: EmailStr
    DiaChi: Optional[str] = None
    BaoHiemYTe: Optional[str] = None
    is_active: Optional[bool] = True

class PatientCreate(PatientBase):
    Password: str
    TienSu: str

class PatientUpdate(BaseModel):
    HoTen: Optional[str] = None
    NgaySinh: Optional[date] = None
    GioiTinh: Optional[str] = None
    SoDienThoai: Optional[str] = None
    SoDinhDanh: Optional[str] = None
    Email: Optional[EmailStr] = None
    DiaChi: Optional[str] = None
    BaoHiemYTe: Optional[str] = None
    is_active: Optional[bool] = None

class PatientResponse(PatientBase):
    id: int

    class Config:
        orm_mode = True
