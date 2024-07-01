# models/delivery.py
from sqlalchemy import Column, Integer, String, Enum, TIMESTAMP
from sqlalchemy.sql import func
from database import Base

class Delivery(Base):
    __tablename__ = 'deliveries'

    id = Column(Integer, primary_key=True, index=True)
    route_name = Column(String(255), nullable=False)
    destination = Column(String(255), nullable=False)
    recipient_type = Column(Enum('association', 'individual'), nullable=False)
    delivery_date = Column(TIMESTAMP, server_default=func.now())
    status = Column(String(255), nullable=False)
    comment = Column(String(255))
    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())