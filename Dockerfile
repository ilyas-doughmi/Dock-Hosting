FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    docker.io \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql mysqli zip

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

RUN mkdir -p /var/www/html/users/Projects && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 777 /var/www/html/users

RUN echo "ServerName dockhosting.dev" >> /etc/apache2/apache2.conf
