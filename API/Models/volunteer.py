# models/volunteer.py
from sqlalchemy import Column, Integer, String, JSON, TIMESTAMP
from sqlalchemy.sql import func
from database import Base

class Volunteer(Base):
    __tablename__ = 'volunteers'

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    skills = Column(JSON, nullable=False)
    availabilities = Column(JSON, nullable=False)
    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())
