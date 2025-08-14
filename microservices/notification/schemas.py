from pydantic import BaseModel, EmailStr
from datetime import datetime
from typing import Optional

class NotificationCreate(BaseModel):
    userId: str
    sourceSystem: str
    message: str

    class Config:
        from_attributes = True

class NotificationResponse(BaseModel):
    id: int
    userId: str
    sourceSystem: str
    message: str
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

class EmailNotificationRequest(BaseModel):
    userId: str
    userEmail: EmailStr
    sourceSystem: str
    message: str

    class Config:
        json_schema_extra = {
            "example": {
                "userId": "12345",
                "userEmail": "patient@example.com",
                "sourceSystem": "Laboratory",
                "message": "Your lab results are ready! Please visit our portal to view them."
            }
        }