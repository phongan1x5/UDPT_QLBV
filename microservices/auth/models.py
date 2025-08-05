from sqlalchemy import Column, Integer, String, Enum
from database import Base
import enum

class UserRole(str, enum.Enum):
    patient = "patient"
    doctor = "doctor"
    desk_staff = "desk_staff"
    nurse = "nurse"
    lab_staff = "lab_staff"
    pharmacist = "pharmacist"
    admin = "admin"

class User(Base):
    __tablename__ = "users"
    
    id = Column(String, primary_key=True, index=True)
    hashed_password = Column(String, nullable=False)
    role = Column(Enum(UserRole), default=UserRole.patient, nullable=False)
