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
    postgresql-client \
    ghostscript \
    libmagickwand-dev \
    libzip-dev \
    poppler-utils

# PHP extensions install 
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip
RUN pecl install redis imagick && docker-php-ext-enable redis imagick

# Allow Imagick to process PDF files (security policy override)
RUN sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/' /etc/ImageMagick-6/policy.xml 2>/dev/null || true

# Composer install 
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www

# Permissions set
RUN chown -R www-data:www-data /var/www