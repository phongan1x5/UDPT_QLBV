from pydantic import BaseModel
from models import UserRole

class UserCreate(BaseModel):
    id: str
    password: str
    role: UserRole = UserRole.patient

class UserLogin(BaseModel):
    id: str
    password: str

class UserResponse(BaseModel):
    id: str
    role: UserRole

    class Config:
        orm_mode = True

class Token(BaseModel):
    access_token: str
    token_type: str = "bearer"
