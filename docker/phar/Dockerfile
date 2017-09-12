FROM php:7.1-alpine

MAINTAINER Cédric Dugat <cedric@dugat.me>

RUN set -xe && apk update && \
    apk add --update wget && \
    rm -rf /var/cache/apk/*

RUN wget --quiet --no-check-certificate -O \
    /root/docker-arch-install \
    https://raw.githubusercontent.com/Ph3nol/Docker-Arch/master/install.sh

RUN chmod +x /root/docker-arch-install
RUN /bin/sh /root/docker-arch-install

ENTRYPOINT ["/usr/local/bin/docker-arch"]
