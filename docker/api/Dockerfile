FROM webdevops/php-nginx-dev:8.2
WORKDIR /app

COPY composer.json composer.lock* ./

#--no-dev
RUN composer install  --optimize-autoloader

COPY api/src/ ./src/
COPY api/public/ ./public/

RUN chown -R application:application /app

ENV WEB_DOCUMENT_ROOT=/app/public