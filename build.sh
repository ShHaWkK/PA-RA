#!/bin/bash

# Construire l'image backend
echo "Building backend image..."
docker build -t backend-image -f backend/Dockerfile ./backend

# Construire l'image frontend
echo "Building frontend image..."
docker build -t frontend-image -f frontend/Dockerfile ./frontend

# Exécuter les conteneurs
echo "Running backend container..."
docker run -d --name backend-container -p 80:80 backend-image

echo "Running frontend container..."
docker run -d --name frontend-container -p 3000:80 frontend-image

# Afficher les conteneurs en cours d'exécution
docker ps
