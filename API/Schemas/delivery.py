# schemas/delivery.py
from pydantic import BaseModel
from datetime import datetime
from typing import Optional

class DeliveryBase(BaseModel):
    route_name: str
    destination: str
    recipient_type: str
    status: str
    comment: Optional[str] = None

class DeliveryCreate(DeliveryBase):
    pass

class Delivery(DeliveryBase):
    id: int
    delivery_date: datetime
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True