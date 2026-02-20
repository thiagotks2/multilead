FROM php:8.5-fpm-alpine

# 1. Instalar dependências de sistema permanentes e Node.js/NPM
RUN apk add --no-cache \
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

# 2. Dependências de compilação (Headers)
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

# 3. Configurações Prévias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl

# 4. Instalação das extensões (Separadas para Debug)
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

# 5. Composer e Laravel Installer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer global require laravel/installer
ENV PATH="/root/.composer/vendor/bin:${PATH}"

COPY ./php-fpm.conf /usr/local/etc/php-fpm.conf

# Alpine usa http.d para Nginx
RUN mkdir -p /etc/nginx/http.d
COPY ./default.conf /etc/nginx/http.d/default.conf

COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# 7. Estrutura de Logs e Permissões
RUN mkdir -p /var/log/nginx /var/run/php /var/www/html \
    && touch /var/log/reverb.log /var/log/reverb-error.log \
    && chown -R www-data:www-data /var/log /var/www/html

# 8. Limpeza de dependências de build
RUN apk del .build-deps

WORKDIR /var/www/html
ENTRYPOINT ["/entrypoint.sh"]