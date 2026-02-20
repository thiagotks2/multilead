FROM php:8.5-fpm-alpine

RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    libwebp \
    curl \
    git \
    unzip \
    nano \
    supervisor \
    nginx \
    libpq \
    libzip \
    icu-libs \
    tidyhtml \
    libxslt \
    oniguruma \
    linux-headers \
    nodejs \
    npm

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    libxml2-dev \
    libxslt-dev \
    postgresql-dev \
    tidyhtml-dev \
    oniguruma-dev \
    zlib-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl

RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-install -j$(nproc) bcmath
RUN docker-php-ext-install -j$(nproc) pdo_pgsql
RUN docker-php-ext-install -j$(nproc) exif
RUN docker-php-ext-install -j$(nproc) pcntl
RUN docker-php-ext-install -j$(nproc) sockets
RUN docker-php-ext-install -j$(nproc) xsl
RUN docker-php-ext-install -j$(nproc) tidy
RUN docker-php-ext-install -j$(nproc) gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer global require laravel/installer
ENV PATH="/root/.composer/vendor/bin:${PATH}"

COPY ./php-fpm.conf /usr/local/etc/php-fpm.conf

RUN mkdir -p /etc/nginx/http.d
COPY ./default.conf /etc/nginx/http.d/default.conf

COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN mkdir -p /var/log/nginx /var/run/php /var/www/html \
    && touch /var/log/reverb.log /var/log/reverb-error.log \
    && chown -R www-data:www-data /var/log /var/www/html

RUN apk del .build-deps

WORKDIR /var/www/html
ENTRYPOINT ["/entrypoint.sh"]