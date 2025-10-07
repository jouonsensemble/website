ARG PHP_VERSION=${PHP_VERSION}

FROM phpdockerio/php:${PHP_VERSION}-fpm
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

ARG PHP_VERSION=${PHP_VERSION}

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
    php${PHP_VERSION}-intl \
    language-pack-fr \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
