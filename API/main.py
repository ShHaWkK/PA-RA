# main.py
from fastapi import FastAPI
from database import engine, Base
from Controllers import user_controller, merchant_controller, product_controller, collection_controller, delivery_controller, volunteer_controller, service_controller, service_registration_controller, auth_controller
from fastapi.openapi.docs import get_redoc_html

# Créer les tables de la base de données
Base.metadata.create_all(bind=engine)

app = FastAPI()

# Inclure les routeurs des contrôleurs
app.include_router(user_controller.router)
app.include_router(merchant_controller.router)
app.include_router(product_controller.router)
app.include_router(collection_controller.router)
app.include_router(delivery_controller.router)
app.include_router(volunteer_controller.router)
app.include_router(service_controller.router)
app.include_router(service_registration_controller.router)
app.include_router(auth_controller.router, prefix="/auth", tags=["auth"])

# Configuration de la documentation Redoc
@app.get("/")
def read_root():
    return {"message": "Welcome to No More Waste API"}

@app.get("/redoc", include_in_schema=False)
def redoc_html():
    return get_redoc_html(
        title="No More Waste API",
        openapi_url=app.openapi_url,
    )
