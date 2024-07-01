from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.product import Product as ProductModel
from Schemas.product import Product, ProductCreate, ProductUpdate
from database import get_db

router = APIRouter()

@router.post("/products/", response_model=Product)
def create_product(product: ProductCreate, db: Session = Depends(get_db)):
    new_product = ProductModel(
        name=product.name,
        barcode=product.barcode,
        expiration_date=product.expiration_date,
        quantity=product.quantity,
    )
    db.add(new_product)
    db.commit()
    db.refresh(new_product)
    return new_product

@router.get("/products/{product_id}", response_model=Product)
def read_product(product_id: int, db: Session = Depends(get_db)):
    db_product = db.query(ProductModel).filter(ProductModel.id == product_id).first()
    if db_product is None:
        raise HTTPException(status_code=404, detail="Product not found")
    return db_product

@router.put("/products/{product_id}", response_model=Product)
def update_product(product_id: int, product: ProductUpdate, db: Session = Depends(get_db)):
    db_product = db.query(ProductModel).filter(ProductModel.id == product_id).first()
    if db_product is None:
        raise HTTPException(status_code=404, detail="Product not found")
    db_product.name = product.name
    db_product.barcode = product.barcode
    db_product.expiration_date = product.expiration_date
    db_product.quantity = product.quantity
    db.commit()
    db.refresh(db_product)
    return db_product

@router.delete("/products/{product_id}", response_model=Product)
def delete_product(product_id: int, db: Session = Depends(get_db)):
    db_product = db.query(ProductModel).filter(ProductModel.id == product_id).first()
    if db_product is None:
        raise HTTPException(status_code=404, detail="Product not found")
    db.delete(db_product)
    db.commit()
    return db_product
