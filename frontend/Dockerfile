# Utiliser l'image officielle PHP avec Apache
FROM php:8.1-apache

# Mettre à jour les paquets et installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Configurer le répertoire racine d'Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Activer le module mod_rewrite d'Apache
RUN a2enmod rewrite

# Copier les fichiers de l'application dans le répertoire de travail du conteneur
COPY . /var/www/html

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 3000 pour permettre l'accès au serveur Apache
EXPOSE 3000

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]