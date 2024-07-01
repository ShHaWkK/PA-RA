# controllers/volunteer_controller.py
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from Models.volunteer import Volunteer as VolunteerModel
from Schemas.volunteer import Volunteer, VolunteerCreate
from database import get_db

router = APIRouter()

@router.post("/volunteers/", response_model=Volunteer)
def create_volunteer(volunteer: VolunteerCreate, db: Session = Depends(get_db)):
    new_volunteer = VolunteerModel(
        name=volunteer.name,
        skills=volunteer.skills,
        availabilities=volunteer.availabilities
    )
    db.add(new_volunteer)
    db.commit()
    db.refresh(new_volunteer)
    return new_volunteer

@router.get("/volunteers/{volunteer_id}", response_model=Volunteer)
def read_volunteer(volunteer_id: int, db: Session = Depends(get_db)):
    db_volunteer = db.query(VolunteerModel).filter(VolunteerModel.id == volunteer_id).first()
    if db_volunteer is None:
        raise HTTPException(status_code=404, detail="Volunteer not found")
    return db_volunteer

@router.delete("/volunteers/{volunteer_id}", response_model=Volunteer)
def delete_volunteer(volunteer_id: int, db: Session = Depends(get_db)):
    db_volunteer = db.query(VolunteerModel).filter(VolunteerModel.id == volunteer_id).first()
    if db_volunteer is None:
        raise HTTPException(status_code=404, detail="Volunteer not found")
    db.delete(db_volunteer)
    db.commit()
    return db_volunteer