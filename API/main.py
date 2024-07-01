# main.py
from fastapi import FastAPI
from database import engine, Base
from Controllers import user_controller

# Créer les tables de la base de données
Base.metadata.create_all(bind=engine)

app = FastAPI()

# Inclure les routeurs des contrôleurs
app.include_router(user_controller.router)

@app.get("/")
def read_root():
    return {"message": "Welcome to API"}
