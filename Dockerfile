# Utiliser PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires pour Symfony et PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip \
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
COPY . .

# Installer les dépendances (sans les scripts pour l'instant)
RUN composer install --no-scripts --no-interaction --optimize-autoloader

# Permissions pour Symfony
RUN chown -R www-data:www-data /var/www/html/var

# Exposer le port 80
EXPOSE 80

# Script de démarrage pour lancer les migrations avant Apache
CMD php bin/console doctrine:migrations:migrate --no-interaction && apache2-foreground