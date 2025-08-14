from fastapi import FastAPI
import models
import database
from routes import router

# Create FastAPI app
app = FastAPI(title="Notification Service", version="1.0.0")
app.include_router(router)

# Create database tables
database.Base.metadata.create_all(bind=database.engine)

@app.get("/")
def root():
    return {"service": "notification", "status": "running"}

@app.get("/health")
def health_check():
    return {"status": "ok", "service": "notification"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=6008)