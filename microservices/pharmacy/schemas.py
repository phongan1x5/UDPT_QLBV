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
        orm_mode = True

class ThuocTheoToaBase(BaseModel):
    MaThuoc: int
    SoLuong: int
    GhiChu: Optional[str] = None

class ThuocTheoToaCreate(ThuocTheoToaBase):
    pass

class ThuocTheoToaResponse(ThuocTheoToaBase):
    MaToaThuoc: int

    class Config:
        orm_mode = True

class ToaThuocBase(BaseModel):
    MaGiayKhamBenh: int
    TrangThaiToaThuoc: str

class ToaThuocCreate(ToaThuocBase):
    ThuocTheoToa: List[ThuocTheoToaCreate]

class ToaThuocResponse(ToaThuocBase):
    MaToaThuoc: int
    ThuocTheoToa: List[ThuocTheoToaResponse]

    class Config:
        orm_mode = True