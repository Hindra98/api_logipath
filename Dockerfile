# Utiliser PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires pour Symfony et PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip intl \
    && apt-get upgrade -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurer Apache (DocumentRoot vers /public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
WORKDIR /var/www/html

# OPTIMISATION : On installe les dépendances PHP d'abord
# En copiant uniquement ces deux fichiers, Docker met les paquets en cache et
# Installer les dépendances (sans les scripts pour l'instant)
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --no-autoloader --optimize-autoloader

COPY . .

RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 var/cache var/log var/sessions

# On génère l'autoloader et on pré-chauffe le cache en tant que www-data
USER www-data
RUN composer dump-autoload --optimize --no-scripts \
    && php bin/console cache:warmup --env=prod

USER root
# Retour en root pour qu'Apache puisse démarrer sur le port 80

# Exposer le port 80
EXPOSE 80

# Script de démarrage pour lancer les migrations avant Apache
CMD php bin/console doctrine:migrations:migrate --no-interaction && apache2-foreground