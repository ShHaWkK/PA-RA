# Utilisez une image Python officielle comme image de base
FROM python:3.12-slim

# Définir le répertoire de travail dans le conteneur
WORKDIR /app

# Copier les fichiers requirements.txt et app.py dans le conteneur
COPY requirements.txt .
COPY . .
COPY main.py .
COPY src ./src

# Installer les dépendances système et Xvfb
RUN apt-get update && apt-get install -y \
    tk \
    xvfb \
    x11vnc \
    fluxbox \
    && rm -rf /var/lib/apt/lists/*

# Installer les dépendances Python
RUN pip install --no-cache-dir -r requirements.txt

# Définir la variable d'environnement pour le chemin de l'API_ENDPOINT
ENV API_ENDPOINT=http://localhost:80

# Ajouter un script de démarrage pour Xvfb et VNC
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Définir la commande par défaut pour exécuter l'application
CMD ["start.sh"]
