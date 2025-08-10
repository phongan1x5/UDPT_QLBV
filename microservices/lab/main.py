from fastapi import FastAPI
import database, routes
from fastapi.staticfiles import StaticFiles
UPLOAD_DIR = "uploads/results"
app = FastAPI()

app.mount("/results", StaticFiles(directory=UPLOAD_DIR), name="results")
app.include_router(routes.router)

@app.on_event("startup")
def on_startup():
    database.Base.metadata.create_all(bind=database.engine)

@app.get("/health")
def health_check():
    return {"status": "ok"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=6005)