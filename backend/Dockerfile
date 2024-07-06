# Utilise l'image officielle de PHP avec Apache
FROM php:8.2-apache

# Installe les extensions PHP nécessaires et les outils de base
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        zip \
        unzip \
        libzip-dev \
        libpng-dev \
    && docker-php-ext-install zip pdo_mysql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Active le module de réécriture d'URL dans Apache
RUN a2enmod rewrite

# Expose le port 80
EXPOSE 80

# Démarrer Apache dans le conteneur
CMD ["apache2-foreground"]
