# Utilise l'image MySQL officielle comme base
FROM mysql:latest

# Définir les variables d'environnement pour MySQL
ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=no_more_waste
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

# Expose le port MySQL standard
EXPOSE 3306

# Copie du script de configuration pour créer et remplir la base de données
# Si vous avez un script SQL pour initialiser la base de données, vous pouvez le copier ici
COPY ./init.sql /docker-entrypoint-initdb.d/

# Commande par défaut pour démarrer MySQL
CMD ["mysqld"]
