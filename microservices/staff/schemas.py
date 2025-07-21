from pydantic import BaseModel
from typing import Optional

class StaffBase(BaseModel):
    IDPhongBan: int  # Department ID
    HoTen: str  # Full Name
    NgaySinh: str  # Date of Birth
    DiaChi: str  # Address
    SoDienThoai: str  # Phone Number
    SoDinhDanh: str #CCCD
    LoaiNhanVien: str  # Staff Type
    ChuyenKhoa: Optional[str] = None  # Specialization

class StaffCreate(StaffBase):
    Password: str  # Password

class StaffUpdate(BaseModel):
    HoTen: Optional[str] = None  # Full Name
    NgaySinh: Optional[str] = None  # Date of Birth
    DiaChi: Optional[str] = None  # Address
    SoDienThoai: Optional[str] = None  # Phone Number
    SoDinhDanh: str #CCCD
    LoaiNhanVien: Optional[str] = None  # Staff Type
    ChuyenKhoa: Optional[str] = None  # Specialization

class StaffResponse(StaffBase):
    MaNhanVien: int  # Staff ID

    class Config:
        orm_mode = True

class DepartmentBase(BaseModel):
    TenPhongBan: str  # Department Name
    IDTruongPhongBan: Optional[int] = None  # Department Head ID

class DepartmentCreate(DepartmentBase):
    pass

class DepartmentUpdate(BaseModel):
    TenPhongBan: Optional[str] = None  # Department Name
    IDTruongPhongBan: Optional[int] = None  # Department Head ID

class DepartmentResponse(DepartmentBase):
    IDPhongBan: int  # Department ID

    class Config:
        orm_mode = True