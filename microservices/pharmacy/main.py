from fastapi import FastAPI
import database, routes
from rabbitmq import test_connection

app = FastAPI()

app.include_router(routes.router)

@app.on_event("startup")
def on_startup():
    database.Base.metadata.create_all(bind=database.engine)
    
    # ✅ Test RabbitMQ connection like other services
    success, message = test_connection()
    if success:
        print(f"✅ RabbitMQ: {message}")
    else:
        print(f"❌ RabbitMQ: {message}")

@app.get("/health")
def health_check():
    return {"status": "ok"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=6006)