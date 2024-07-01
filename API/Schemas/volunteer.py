# schemas/volunteer.py
from pydantic import BaseModel
from typing import List
from datetime import datetime

class VolunteerBase(BaseModel):
    name: str
    skills: List[str]
    availabilities: List[str]

class VolunteerCreate(VolunteerBase):
    pass

class Volunteer(VolunteerBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True