FROM php:8.4-fpm

ARG UID=1000
ARG USER=forge

RUN apt-get update && apt-get install -y \
    cron \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libgmp-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        pcntl \
        bcmath \
        zip \
        xml \
        sockets \
        gmp

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create forge user
RUN groupadd --gid ${UID} ${USER} \
    && useradd --uid ${UID} --gid ${USER} --shell /bin/bash --create-home ${USER}

# Laravel scheduler cron — runs every minute (mirrors Forge's scheduler)
RUN echo "* * * * * forge php /var/www/artisan schedule:run >> /var/www/storage/logs/cron.log 2>&1" \
    | crontab -

WORKDIR /var/www

CMD ["cron", "-f"]
