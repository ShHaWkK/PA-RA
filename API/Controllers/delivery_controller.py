# controllers/delivery_controller.py
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.delivery import Delivery as DeliveryModel
from Schemas.delivery import Delivery, DeliveryCreate
from database import get_db

router = APIRouter()

@router.post("/deliveries/", response_model=Delivery)
def create_delivery(delivery: DeliveryCreate, db: Session = Depends(get_db)):
    new_delivery = DeliveryModel(
        route_name=delivery.route_name,
        destination=delivery.destination,
        recipient_type=delivery.recipient_type,
        status=delivery.status,
        comment=delivery.comment
    )
    db.add(new_delivery)
    db.commit()
    db.refresh(new_delivery)
    return new_delivery

@router.get("/deliveries/{delivery_id}", response_model=Delivery)
def read_delivery(delivery_id: int, db: Session = Depends(get_db)):
    db_delivery = db.query(DeliveryModel).filter(DeliveryModel.id == delivery_id).first()
    if db_delivery is None:
        raise HTTPException(status_code=404, detail="Delivery not found")
    return db_delivery

@router.delete("/deliveries/{delivery_id}", response_model=Delivery)
def delete_delivery(delivery_id: int, db: Session = Depends(get_db)):
    db_delivery = db.query(DeliveryModel).filter(DeliveryModel.id == delivery_id).first()
    if db_delivery is None:
        raise HTTPException(status_code=404, detail="Delivery not found")
    db.delete(db_delivery)
    db.commit()
    return db_delivery