FROM php:8.0-apache
RUN docker-php-ext-install pdo_mysql mysqli && docker-php-ext-enable pdo_mysql mysqli
RUN apt-get update && apt-get upgrade -y