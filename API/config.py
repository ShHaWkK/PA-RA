import os
from dotenv import load_dotenv

load_dotenv()

class Settings:
    # DATABASE_URL: str = os.getenv("DATABASE_URL", "mysql+pymysql://user:password@db:3306/database_name")
    DATABASE_URL: str = os.getenv("DATABASE_URL")


print("DATABASE_URL Settings", Settings.DATABASE_URL)

settings = Settings()
