# controllers/service_controller.py
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.service import Service as ServiceModel
from Schemas.service import Service, ServiceCreate
from database import get_db

router = APIRouter()

@router.post("/services/", response_model=Service)
def create_service(service: ServiceCreate, db: Session = Depends(get_db)):
    new_service = ServiceModel(
        name=service.name,
        description=service.description,
        schedule=service.schedule
    )
    db.add(new_service)
    db.commit()
    db.refresh(new_service)
    return new_service

@router.get("/services/{service_id}", response_model=Service)
def read_service(service_id: int, db: Session = Depends(get_db)):
    db_service = db.query(ServiceModel).filter(ServiceModel.id == service_id).first()
    if db_service is None:
        raise HTTPException(status_code=404, detail="Service not found")
    return db_service

@router.delete("/services/{service_id}", response_model=Service)
def delete_service(service_id: int, db: Session = Depends(get_db)):
    db_service = db.query(ServiceModel).filter(ServiceModel.id == service_id).first()
    if db_service is None:
        raise HTTPException(status_code=404, detail="Service not found")
    db.delete(db_service)
    db.commit()
    return db_service