FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Create necessary directories for DomPDF and ensure proper permissions
RUN mkdir -p /var/www/storage/app/dompdf_temp /var/www/storage/fonts && \
    chmod -R 755 /var/www/storage
