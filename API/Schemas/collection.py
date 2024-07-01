# schemas/collection.py
from pydantic import BaseModel
from datetime import datetime

class CollectionBase(BaseModel):
    merchant_id: int
    product_id: int

class CollectionCreate(CollectionBase):
    pass

class Collection(CollectionBase):
    id: int
    collection_date: datetime
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True