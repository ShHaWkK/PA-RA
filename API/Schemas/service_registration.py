from pydantic import BaseModel
from datetime import datetime

class ServiceRegistrationBase(BaseModel):
    service_id: int
    volunteer_id: int

class ServiceRegistrationCreate(ServiceRegistrationBase):
    pass

class ServiceRegistration(ServiceRegistrationBase):
    id: int
    registration_date: datetime
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True