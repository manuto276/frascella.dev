# Stage 1: Composer dependencies
FROM composer:latest AS composer

# Stage 2: Final PHP 8.2 image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    git-flow \
    nano \
    tree \
    unzip \
    curl \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    zip \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libpq-dev \
    libsqlite3-dev \
    libreadline-dev \
    libedit-dev \
    libxslt-dev \
    libffi-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        zip \
        opcache \
        xml

# Install Nginx
RUN apt-get install -y nginx \
    procps \
    && rm /etc/nginx/sites-enabled/default \
    && mkdir -p /var/www/html/public \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copy Nginx configuration
COPY .devcontainer/default.conf /etc/nginx/sites-available/default

# Optional: Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy Composer dependencies from the composer stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html