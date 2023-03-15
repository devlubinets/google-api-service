FROM php:7.4-cli

COPY docker/php/dev/configuration /usr/local/etc/php

RUN apt-get update && apt-get install -y \
        curl \
        unzip \
        libssl-dev \
        libcurl4-openssl-dev \
        zlib1g-dev \
        && pecl install grpc \
        && docker-php-ext-enable grpc \
        && apt-get remove -y libssl-dev \
        && apt-get autoremove -y \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/*

RUN pecl install protobuf \
    && echo 'extension=protobuf.so' > /usr/local/etc/php/php.ini

COPY ./ /app
WORKDIR /app

RUN pecl install xdebug-2.9.8

CMD [ "php", "index.php" ]