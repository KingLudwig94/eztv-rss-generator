# Tells the image to use the latest version of PHP
FROM php:8.2.1-apache

COPY ./ /var/www/html/
EXPOSE 80