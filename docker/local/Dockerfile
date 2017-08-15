FROM php:7.1-cli

MAINTAINER Cédric Dugat <cedric@dugat.me>

# APT update and basic packages
RUN set -xe && \
    apt-get update && \
    apt-get install -y \
        wget \
        curl \
        git \
        zsh

# PHP configuration
RUN echo "phar.readonly = 0" >> /usr/local/etc/php/conf.d/php.ini

# PHP Xdebug
RUN pecl install -o -f xdebug \
          && rm -rf /tmp/pear \
          && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20160303/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini \
          && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/xdebug.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/bin
ENV PATH /root/.composer/vendor/bin:$PATH

RUN chsh -s /bin/zsh

WORKDIR /app
