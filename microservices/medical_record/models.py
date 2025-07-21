from sqlalchemy import Column, Integer, String, Date, Boolean
from sqlalchemy.ext.declarative import declarative_base

Base = declarative_base()

class HoSoBenhAn(Base):
    __tablename__ = "HoSoBenhAn"  # Table name for medical history

    MaHSBA = Column(Integer, primary_key=True, index=True)  # Primary Key
    MaBenhNhan = Column(Integer, nullable=False)  # Reference to patient (no FK constraint)
    TienSu = Column(String, nullable=True)  # Medical history notes
    is_active = Column(Boolean, default=True)

class GiayKhamBenh(Base):
    __tablename__ = "GiayKhamBenh"  # Table name for medical records

    MaGiayKhamBenh = Column(Integer, primary_key=True, index=True)  # Primary Key
    MaHSBA = Column(Integer, nullable=False)  # Reference to HoSoBenhAn (no FK constraint)
    BacSi = Column(Integer, nullable=False)  # Reference to doctor (no FK constraint)
    MaLichHen = Column(Integer, nullable=True)  # Reference to appointment (no FK constraint)
    NgayKham = Column(Date, nullable=False)  # Examination date
    ChanDoan = Column(String, nullable=False)  # Diagnosis
    LuuY = Column(String, nullable=True)  # Notes or remarks