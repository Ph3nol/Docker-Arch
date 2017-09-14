FROM php:7.1-alpine

MAINTAINER Cédric Dugat <cedric@dugat.me>

RUN set -xe && apk update && \
    apk add --update wget && \
    rm -rf /var/cache/apk/*

RUN wget -qO - https://raw.githubusercontent.com/Ph3nol/Docker-Arch/master/install.sh | sh

ENTRYPOINT ["/usr/local/bin/docker-arch"]
