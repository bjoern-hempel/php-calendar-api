# Use debian:bullseye-slim image
FROM debian:bullseye-slim

ENV PHP_VERSION 8.1.4
ENV PHP_VERSION_MINOR 8.1
ENV PHP_RUN_DIRECTORY /run/php
ENV PHP_FPM_PORT 9000
ENV PHP_GD_VERSION 2.3.0
ENV COMPOSER_VERSION 2.3.5
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV NODE_VERSION_MAYOR 16
ENV WORK_DIRECTORY /var/www/web

# Working dir
WORKDIR $WORK_DIRECTORY

# Install applications
RUN    apt-get update \
	&& apt-get -y install \
        apt-transport-https \
        ca-certificates \
        cron \
        curl \
        git  \
        imagemagick \
        lsb-release \
        supervisor \
        unzip \
        wget \
        zip \
	&& wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
	&& (echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list) \
    && (echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' > /etc/apt/sources.list.d/symfony-cli.list) \
    && (curl -sL https://deb.nodesource.com/setup_${NODE_VERSION_MAYOR}.x | bash -) \
	&& apt-get update \
    && apt-get upgrade -y \
	&& apt-get -y install \
        libavif-dev \
        nodejs \
        readline-common \
        symfony-cli \
        zlib1g-dev \
        php${PHP_VERSION_MINOR}-cli \
        php${PHP_VERSION_MINOR}-fpm \
        php${PHP_VERSION_MINOR}-curl \
        php${PHP_VERSION_MINOR}-gd \
        php${PHP_VERSION_MINOR}-imagick \
        php${PHP_VERSION_MINOR}-intl \
        php${PHP_VERSION_MINOR}-mbstring \
        php${PHP_VERSION_MINOR}-mysql \
        php${PHP_VERSION_MINOR}-opcache \
        php${PHP_VERSION_MINOR}-soap  \
        php${PHP_VERSION_MINOR}-sqlite3  \
        php${PHP_VERSION_MINOR}-xml \
        php${PHP_VERSION_MINOR}-zip \
    && (curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}) \
    && npm install -g npm \
    && npm install --global yarn \
    && mkdir -p /run/php \
    && sed -ri "s/^listen = .+$/listen = $PHP_FPM_PORT/" /etc/php/${PHP_VERSION_MINOR}/fpm/pool.d/www.conf \
    && sed -ri "s/^;clear_env = no$/clear_env = no/" /etc/php/${PHP_VERSION_MINOR}/fpm/pool.d/www.conf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Switch to production configuration
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Add opcache.ini
COPY conf.d/opcache.ini /etc/php/${PHP_VERSION_MINOR}/fpm/conf.d/opcache.ini

# Add config.ini
COPY conf.d/config.ini /etc/php/${PHP_VERSION_MINOR}/fpm/conf.d/config.ini

# Add supervisor messenger-worker.conf
COPY conf.d/messenger-worker.conf  /etc/supervisor/conf.d/messenger-worker.conf

# Connect log files
RUN ln -sf /dev/stdout /var/log/php${PHP_VERSION_MINOR}-fpm.log

# Expose PHP FPM 9000 port
EXPOSE $PHP_FPM_PORT

# keep container running (ENTRYPOINT ["tail", "-f", "/dev/null"])
CMD ["/usr/sbin/php-fpm8.1", "-F"]
