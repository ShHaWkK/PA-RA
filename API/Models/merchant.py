from sqlalchemy import Column, Integer, String, TIMESTAMP
from sqlalchemy.sql import func
from database import Base

class Merchant(Base):
    __tablename__ = 'merchants'

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    address = Column(String(255), nullable=False)
    contact_info = Column(String(255), nullable=False)
    membership_expiration_date = Column(TIMESTAMP, nullable=False)
    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())
