from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class MerchantBase(BaseModel):
    name: str
    address: str
    contact_info: str
    membership_expiration_date: datetime

class MerchantCreate(MerchantBase):
    pass

class MerchantUpdate(MerchantBase):
    pass

class Merchant(MerchantBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True
