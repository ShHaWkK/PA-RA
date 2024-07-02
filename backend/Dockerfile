# Utiliser l'image officielle PHP avec Apache
FROM php:8.1-apache

# Mettre à jour les paquets et installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libssl-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application dans le répertoire de travail du conteneur
COPY . /var/www/html

# Installer les dépendances PHP via Composer
# RUN composer install --no-scripts --no-autoloader --working-dir=/var/www/html

# Configurer Apache pour utiliser le répertoire public comme document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Activer le module mod_rewrite d'Apache
RUN a2enmod rewrite

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80 pour permettre l'accès au serveur Apache
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]
