FROM php:8.2-fpm-bookworm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Tooling: extension installer (pulls only the runtime libs each extension needs) and Composer
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# System packages + PHP extensions in a single layer so the apt cache cleanup actually shrinks the image
RUN apt-get update \
    && apt-get install -y --no-install-recommends git curl zip unzip \
    && install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd zip imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/*

RUN mkdir -p /var/log/supervisor

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
