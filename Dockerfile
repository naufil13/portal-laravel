FROM php:7.4.1-apache

USER root

WORKDIR /var/www/erx-portal-revamp

RUN apt update && apt install -y \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        zip \
        curl \
        unzip \
        nano \
        git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-source delete

COPY vhost.conf /etc/apache2/sites-available/000-default.conf

COPY ports.conf /etc/apache2/ports.conf

RUN git config --global user.name "junaidlathransoft"

RUN git config --global user.email "junaid.husnain@lathran.com"

RUN chmod -R 777 /var/www/erx-portal-revamp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/erx-portal-revamp && a2enmod rewrite
