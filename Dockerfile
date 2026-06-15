FROM php:8.4-cli-alpine

RUN apk add --no-cache git unzip curl icu-dev oniguruma-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql intl mbstring xml ctype iconv tokenizer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copie les manifestes en premier — cache Docker si rien n'a changé
COPY composer.json composer.lock symfony.lock ./

# Plugins activés (symfony/runtime génère vendor/autoload_runtime.php)
# --no-scripts évite les auto-scripts qui appellent symfony-cmd
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction \
    --prefer-dist

COPY . .

# Env de build — les vraies valeurs sont injectées par Railway au runtime
ENV APP_ENV=prod \
    APP_SECRET=buildsecret \
    DATABASE_URL="sqlite:///:memory:"

# Télécharge les assets JS/CSS depuis les CDNs et installe les assets publics
RUN php bin/console importmap:install \
 && php bin/console assets:install

CMD ["sh", "-c", "php bin/console doctrine:migrations:migrate --no-interaction && php -S 0.0.0.0:${PORT:-8000} -t public/"]
