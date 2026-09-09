FROM php:8.5-fpm-bookworm

ARG COMPOSER_VERSION=2.8.10

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        wget \
        curl \
        bash \
        default-mysql-client \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        librabbitmq-dev \
        libssl-dev \
        libxslt1-dev \
        libgmp-dev \
        zlib1g-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        zip \
        bcmath \
        gmp \
        sockets \
        pcntl \
        soap \
        xsl \
        gd \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && rm -rf /var/lib/apt/lists/*

# json, dom and libxml are bundled with this PHP build.

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker-entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

WORKDIR /var/www/html

RUN git config --global --add safe.directory /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm"]
