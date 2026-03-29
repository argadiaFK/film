FROM dunglas/frankenphp:latest-php8.3

# Install system dependencies and PostgreSQL 16 client
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    gnupg2 \
    lsb-release \
    && echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor -o /etc/apt/trusted.gpg.d/postgresql.gpg \
    && apt-get update \
    && apt-get install -y postgresql-client-16 \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# PHP Configuration for performance
RUN echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy Caddyfile first
COPY .docker/Caddyfile /etc/caddy/Caddyfile

# Copy application files
COPY . /app

# Install PHP dependencies
# Force dummy environment variables during build to prevent Laravel from attempting
# to connect to non-existent Redis/Postgres databases during `package:discover`
RUN DB_CONNECTION=sqlite DB_DATABASE=/tmp/db.sqlite CACHE_STORE=file SESSION_DRIVER=file QUEUE_CONNECTION=sync REDIS_CLIENT=null \
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Copy entrypoint script
COPY .docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80 443

# Run entrypoint (clears cache, then starts FrankenPHP)
ENTRYPOINT ["/entrypoint.sh"]
