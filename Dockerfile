FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    sqlite3 \
    libsqlite3-dev

# Clear apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Install Node and build frontend assets
RUN apt-get update && apt-get install -y nodejs npm && npm install && npm run build

# Expose port
EXPOSE 8000

# Start script
CMD ["sh", "-c", "touch database/database.sqlite && php artisan migrate --force --seed && php artisan serve --host=0.0.0.0 --port=$PORT"]