FROM php:7.4-apache

ARG APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV APACHE_DOCUMENT_ROOT=${APACHE_DOCUMENT_ROOT}

# Set the locale
RUN apt-get clean && apt-get update && apt-get install -y locales
RUN locale-gen en_US.UTF-8

# Install tools
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install wget nano lnav git zip unzip sudo

# Install php requirements
RUN apt-get install -y libzip-dev zlib1g-dev libicu-dev \
    && docker-php-ext-install pdo_mysql intl zip opcache

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Apache configuration
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite headers

RUN apt-get install --no-install-recommends --assume-yes --quiet ca-certificates curl git \
    && rm -rf /var/lib/apt/lists/*

# Clean-up installation.
RUN DEBIAN_FRONTEND=noninteractive apt-get autoclean && apt-get autoremove
