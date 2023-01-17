# Tells the image to use the latest version of PHP
FROM php:8.1.1-apache

COPY ./ /var/www/html/
#EXPOSE 80