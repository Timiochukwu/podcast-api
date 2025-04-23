FROM php:8.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    build-base \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -h /home/www -D www

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www/

# Copy existing application directory permissions
COPY --chown=www:www . /var/www/

# Change current user to www
USER www

# Expose port 9000
EXPOSE 9000

# Start php-fpm server
CMD ["php-fpm"]