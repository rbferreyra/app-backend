FROM php:8.4-fpm

ARG UID=1000
ARG USER=forge

# Install system dependencies (mirrors Forge's App Server provisioning)
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    zip \
    autoconf \
    make \
    g++ \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libmemcached-dev \
    libgmp-dev \
    libicu-dev \
    supervisor \
    cron \
    nano \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js 22 LTS (same as Forge)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
        opcache \
        intl \
        gmp \
        sockets

# Install Redis extension (via source — avoids PECL unpack issues on PHP 8.4)
RUN curl -fsSL https://github.com/phpredis/phpredis/archive/6.1.0.tar.gz | tar xz \
    && cd phpredis-6.1.0 \
    && phpize && ./configure && make && make install \
    && docker-php-ext-enable redis \
    && cd .. && rm -rf phpredis-6.1.0

# Install Memcached extension (via source)
RUN curl -fsSL https://github.com/php-memcached-dev/php-memcached/archive/v3.2.0.tar.gz | tar xz \
    && cd php-memcached-3.2.0 \
    && phpize && ./configure && make && make install \
    && docker-php-ext-enable memcached \
    && cd .. && rm -rf php-memcached-3.2.0

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user matching Forge's 'forge' user pattern
RUN groupadd --gid ${UID} ${USER} \
    && useradd --uid ${UID} --gid ${USER} --shell /bin/bash --create-home ${USER}

WORKDIR /var/www

USER ${USER}

EXPOSE 9000

CMD ["php-fpm"]
