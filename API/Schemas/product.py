from pydantic import BaseModel
from typing import Optional
from datetime import datetime, date

class ProductBase(BaseModel):
    name: str
    barcode: str
    expiration_date: date
    quantity: int

class ProductCreate(ProductBase):
    pass

class ProductUpdate(ProductBase):
    pass

class Product(ProductBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True
