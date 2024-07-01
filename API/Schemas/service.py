from pydantic import BaseModel
from datetime import datetime

class ServiceBase(BaseModel):
    name: str
    description: str
    schedule: datetime

class ServiceCreate(ServiceBase):
    pass

class Service(ServiceBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True