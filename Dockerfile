FROM php:8.2-fpm

# System dependencies install 
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm \
    postgresql-client

# PHP extensions install 
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd
RUN pecl install redis && docker-php-ext-enable redis

# Composer install 
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www

# Permissions set
RUN chown -R www-data:www-data /var/www