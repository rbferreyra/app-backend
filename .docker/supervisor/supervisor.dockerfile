FROM php:8.4-fpm

ARG UID=1000
ARG USER=forge

RUN apt-get update && apt-get install -y \
    supervisor \
    autoconf \
    make \
    g++ \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libmemcached-dev \
    libgmp-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        pcntl \
        bcmath \
        zip \
        xml \
        opcache \
        intl \
        sockets \
        gmp

# Redis via source
RUN curl -fsSL https://github.com/phpredis/phpredis/archive/6.1.0.tar.gz | tar xz \
    && cd phpredis-6.1.0 \
    && phpize && ./configure && make && make install \
    && docker-php-ext-enable redis \
    && cd .. && rm -rf phpredis-6.1.0

# Memcached via source
RUN curl -fsSL https://github.com/php-memcached-dev/php-memcached/archive/v3.2.0.tar.gz | tar xz \
    && cd php-memcached-3.2.0 \
    && phpize && ./configure && make && make install \
    && docker-php-ext-enable memcached \
    && cd .. && rm -rf php-memcached-3.2.0

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupadd --gid ${UID} ${USER} \
    && useradd --uid ${UID} --gid ${USER} --shell /bin/bash --create-home ${USER}

COPY ./.docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
