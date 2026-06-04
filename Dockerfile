# 1. Use an official PHP image with Apache server pre-installed
FROM php:8.2-apache

# 2. Install native system drivers required for Laravel and PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip

# 3. Enable Apache mod_rewrite rules for clean Laravel routing
RUN a2enmod rewrite

# 4. Point Apache's document root path to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. Install Composer globally inside the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy all your project files into the container web directory
COPY . /var/www/html

# 7. Set working directory permissions for storage paths
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Execute optimized Composer deployment installation tasks
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80