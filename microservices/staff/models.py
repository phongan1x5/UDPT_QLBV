from sqlalchemy import Column, Integer, String, Date, ForeignKey
from sqlalchemy.orm import relationship
from database import Base


class Staff(Base):
    __tablename__ = "NhanVien"  # Table name matches the ERD

    MaNhanVien = Column(Integer, primary_key=True, index=True)  # Primary Key
    IDPhongBan = Column(Integer, nullable=True)  # Foreign Key to PhongBan
    HoTen = Column(String, nullable=False)  # Full Name
    NgaySinh = Column(String, nullable=False)  # Date of Birth
    DiaChi = Column(String, nullable=False)  # Address
    SoDienThoai = Column(String, nullable=False)  # Phone Number
    SoDinhDanh = Column(String, nullable=False) #CCCD
    LoaiNhanVien = Column(String, nullable=False)  # Staff Type
    ChuyenKhoa = Column(String, nullable=True)  # Specialization

    # Relationships
    # PhongBan = relationship("Department", back_populates="NhanVien")


class Department(Base):
    __tablename__ = "PhongBan"  # Table name matches the ERD

    IDPhongBan = Column(Integer, primary_key=True, index=True)  # Primary Key
    TenPhongBan = Column(String, nullable=False)  # Department Name
    IDTruongPhongBan = Column(Integer, nullable=True)  # Department Head ID

    # Relationships
    # NhanVien = relationship("Staff", back_populates="PhongBan")
