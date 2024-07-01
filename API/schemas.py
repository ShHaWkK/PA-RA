from pydantic import BaseModel, EmailStr
from typing import Optional
from datetime import datetime
from enum import Enum

class UserRole(str, Enum):
    admin = "admin"
    commercant = "commercant"
    benevole = "benevole"
    client = "client"

class UserBase(BaseModel):
    nom: str
    email: EmailStr
    role: UserRole

class UserCreate(UserBase):
    mot_de_passe: str

class UserUpdate(UserBase):
    mot_de_passe: Optional[str] = None

class User(UserBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True
