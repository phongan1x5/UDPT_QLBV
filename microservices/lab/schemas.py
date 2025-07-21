from pydantic import BaseModel
from typing import Optional

class DichVuBase(BaseModel):
    TenDichVu: str  # Service Name
    NoiDungDichVu: str  # Service Description
    DonGia: float  # Service Price

class DichVuCreate(DichVuBase):
    pass

class DichVuResponse(DichVuBase):
    MaDichVu: int  # Service ID

    class Config:
        orm_mode = True

class DichVuSuDungBase(BaseModel):
    MaDichVu: int  # Service ID
    MaGiayKhamBenh: int  # Medical Record ID
    ThoiGian: str  # Time of service usage
    KetQua: Optional[str] = None  # Result of the service
    FileKetQua: Optional[str] = None  # File path or URL for the scan result

class DichVuSuDungCreate(DichVuSuDungBase):
    pass

class DichVuSuDungResponse(DichVuSuDungBase):
    MaDVSD: int  # Used Service ID

    class Config:
        orm_mode = True