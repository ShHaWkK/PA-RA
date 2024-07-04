# Utilise l'image officielle de PHP avec Apache
FROM php:8.2-apache

# Installe les extensions PHP nécessaires et les outils de base
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        zip \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copie le contenu du dossier de l'application dans le dossier racine du serveur web
COPY . /var/www/html/

# Assure que les permissions sont correctes pour Apache
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Active le module de réécriture d'URL dans Apache
RUN a2enmod rewrite

# Copie le fichier .htaccess pour la réécriture d'URL
COPY .htaccess /var/www/html/.htaccess

RUN ls /var/www/html

# Installer les dépendances
WORKDIR /var/www/html
RUN pwd
RUN ls
RUN composer install
RUN ls
#  --no-interaction --prefer-dist --optimize-autoloader

# Expose le port 80
EXPOSE 80

# Démarrer Apache dans le conteneur
CMD ["apache2-foreground"]