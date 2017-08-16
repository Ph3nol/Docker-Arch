# Docker Arch(itect)

[![Latest Stable Version](https://img.shields.io/packagist/v/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![License](https://img.shields.io/packagist/l/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![Total Downloads](https://img.shields.io/packagist/dt/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![Build Status](https://secure.travis-ci.org/Ph3nol/Docker-Arch.png)](http://travis-ci.org/Ph3nol/Docker-Arch)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/acb7b2ff-0aa1-47bf-a0a9-7b944c36b7c4/big.png)](https://insight.sensiolabs.com/projects/acb7b2ff-0aa1-47bf-a0a9-7b944c36b7c4)

## Local installation

To install, just get `install.sh` file, and launch it.
Here is an example with `curl` usage:

```
curl -L -s -o /tmp/docker-arch-install https://raw.githubusercontent.com/Ph3nol/Docker-Arch/master/install.sh
chmod +x /tmp/docker-arch-install
./tmp/docker-arch-install
```

## Docker installation

From dedicated [Docker image](https://hub.docker.com/r/ph3nol/docker-arch/).

```
docker pull ph3nol/docker-arch
docker run -it -v $(pwd):/destination ph3nol/docker-arch generate /destination
```

Build from local library Dockerfile:

```
docker build --force-rm --no-cache -t ph3nol/docker-arch ./docker/phar/
```

## Docker Arch JSON file configuration

[See some projects configurations examples](examples/).

## Development

```
docker build --force-rm --no-cache -t ph3nol/docker-arch-local ./docker/local/
docker run -it -v /path/to/the/docker-arch/library:/app -v $(pwd):/destination ph3nol/docker-arch-local php
docker run -it -v /path/to/the/docker-arch/library:/app -v $(pwd):/destination ph3nol/docker-arch-local composer
docker run -it -v /path/to/the/docker-arch/library:/app -v $(pwd):/destination ph3nol/docker-arch-local bin/docker-arch generate /destination
docker run -it -v /path/to/the/docker-arch/library:/app -v $(pwd):/destination ph3nol/docker-arch-local composer build-phar
```

## To do

* Improve README and documentation
* Implement a UI to generate JSON configuration, with all possible options
* Add `.docker-arch.json` generation (from `docker-arch init`)
* Publish official Docker ph3nol/docker-arch image
* Add some services (ElasticSearch, RabbitMQ, MongoDB, etc.)
* Write fucking unit Tests (Atoum powered)
