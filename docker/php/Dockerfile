# ./docker/php/Dockerfile

FROM php:8.0.3-fpm-alpine

WORKDIR /usr/src/app

RUN apk --no-cache update \
    && apk --no-cache add bash zsh \
    && docker-php-ext-install pdo_mysql

# Install Symfony Cli

RUN apk --no-cache update \
  && apk --no-cache add bash zsh \
  && docker-php-ext-install pdo_mysql \
  && wget https://get.symfony.com/cli/installer -O - | bash \
  && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY --chown=1000:1000 apps/api /usr/src/app

COPY  apps/api /usr/src/app

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# RUN composer install --no-scripts --prefer-dist --no-interaction