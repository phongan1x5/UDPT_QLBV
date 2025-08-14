from fastapi import FastAPI
import database, routes
from scheduler import start_scheduler
from rabbitmq import test_connection

app = FastAPI()

app.include_router(routes.router)

@app.on_event("startup")
def on_startup():
    database.Base.metadata.create_all(bind=database.engine)
    
    # ✅ Test RabbitMQ connection like lab service
    success, message = test_connection()
    if success:
        print(f"✅ RabbitMQ: {message}")
    else:
        print(f"❌ RabbitMQ: {message}")
    
    # Start the appointment reminder scheduler
    start_scheduler()
    print("✅ Appointment reminder scheduler started")

@app.get("/health")
def health_check():
    return {"status": "ok"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=6004)