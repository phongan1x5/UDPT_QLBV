from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.orm import Session
from typing import List, Optional
from models import Notification
from schemas import NotificationCreate, NotificationResponse, EmailNotificationRequest
from database import get_db
from notifications import send_email, send_sms, send_push_notification

router = APIRouter()

@router.post("/create", response_model=NotificationResponse)
def create_notification(notification: NotificationCreate, db: Session = Depends(get_db)):
    """Create a notification record in the database"""
    try:
        new_notification = Notification(
            userId=notification.userId,
            sourceSystem=notification.sourceSystem,
            message=notification.message
        )
        db.add(new_notification)
        db.commit()
        db.refresh(new_notification)
        return new_notification
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=500, detail=f"Error creating notification: {str(e)}")

@router.post("/send-email-and-store")
def send_email_and_store(
    request: EmailNotificationRequest, 
    db: Session = Depends(get_db)
):
    """
    Send email notification and store in database
    Expected payload: {
        "userId": "12345",
        "userEmail": "patient@email.com", 
        "sourceSystem": "Laboratory",
        "message": "Your lab results are ready!"
    }
    """
    try:
        # Send email
        subject = f"Notification from {request.sourceSystem}"
        send_email(request.userEmail, subject, request.message)
        
        # Store in database
        notification = Notification(
            userId=request.userId,
            sourceSystem=request.sourceSystem,
            message=request.message
        )
        db.add(notification)
        db.commit()
        db.refresh(notification)
        
        return {
            "status": "success",
            "message": f"Email sent to {request.userEmail} and notification stored",
            "notification_id": notification.id
        }
        
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=500, detail=f"Error processing request: {str(e)}")

@router.get("/user_notification/{user_id}", response_model=List[NotificationResponse])
def get_user_notifications(
    user_id: str, 
    db: Session = Depends(get_db),
    limit: Optional[int] = Query(50, description="Maximum number of notifications to return"),
    offset: Optional[int] = Query(0, description="Number of notifications to skip"),
    source_system: Optional[str] = Query(None, description="Filter by source system")
):
    """Get notifications for a specific user ID with optional filtering and pagination"""
    try:
        query = db.query(Notification).filter(Notification.userId == user_id)
        
        if source_system:
            query = query.filter(Notification.sourceSystem == source_system)
        
        query = query.order_by(Notification.created_at.desc())
        notifications = query.offset(offset).limit(limit).all()
        
        return notifications
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error retrieving notifications: {str(e)}")

@router.get("/user/{user_id}/count")
def get_user_notification_count(
    user_id: str, 
    db: Session = Depends(get_db),
    source_system: Optional[str] = Query(None, description="Filter by source system")
):
    """Get total count of notifications for a specific user"""
    try:
        query = db.query(Notification).filter(Notification.userId == user_id)
        
        if source_system:
            query = query.filter(Notification.sourceSystem == source_system)
        
        total_count = query.count()
        
        return {"user_id": user_id, "total_notifications": total_count}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error counting notifications: {str(e)}")

@router.delete("/user/{user_id}")
def delete_user_notifications(
    user_id: str, 
    db: Session = Depends(get_db),
    source_system: Optional[str] = Query(None, description="Filter by source system to delete")
):
    """Delete all notifications for a specific user"""
    try:
        query = db.query(Notification).filter(Notification.userId == user_id)
        
        if source_system:
            query = query.filter(Notification.sourceSystem == source_system)
        
        deleted_count = query.count()
        query.delete()
        db.commit()
        
        return {
            "message": f"Successfully deleted {deleted_count} notifications for user {user_id}",
            "deleted_count": deleted_count
        }
        
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=500, detail=f"Error deleting notifications: {str(e)}")

@router.get("/health")
def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "service": "notification"}

# Legacy endpoints (for backward compatibility)
@router.post("/send-email")
def send_email_notification(recipient: str, subject: str, message: str):
    send_email(recipient, subject, message)
    return {"status": "Email sent"}

@router.post("/send-sms")
def send_sms_notification(phone_number: str, message: str):
    send_sms(phone_number, message)
    return {"status": "SMS placeholder"}

@router.post("/send-push")
def send_push_notification_endpoint(device_id: str, message: str):
    send_push_notification(device_id, message)
    return {"status": "Push notification placeholder"}