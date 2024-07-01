# controllers/service_registration_controller.py
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.service_registration import ServiceRegistration as ServiceRegistrationModel
from Schemas.service_registration import ServiceRegistration, ServiceRegistrationCreate
from database import get_db

router = APIRouter()

@router.post("/service_registrations/", response_model=ServiceRegistration)
def create_service_registration(service_registration: ServiceRegistrationCreate, db: Session = Depends(get_db)):
    new_service_registration = ServiceRegistrationModel(
        service_id=service_registration.service_id,
        volunteer_id=service_registration.volunteer_id
    )
    db.add(new_service_registration)
    db.commit()
    db.refresh(new_service_registration)
    return new_service_registration

@router.get("/service_registrations/{service_registration_id}", response_model=ServiceRegistration)
def read_service_registration(service_registration_id: int, db: Session = Depends(get_db)):
    db_service_registration = db.query(ServiceRegistrationModel).filter(ServiceRegistrationModel.id == service_registration_id).first()
    if db_service_registration is None:
        raise HTTPException(status_code=404, detail="Service Registration not found")
    return db_service_registration

@router.delete("/service_registrations/{service_registration_id}", response_model=ServiceRegistration)
def delete_service_registration(service_registration_id: int, db: Session = Depends(get_db)):
    db_service_registration = db.query(ServiceRegistrationModel).filter(ServiceRegistrationModel.id == service_registration_id).first()
    if db_service_registration is None:
        raise HTTPException(status_code=404, detail="Service Registration not found")
    db.delete(db_service_registration)
    db.commit()
    return db_service_registration