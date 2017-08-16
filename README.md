# Docker Arch(itect)

[![Latest Stable Version](https://img.shields.io/packagist/v/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![License](https://img.shields.io/packagist/l/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![Total Downloads](https://img.shields.io/packagist/dt/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![Build Status](https://secure.travis-ci.org/Ph3nol/Docker-Arch.png)](http://travis-ci.org/Ph3nol/Docker-Arch)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/acb7b2ff-0aa1-47bf-a0a9-7b944c36b7c4/big.png)](https://insight.sensiolabs.com/projects/acb7b2ff-0aa1-47bf-a0a9-7b944c36b7c4)

## Local installation (PHP 7.1+ required)

To install, just get `install.sh` file, and launch it.
Here is an example with `curl` usage:

```
curl -L -s -o docker-arch-install \
    https://raw.githubusercontent.com/Ph3nol/Docker-Arch/master/install.sh
chmod +x docker-arch-install
./docker-arch-install
```

## Docker installation

From dedicated [Docker image](https://hub.docker.com/r/ph3nol/docker-arch/).

```
docker pull ph3nol/docker-arch
```

Build from local library Dockerfile:

```
docker build --force-rm --no-cache -t ph3nol/docker-arch ./docker/phar/
```

## Build Docker environment

Go to your project root directory, containing `.docker-arch.json` file [see examples](examples/).

Generate Docker environment from installed package:

```
docker-arch generate
```

Or generate from Docker image:

```
docker run -it -v $(pwd):/destination ph3nol/docker-arch generate /destination
```

Then, from your project root directory:

```
.docker-arch/do build    # Build containers (like `docker-compose up --build -d`)
.docker-arch/do start    # Start containers (like `docker-compose up -d`)
.docker-arch/do shell    # Access a container Shell
.docker-arch/do dc       # Access `docker-compose` with your configuration, for specific requests
.docker-arch/do stop     # Stop containers (like `docker-compose stop`)
.docker-arch/do clean    # Stop/Remove containers and reset linked volumes
```

## Development

* Install Docker Arch (see the installation dedicated part)
* Clone this project from Github
* `composer install`
* Build Docker Arch environment with `docker-arch build` (library root directory)
* Use `.docker-arch/do` (see the part above)

## To do

* Improve README and documentation
* Implement a UI to generate JSON configuration, with all possible options
* Add `.docker-arch.json` generation (from `docker-arch init`)
* Publish official Docker ph3nol/docker-arch image
* Add some services (ElasticSearch, RabbitMQ, MongoDB, etc.)
* Write fucking unit Tests (Atoum powered)
