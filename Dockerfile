# Base image PHP avec extensions nécessaires
FROM php:8.2-fpm

# Arguments pour composer
ARG COMPOSER_VERSION=2.6.4

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    curl \
    default-mysql-client \
    && docker-php-ext-install intl mbstring pdo_mysql zip opcache \
    && docker-php-ext-enable pdo_mysql intl mbstring opcache \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier le projet
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Créer l’utilisateur www-data
RUN chown -R www-data:www-data /var/www/html

# Exposer le port PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]
