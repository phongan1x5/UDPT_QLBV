from pydantic import BaseModel
from typing import Optional, List

class ThuocBase(BaseModel):
    TenThuoc: str
    DonViTinh: str
    ChiDinh: Optional[str] = None
    SoLuongTonKho: int
    GiaTien: float

class ThuocCreate(ThuocBase):
    pass

class ThuocResponse(ThuocBase):
    MaThuoc: int

    class Config:
        from_attributes = True  # Updated for newer Pydantic

class ThuocTheoToaBase(BaseModel):
    MaThuoc: int
    SoLuong: int
    GhiChu: Optional[str] = None

class ThuocTheoToaCreate(ThuocTheoToaBase):
    pass

class ThuocTheoToaResponse(ThuocTheoToaBase):
    MaToaThuoc: int

    class Config:
        from_attributes = True

class ToaThuocBase(BaseModel):
    MaGiayKhamBenh: int
    TrangThaiToaThuoc: str

class ToaThuocCreate(ToaThuocBase):
    ThuocTheoToa: List[ThuocTheoToaCreate] = []  # Made optional

# Simple response without relationships for create/list operations
class ToaThuocResponse(ToaThuocBase):
    MaToaThuoc: int

    class Config:
        from_attributes = True

# Detailed response with relationships for specific queries
class ToaThuocDetailResponse(ToaThuocBase):
    MaToaThuoc: int
    ThuocTheoToa: List[ThuocTheoToaResponse] = []

    class Config:
        from_attributes = True