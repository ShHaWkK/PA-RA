# models/collection.py
from sqlalchemy import Column, Integer, ForeignKey, TIMESTAMP
from sqlalchemy.sql import func
from database import Base

class Collection(Base):
    __tablename__ = 'collections'

    id = Column(Integer, primary_key=True, index=True)
    merchant_id = Column(Integer, ForeignKey('merchants.id'), nullable=False)
    product_id = Column(Integer, ForeignKey('products.id'), nullable=False)
    collection_date = Column(TIMESTAMP, server_default=func.now())
    created_at = Column(TIMESTAMP, server_default=func.now())
    updated_at = Column(TIMESTAMP, server_default=func.now(), onupdate=func.now())