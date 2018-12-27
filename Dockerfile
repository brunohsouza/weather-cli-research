FROM php:7.2-apache

COPY php.ini /usr/local/etc/php
RUN apt-get update && apt-get install -y libfreetype6-dev libmcrypt-dev mysql-client\
&& docker-php-ext install pdo_mysql mysqli gd iconv\
&& docker-php-ext install mbstring \ 
&& docker-php-ext install mcrypt

COPY ./weather-api.conf /etc/apache2/sites-availabe/
COPY ./hosts /etc/hosts
RUN a2enmod rewrite

RUN a2enmod mcrypt 
RUN service apache2 restart
WORKDIR /etc/apache2/sistes-availabe/
RUN a2ensite weather-api.conf

EXPOSE 80
