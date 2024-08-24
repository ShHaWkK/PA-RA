#!/bin/bash

# Vérifier si Docker est installé et accessible
if ! command -v docker &> /dev/null
then
    echo "Docker could not be found. Please ensure Docker is installed and running."
    exit 1
fi

# Construire l'image backend
if [ -d "./backend" ] && [ -f "./backend/Dockerfile" ]; then
    echo "Building backend image..."
    docker build -t backend-image -f backend/Dockerfile ./backend
else
    echo "Backend directory or Dockerfile not found!"
    exit 1
fi

# Construire l'image frontend
if [ -d "./frontend" ] && [ -f "./frontend/Dockerfile" ]; then
    echo "Building frontend image..."
    docker build -t frontend-image -f frontend/Dockerfile ./frontend
else
    echo "Frontend directory or Dockerfile not found!"
    exit 1
fi

# Exécuter les conteneurs
echo "Running backend container..."
docker run -d --name backend-container -p 80:80 backend-image

echo "Running frontend container..."
docker run -d --name frontend-container -p 3000:80 frontend-image

# Afficher les conteneurs en cours d'exécution
docker ps
