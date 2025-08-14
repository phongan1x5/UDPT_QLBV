from datetime import datetime, timedelta
from sqlalchemy.orm import Session
from rabbitmq import publish_event
import models
import requests

def get_patient_data_by_id(patient_id: int):
    """Get patient data from API Gateway"""
    try:
        response = requests.get(f"http://patient_service:6002/patients/{patient_id}", timeout=10)
        if response.status_code == 200:
            patient_data = response.json()
            print(f"✅ Found patient data for ID {patient_id}")
            return patient_data
        else:
            print(f"❌ Failed to get patient data for ID {patient_id}: {response.status_code}")
            return None
    except Exception as e:
        print(f"❌ Error getting patient data: {e}")
        return None

def get_doctor_name(doctor_id: int):
    """Get doctor name from API Gateway"""
    try:
        response = requests.get(f"http://staff_service:6003/staff/{doctor_id}", timeout=10)
        if response.status_code == 200:
            doctor_data = response.json()
            doctor_name = doctor_data.get('HoTen', f'Doctor #{doctor_id}')
            print(f"✅ Found doctor name: {doctor_name}")
            return doctor_name
        else:
            print(f"❌ Failed to get doctor data for ID {doctor_id}")
            return f"Doctor #{doctor_id}"
    except Exception as e:
        print(f"❌ Error getting doctor data: {e}")
        return f"Doctor #{doctor_id}"

def send_reminders(db: Session):
    """Send appointment reminders for upcoming appointments"""
    try:
        print("🔔 Starting appointment reminder check...")
        
        now = datetime.now()
        
        # Check for appointments in the next 24 hours
        tomorrow = now + timedelta(hours=24)
        tomorrow_date = tomorrow.strftime("%Y-%m-%d")
        print("tomorrow_date: ", tomorrow_date)
        
        print(f"📅 Checking appointments for {tomorrow_date}")
        
        # Query confirmed appointments for tomorrow
        upcoming_appointments = db.query(models.Appointment).filter(
            models.Appointment.Ngay == tomorrow_date,
            models.Appointment.TrangThai.in_(["DaXacNhan", "DaThuTien"])  # Confirmed or paid appointments
        ).all()
        
        print(f"📋 Found {len(upcoming_appointments)} upcoming appointments")
        
        for appointment in upcoming_appointments:
            try:
                print(f"🔄 Processing appointment {appointment.MaLichHen}")
                
                # Get patient data
                patient_data = get_patient_data_by_id(appointment.MaBenhNhan)
                if not patient_data:
                    print(f"⚠️ Skipping appointment {appointment.MaLichHen} - no patient data")
                    continue
                
                # Get doctor name
                doctor_name = get_doctor_name(appointment.MaBacSi)
                
                # Extract patient email and ID
                patient_email = patient_data.get('Email') or patient_data.get('email')
                patient_id = str(appointment.MaBenhNhan)
                
                if not patient_email:
                    print(f"⚠️ No email found for patient {patient_id}")
                    continue
                
                # Format appointment time for display
                appointment_time = appointment.Gio
                if len(appointment_time) == 5:  # "HH:MM" format
                    try:
                        time_obj = datetime.strptime(appointment_time, "%H:%M")
                        formatted_time = time_obj.strftime("%I:%M %p")  # "02:30 PM"
                    except:
                        formatted_time = appointment_time
                else:
                    formatted_time = appointment_time
                
                # Create reminder message
                reminder_message = f"🏥 Appointment Reminder: You have an appointment with {doctor_name} tomorrow ({appointment.Ngay}) at {formatted_time}. Please arrive 15 minutes early. Appointment ID: #{appointment.MaLichHen}"
                
                # Publish reminder event to RabbitMQ
                payload = f"UserID:BN{patient_id}, UserEmail:{patient_email}, SourceSystem:Appointment, Message:{reminder_message}"
                
                success = publish_event("AppointmentReminder", payload)
                if success:
                    print(f"✅ Sent reminder for appointment {appointment.MaLichHen} to {patient_email}")
                else:
                    print(f"❌ Failed to send reminder for appointment {appointment.MaLichHen}")
                    
            except Exception as e:
                print(f"❌ Error processing appointment {appointment.MaLichHen}: {e}")
                continue
        
        if len(upcoming_appointments) == 0:
            print("📅 No upcoming appointments found for tomorrow")
        else:
            print(f"✅ Processed {len(upcoming_appointments)} appointment reminders")
            
    except Exception as e:
        print(f"❌ Error in send_reminders: {e}")
        import traceback
        print(f"❌ Traceback: {traceback.format_exc()}")