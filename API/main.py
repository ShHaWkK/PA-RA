# main.py
from fastapi import FastAPI
from database import engine, Base
from Controllers import user_controller, merchant_controller, product_controller

# Créer les tables de la base de données
Base.metadata.create_all(bind=engine)

app = FastAPI()

# Inclure les routeurs des contrôleurs
app.include_router(user_controller.router)
app.include_router(merchant_controller.router)
app.include_router(product_controller.router)

# Configuration de la documentation Redoc
@app.get("/")
def read_root():
    return {"message": "Welcome to API"}

@app.get("/redoc", include_in_schema=False)
def redoc_html():
    return get_redoc_html(
        title="No More Waste API",
        openapi_url=app.openapi_url,
    )
