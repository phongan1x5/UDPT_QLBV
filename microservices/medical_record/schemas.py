from pydantic import BaseModel
from typing import Optional
from datetime import date

class HoSoBenhAnBase(BaseModel):
    MaBenhNhan: int
    TienSu: Optional[str] = None

class HoSoBenhAnCreate(HoSoBenhAnBase):
    pass

class HoSoBenhAnResponse(HoSoBenhAnBase):
    MaHSBA: int

    class Config:
        orm_mode = True

class GiayKhamBenhBase(BaseModel):
    MaHSBA: int
    BacSi: int
    MaLichHen: Optional[int] = None
    NgayKham: date
    ChanDoan: str
    LuuY: Optional[str] = None

class GiayKhamBenhCreate(GiayKhamBenhBase):
    pass

class GiayKhamBenhUpdate(BaseModel):
    ChanDoan: str
    LuuY: str

class GiayKhamBenhResponse(GiayKhamBenhBase):
    MaGiayKhamBenh: int

    class Config:
        orm_mode = True