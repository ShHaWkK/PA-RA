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
    name: str
    email: EmailStr
    role: UserRole

class UserCreate(UserBase):
    password: str

class UserUpdate(UserBase):
    password: Optional[str] = None

class User(UserBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        orm_mode = True
