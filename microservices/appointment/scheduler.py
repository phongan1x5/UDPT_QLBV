from apscheduler.schedulers.background import BackgroundScheduler
from sqlalchemy.orm import Session
from database import SessionLocal
from reminder import send_reminders

def start_scheduler():
    scheduler = BackgroundScheduler()

    def reminder_job():
        db = SessionLocal()
        try:
            send_reminders(db)
        finally:
            db.close()

    # Schedule the reminder job to run every 720 minutes = 12 hours
    scheduler.add_job(reminder_job, 'interval', minutes=720)
    scheduler.start()