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

    # Schedule the reminder job to run every 15 minutes
    scheduler.add_job(reminder_job, 'interval', minutes=60)
    scheduler.start()