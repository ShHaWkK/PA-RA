# Path : /var/www/html/frontend/Dockerfile
FROM php:8.1-apache

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Activer le module mod_rewrite d'Apache
RUN a2enmod rewrite

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]