# Tells the image to use the latest version of PHP
FROM php:8.1.1-apache

COPY ./ /var/www/html/
#EXPOSE 80


# To build: docker buildx build --push --platform linux/amd64,linux/arm64 --tag kingludwig94/eztvrssgenerator:latest .