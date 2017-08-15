FROM php:7.1-cli

MAINTAINER Cédric Dugat <cedric@dugat.me>

# APT update and basic packages
RUN set -xe && \
    apt-get update && \
    apt-get install -y wget

RUN wget --quiet --no-check-certificate -O /root/docker-arch https://github.com/Ph3nol/Docker-Arch/raw/master/dist/docker-arch.phar
RUN chmod +x /root/docker-arch

RUN cat /root/docker-arch

ENTRYPOINT ["/root/docker-arch"]
