from sqlalchemy import Column, Integer, String, Date, Boolean
from database import Base

class Patient(Base):
    __tablename__ = "patients"

    id = Column(Integer, primary_key=True, index=True)
    HoTen = Column(String, nullable=False)
    NgaySinh = Column(Date, nullable=False)
    GioiTinh = Column(String, nullable=False)
    SoDienThoai = Column(String, nullable=False)
    SoDinhDanh = Column(String, nullable=False, unique=True)
    Email = Column(String, nullable=False, unique=False)
    DiaChi = Column(String, nullable=False)
    BaoHiemYTe = Column(String, nullable=True)
    is_active = Column(Boolean, default=True)
