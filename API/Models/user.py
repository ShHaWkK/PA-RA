# file : /API/Models/user.py
from sqlalchemy import Column, Integer, String, Enum, TIMESTAMP
from sqlalchemy.sql import func
from database import Base

class User(Base):
    __tablename__ = 'users'

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False) 
    email = Column(String(255), unique=True, index=True, nullable=False)
    password = Column(String(255), nullable=False)
    role = Column(Enum('admin', 'commercant', 'benevole', 'client'), nullable=False)
    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())
