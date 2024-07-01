# models/service_registration.py
from sqlalchemy import Column, Integer, ForeignKey, TIMESTAMP
from sqlalchemy.sql import func
from database import Base

class ServiceRegistration(Base):
    __tablename__ = 'service_registrations'

    id = Column(Integer, primary_key=True, index=True)
    service_id = Column(Integer, ForeignKey('services.id'), nullable=False)
    volunteer_id = Column(Integer, ForeignKey('volunteers.id'), nullable=False)
    registration_date = Column(TIMESTAMP, server_default=func.now())
    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())