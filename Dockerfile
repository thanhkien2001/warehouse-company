FROM php:8.4-cli

# Cài thư viện cần thiết cho GD + Laravel
RUN apt-get update && apt-get install -y \
    git unzip curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy source code
COPY . .

# Cài dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel optimize
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# Expose port
EXPOSE 8080

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=8080
