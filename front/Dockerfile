# Utilisez une image officielle de Node.js comme image de base
FROM node:latest

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste des fichiers de l'application
COPY . .

# Exposer le port sur lequel tourne l'application
EXPOSE 3000

# Commande pour démarrer le front-end
CMD ["npm", "start"]
