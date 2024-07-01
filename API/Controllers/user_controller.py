# user_controller.py
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.user import User as UserModel
from schemas import User, UserCreate, UserUpdate
from database import get_db
from passlib.context import CryptContext

router = APIRouter()

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def get_password_hash(password):
    return pwd_context.hash(password)

@router.post("/users/", response_model=User)
def create_user(user: UserCreate, db: Session = Depends(get_db)):
    db_user = db.query(UserModel).filter(UserModel.email == user.email).first()
    if db_user:
        raise HTTPException(status_code=400, detail="Email already registered")
    hashed_password = get_password_hash(user.password)
    new_user = UserModel(
        name=user.name,
        email=user.email,
        password=hashed_password,
        role=user.role,
    )
    db.add(new_user)
    db.commit()
    db.refresh(new_user)
    return new_user

@router.get("/users/{user_id}", response_model=User)
def read_user(user_id: int, db: Session = Depends(get_db)):
    db_user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if db_user is None:
        raise HTTPException(status_code=404, detail="User not found")
    return db_user

@router.put("/users/{user_id}", response_model=User)
def update_user(user_id: int, user: UserUpdate, db: Session = Depends(get_db)):
    db_user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if db_user is None:
        raise HTTPException(status_code=404, detail="User not found")
    db_user.name = user.name
    db_user.email = user.email
    db_user.role = user.role
    if user.password:
        db_user.password = get_password_hash(user.password)
    db.commit()
    db.refresh(db_user)
    return db_user

@router.delete("/users/{user_id}", response_model=User)
def delete_user(user_id: int, db: Session = Depends(get_db)):
    db_user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if db_user is None:
        raise HTTPException(status_code=404, detail="User not found")
    db.delete(db_user)
    db.commit()
    return db_user
