FROM rhamdeew/docker-php-8-fpm-alpine
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer require spomky-labs/lexik-jose-bridge:">=3.0.2" web-token/jwt-signature-algorithm-rsa php-http/httplug-bundle php-http/curl-client nyholm/psr7 php-http/cache-plugin annotations

WORKDIR /app
