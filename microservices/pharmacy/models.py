from sqlalchemy import Column, Integer, String, ForeignKey, Float
from sqlalchemy.orm import relationship
from database import Base

class Thuoc(Base):
    __tablename__ = "Thuoc"  # Table name for medicines

    MaThuoc = Column(Integer, primary_key=True, index=True)  # Primary Key
    TenThuoc = Column(String, nullable=False)  # Medicine Name
    DonViTinh = Column(String, nullable=False)  # Unit of Measurement
    ChiDinh = Column(String, nullable=True)  # Indication
    SoLuongTonKho = Column(Integer, nullable=False)  # Stock Quantity
    GiaTien = Column(Float, nullable=False)  # Price per Unit

class ToaThuoc(Base):
    __tablename__ = "ToaThuoc"  # Table name for prescriptions

    MaToaThuoc = Column(Integer, primary_key=True, index=True)  # Primary Key
    MaGiayKhamBenh = Column(Integer, nullable=False)  # Foreign Key to medical record
    TrangThaiToaThuoc = Column(String, nullable=False)  # Prescription Status

    # Relationship to ThuocTheoToa
    # ThuocTheoToa = relationship("ThuocTheoToa", back_populates="ToaThuoc", cascade="all, delete-orphan")

class ThuocTheoToa(Base):
    __tablename__ = "ThuocTheoToa"  # Table name for medicines in prescriptions

    MaToaThuoc = Column(Integer, ForeignKey("ToaThuoc.MaToaThuoc"), primary_key=True)  # Foreign Key to ToaThuoc
    MaThuoc = Column(Integer, ForeignKey("Thuoc.MaThuoc"), primary_key=True)  # Foreign Key to Thuoc
    SoLuong = Column(Integer, nullable=False)  # Quantity of Medicine
    GhiChu = Column(String, nullable=True)  # Notes

    # # Relationships
    # ToaThuoc = relationship("ToaThuoc", back_populates="ThuocTheoToa")
    # Thuoc = relationship("Thuoc")