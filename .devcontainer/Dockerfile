FROM dunglas/frankenphp:php8.4

# https://github.com/mlocati/docker-php-extension-installer
RUN install-php-extensions \
    @composer \
    intl \
    opcache \
    zip
