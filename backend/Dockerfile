# Utilise l'image officielle de PHP avec Apache
FROM php:8.1-apache

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

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définit COMPOSER_ALLOW_SUPERUSER à 1 pour autoriser l'exécution de plugins
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copie le contenu du dossier de l'application dans le dossier racine du serveur web
COPY . /var/www/html/

# Change le répertoire de travail
WORKDIR /var/www/html

# Supprime le fichier composer.lock pour éviter les erreurs de dépendances (s'il est présent)
RUN rm -f composer.lock

# Installe les dépendances Composer sans les dépendances de développement
RUN composer install --no-dev --optimize-autoloader

# Assure que les permissions sont correctes pour Apache
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Expose le port 80
EXPOSE 80

# Démarrer Apache dans le conteneur
CMD ["apache2-foreground"]
