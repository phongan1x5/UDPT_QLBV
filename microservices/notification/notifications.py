from email_validator import validate_email, EmailNotValidError
from config import EMAIL_CONFIG

def send_email(recipient, subject, message):
    try:
        validate_email(recipient)
        print(f"Sending email to {recipient}: {subject} - {message}")
        # Replace with actual email sending logic (e.g., using smtplib or an external service)
    except EmailNotValidError as e:
        print(f"Invalid email address: {e}")

def send_sms(phone_number, message):
    print(f"Sending SMS to {phone_number}: {message}")
    # Replace with actual SMS sending logic (e.g., Twilio)

def send_push_notification(device_id, message):
    print(f"Sending push notification to {device_id}: {message}")
    # Replace with actual push notification logic