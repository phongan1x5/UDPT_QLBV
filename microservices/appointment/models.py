from sqlalchemy import Column, Integer, String, Date, Time, ForeignKey, Enum
from sqlalchemy.orm import relationship
from database import Base
import enum

class LichHenType(str, enum.Enum):
    KhamMoi = "KhamMoi"
    TaiKham = "TaiKham"

class TrangThaiType(str, enum.Enum):
    ChoXacNhan = "ChoXacNhan"
    DaXacNhan = "DaXacNhan"
    DaHuy = "DaHuy"

class Appointment(Base):
    __tablename__ = "LichHen"  # Table name matches the ERD

    MaLichHen = Column(Integer, primary_key=True, index=True)  # Primary Key
    MaBenhNhan = Column(Integer, nullable=False)  # Foreign Key to BenhNhan
    MaBacSi = Column(Integer, nullable=False)  # Foreign Key to NhanVien
    Ngay = Column(String, nullable=False)  # Appointment Date
    Gio = Column(String, nullable=False)  # Appointment Time
    LoaiLichHen = Column(Enum(LichHenType), nullable=False)  # Appointment Type (Enum)
    TrangThai = Column(Enum(TrangThaiType), nullable=False)  # Appointment Status (Enum)

    # Relationships (optional, depending on other services)
    # BenhNhan = relationship("Patient", back_populates="Appointments")
    # BacSi = relationship("Staff", back_populates="Appointments")