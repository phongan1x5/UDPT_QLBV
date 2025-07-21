from fastapi import APIRouter
from notifications import send_email, send_sms, send_push_notification

router = APIRouter()

@router.post("/send-email")
def send_email_notification(recipient: str, subject: str, message: str):
    send_email(recipient, subject, message)
    return {"status": "Email sent"}

@router.post("/send-sms")
def send_sms_notification(phone_number: str, message: str):
    send_sms(phone_number, message)
    return {"status": "SMS sent"}

@router.post("/send-push")
def send_push_notification_endpoint(device_id: str, message: str):
    send_push_notification(device_id, message)
    return {"status": "Push notification sent"}