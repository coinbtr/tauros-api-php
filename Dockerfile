FROM php:7.2-apache
LABEL maintainer="JySa65 <jysa65.dev@gmail.com>"

WORKDIR /var/www/html/

ADD composer.json .

RUN apt-get update && \
    apt-get install -y \
    apt-utils \
    zip \
    unzip \
    git && \
    curl -sSk https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install && \
    composer dump-autoload && \
    apt-get autoremove -y
