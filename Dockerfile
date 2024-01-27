# Use PHP 8.3 with Apache as the base image
FROM php:8.3-apache

# Install system dependencies (if any)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite for routing through index.php
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy Composer files
COPY composer.json composer.lock /var/www/html/

# Install Composer dependencies
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

# Copy your application code to the container
COPY . /var/www/html/

# Dump the autoloader after the application code has been copied
RUN composer dump-autoload --optimize

# Set Apache's document root to the public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port 80 to access the Apache server
EXPOSE 80
