from sqlalchemy import Column, Integer, String, ForeignKey, Float
from sqlalchemy.orm import relationship
from database import Base

class DichVu(Base):
    __tablename__ = "DichVu"  # Table name for services

    MaDichVu = Column(Integer, primary_key=True, index=True)  # Primary Key
    TenDichVu = Column(String, nullable=False)  # Service Name
    NoiDungDichVu = Column(String, nullable=False)  # Service Description
    DonGia = Column(Float, nullable=False)  # Service Price

class DichVuSuDung(Base):
    __tablename__ = "DichVuSuDung"  # Table name for used services

    MaDVSD = Column(Integer, primary_key=True, index=True)  # Primary Key
    MaDichVu = Column(Integer, ForeignKey("DichVu.MaDichVu"), nullable=False)  # Foreign Key to DichVu
    MaGiayKhamBenh = Column(Integer, nullable=False)  # Foreign Key to medical record
    ThoiGian = Column(String, nullable=False)  # Time of service usage
    KetQua = Column(String, nullable=True)  # Result of the service
    FileKetQua = Column(String, nullable=True)  # File path or URL for the scan result

    # Relationships
    # DichVu = relationship("DichVu", back_populates="DichVuSuDung")