# Use PHP 8.4 with Apache
FROM php:8.4-apache

# Install system dependencies
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
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (for caching)
COPY composer.json composer.lock ./

# Copy artisan and bootstrap/app.php for package discovery
COPY artisan bootstrap/app.php ./

# Install PHP dependencies (skip scripts to avoid artisan error during build)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy application code
COPY . .

# Copy package.json and install JS dependencies
COPY package.json package-lock.json* ./
RUN npm ci && npm run build

# Run package discovery now that all files are present
RUN php artisan package:discover --ansi

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Start script
CMD php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8000