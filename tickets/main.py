import logging
import os
import subprocess
import sys

def install_requirements():
    """Install packages from requirements.txt."""
    requirements_path = os.path.join(os.path.dirname(__file__), 'requirements.txt')
    if not os.path.isfile(requirements_path):
        logging.error(f"Could not find the requirements file: {requirements_path}")
        sys.exit(1)
    
    try:
        subprocess.check_call([sys.executable, "-m", "pip", "install", "-r", requirements_path])
    except subprocess.CalledProcessError as e:
        logging.error(f"Failed to install requirements: {e}")
        sys.exit(1)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler("app.log"),
        logging.StreamHandler()
    ]
)

# Install requirements
install_requirements()

# Example usage of TicketAPI
from src.views.login_view import open_login

if __name__ == "__main__":
    open_login()
