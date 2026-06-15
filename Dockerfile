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

# Build-time only — ARG values are NOT baked into the final image
# Runtime container gets DATABASE_URL and APP_SECRET from Railway env vars
ARG APP_SECRET=buildsecret
ARG DATABASE_URL="sqlite:///:memory:"

ENV APP_ENV=prod

RUN php bin/console importmap:install \
 && php bin/console assets:install \
 && php bin/console asset-map:compile

RUN chmod +x docker-entrypoint.sh

CMD ["./docker-entrypoint.sh"]
