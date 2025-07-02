# Use the official PHP image with Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Enable Apache rewrite module


# Set working directory
WORKDIR /var/www/html

# Copy entire Laravel project first
COPY . .
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite
RUN a2enmod rewrite headers

# Copy composer and install dependencies
COPY --from=composer:2.7.9 /usr/bin/composer /usr/bin/composer

# Install dependencies properly
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --no-scripts

# Run Laravel-specific scripts AFTER the project exists
RUN php artisan package:discover --ansi || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/vendor

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
