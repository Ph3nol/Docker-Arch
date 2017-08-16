FROM php:7.1-cli

MAINTAINER Cédric Dugat <cedric@dugat.me>

# APT update and basic packages
RUN set -xe && \
    apt-get update && \
    apt-get install -y wget

RUN wget --quiet --no-check-certificate -O \
    /root/docker-arch-install \
    https://raw.githubusercontent.com/Ph3nol/Docker-Arch/master/install.sh

RUN chmod +x /root/docker-arch-install
RUN /root/docker-arch-install

ENTRYPOINT ["/usr/local/bin/docker-arch"]
