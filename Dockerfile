FROM php:8.4-cli-alpine

RUN apk add --no-cache git unzip curl icu-dev oniguruma-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql intl mbstring xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock symfony.lock ./

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction \
    --prefer-dist

COPY . .

ENV APP_ENV=prod

# Download vendor JS/CSS from CDNs at build time (assets/vendor/)
# asset-map:compile runs at startup so the cache is always fresh
ARG APP_SECRET=buildsecret
ARG DATABASE_URL="sqlite:///:memory:"
RUN php bin/console importmap:install

RUN chmod +x docker-entrypoint.sh

CMD ["./docker-entrypoint.sh"]
