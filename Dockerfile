# syntax=docker/dockerfile:1

FROM php:8.2-apache

ARG COMPOSER_NO_DEV=1

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1

# Dependensi sistem + ffmpeg (wajib untuk konversi HLS)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ffmpeg \
        curl \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        zlib1g-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" intl mysqli pdo_mysql zip gd opcache \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# Konfigurasi Apache & PHP
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/zz-media-streamer.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependency lebih dulu agar layer cache efisien
COPY composer.json composer.lock ./
RUN if [ "$COMPOSER_NO_DEV" = "1" ]; then \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    else \
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    fi

# Salin aplikasi
COPY . .

# Folder writable + akses
RUN mkdir -p \
        writable/cache \
        writable/logs \
        writable/session \
        writable/uploads \
        writable/hls \
    && chown -R www-data:www-data writable

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=5 \
    CMD curl -fsS http://localhost/ >/dev/null || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
