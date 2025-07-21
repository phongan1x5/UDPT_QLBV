from pydantic import BaseModel
from typing import Optional
from models import LichHenType, TrangThaiType

class AppointmentBase(BaseModel):
    MaBenhNhan: int  # Patient ID
    MaBacSi: int  # Doctor ID
    Ngay: str  # Appointment Date (YYYY-MM-DD)
    Gio: str  # Appointment Time (HH:MM:SS)
    LoaiLichHen: LichHenType  # Appointment Type (Enum)
    TrangThai: TrangThaiType  # Appointment Status (Enum)

class AppointmentCreate(AppointmentBase):
    pass

class AppointmentUpdate(BaseModel):
    Ngay: Optional[str] = None  # Appointment Date
    Gio: Optional[str] = None  # Appointment Time
    LoaiLichHen: Optional[LichHenType] = None  # Appointment Type (Enum)
    TrangThai: Optional[TrangThaiType] = None  # Appointment Status (Enum)

class AppointmentResponse(AppointmentBase):
    MaLichHen: int  # Appointment ID

    class Config:
        orm_mode = True