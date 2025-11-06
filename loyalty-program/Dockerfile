FROM php:8.3-fpm

# Install system dependencies, git, and PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    zip \
    unzip \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/* \
    # Install common extensions
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Set the working directory inside the container
WORKDIR /var/www/html

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the composer files to install dependencies
COPY composer.json composer.lock ./

# Install Composer dependencies (production environment to keep it clean)
# We run this BEFORE copying the entire app to leverage Docker layer caching
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Copy the entire Laravel application
COPY . .

# Adjust permissions for storage and cache directories
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port 8000, where the Laravel development server will run
EXPOSE 8000