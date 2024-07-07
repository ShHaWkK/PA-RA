# Utiliser l'image officielle PHP avec Apache
FROM php:8.1-apache

# Mettre à jour les paquets et installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Activer le module mod_rewrite d'Apache
RUN a2enmod rewrite

# Copier les fichiers de l'application dans le répertoire de travail du conteneur
COPY ./src /var/www/html

# Copier la configuration personnalisée d'Apache
#COPY ./config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80 pour permettre l'accès au serveur Apache
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]