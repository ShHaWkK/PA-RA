# controllers/collection_controller.py
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.collection import Collection as CollectionModel
from Schemas.collection import Collection, CollectionCreate
from database import get_db

router = APIRouter()

@router.post("/collections/", response_model=Collection)
def create_collection(collection: CollectionCreate, db: Session = Depends(get_db)):
    new_collection = CollectionModel(
        merchant_id=collection.merchant_id,
        product_id=collection.product_id
    )
    db.add(new_collection)
    db.commit()
    db.refresh(new_collection)
    return new_collection

@router.get("/collections/{collection_id}", response_model=Collection)
def read_collection(collection_id: int, db: Session = Depends(get_db)):
    db_collection = db.query(CollectionModel).filter(CollectionModel.id == collection_id).first()
    if db_collection is None:
        raise HTTPException(status_code=404, detail="Collection not found")
    return db_collection

@router.delete("/collections/{collection_id}", response_model=Collection)
def delete_collection(collection_id: int, db: Session = Depends(get_db)):
    db_collection = db.query(CollectionModel).filter(CollectionModel.id == collection_id).first()
    if db_collection is None:
        raise HTTPException(status_code=404, detail="Collection not found")
    db.delete(db_collection)
    db.commit()
    return db_collection