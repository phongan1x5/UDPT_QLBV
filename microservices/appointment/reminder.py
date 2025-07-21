from datetime import datetime, timedelta
from sqlalchemy.orm import Session
from rabbitmq import publish_event  # Import RabbitMQ publisher
import models

def send_reminders(db: Session):
    now = datetime.now()
    reminder_time = now + timedelta(hours=24)  # Check appointments within the next 24 hours

    # Query appointments within the reminder time window
    upcoming_appointments = db.query(models.Appointment).filter(
        models.Appointment.Ngay == now.date(),
        models.Appointment.Gio >= now.time(),
        models.Appointment.Gio <= reminder_time.time()
    ).all()

    for appointment in upcoming_appointments:
        # Publish reminder event to RabbitMQ
        message = f"Reminder: You have an appointment with Dr. {appointment.MaBacSi} at {appointment.Gio} on {appointment.Ngay}."
        payload = f"PatientPhoneNumber:{appointment.PatientPhoneNumber}, PatientDeviceID:{appointment.PatientDeviceID}, Message:{message}"
        publish_event("AppointmentReminder", payload)