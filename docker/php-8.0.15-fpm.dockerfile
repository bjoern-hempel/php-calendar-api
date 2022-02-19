# Use PHP 8.0.15 fpm image
FROM php:8.0.15-fpm

# Working dir
WORKDIR /var/www/web

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Add opcache.ini
COPY php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Add config.ini
COPY php/conf.d/config.ini /usr/local/etc/php/conf.d/config.ini

# Download and execute the NodeSource installation script
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -

# Install applications
RUN apt-get update && apt-get install -y \
    wget \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    imagemagick \
    libmagickwand-dev \
    nodejs \
    supervisor --no-install-recommends

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Pecl install alternative
RUN mkdir -p /usr/src/php/ext/imagick; \
    curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1

# Add supervisor messenger-worker.conf
COPY php/conf.d/messenger-worker.conf  /etc/supervisor/conf.d/messenger-worker.conf

# Install php extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install mysqli pdo pdo_mysql zip intl opcache soap gd imagick

# Install npm
RUN npm install -g npm

# Install yarn
RUN npm install --global yarn

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.2

# Install symfony cli
RUN wget https://get.symfony.com/cli/installer -O - | bash

# Install symfony cli globally
RUN mv /root/.symfony/bin/symfony /usr/local/bin/.