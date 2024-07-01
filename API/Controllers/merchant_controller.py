from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.merchant import Merchant as MerchantModel
from Schemas.merchant import Merchant, MerchantCreate, MerchantUpdate
from database import get_db

router = APIRouter()

@router.post("/merchants/", response_model=Merchant)
def create_merchant(merchant: MerchantCreate, db: Session = Depends(get_db)):
    new_merchant = MerchantModel(
        name=merchant.name,
        address=merchant.address,
        contact_info=merchant.contact_info,
        membership_expiration_date=merchant.membership_expiration_date,
    )
    db.add(new_merchant)
    db.commit()
    db.refresh(new_merchant)
    return new_merchant

@router.get("/merchants/{merchant_id}", response_model=Merchant)
def read_merchant(merchant_id: int, db: Session = Depends(get_db)):
    db_merchant = db.query(MerchantModel).filter(MerchantModel.id == merchant_id).first()
    if db_merchant is None:
        raise HTTPException(status_code=404, detail="Merchant not found")
    return db_merchant

@router.put("/merchants/{merchant_id}", response_model=Merchant)
def update_merchant(merchant_id: int, merchant: MerchantUpdate, db: Session = Depends(get_db)):
    db_merchant = db.query(MerchantModel).filter(MerchantModel.id == merchant_id).first()
    if db_merchant is None:
        raise HTTPException(status_code=404, detail="Merchant not found")
    db_merchant.name = merchant.name
    db_merchant.address = merchant.address
    db_merchant.contact_info = merchant.contact_info
    db_merchant.membership_expiration_date = merchant.membership_expiration_date
    db.commit()
    db.refresh(db_merchant)
    return db_merchant

@router.delete("/merchants/{merchant_id}", response_model=Merchant)
def delete_merchant(merchant_id: int, db: Session = Depends(get_db)):
    db_merchant = db.query(MerchantModel).filter(MerchantModel.id == merchant_id).first()
    if db_merchant is None:
        raise HTTPException(status_code=404, detail="Merchant not found")
    db.delete(db_merchant)
    db.commit()
    return db_merchant
