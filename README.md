# Docker Arch(itect)

[![Latest Stable Version](https://img.shields.io/packagist/v/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![License](https://img.shields.io/packagist/l/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![Total Downloads](https://img.shields.io/packagist/dt/ph3nol/docker-arch.svg)](https://packagist.org/packages/ph3nol/docker-arch)
[![Build Status](https://secure.travis-ci.org/Ph3nol/Docker-Arch.png)](http://travis-ci.org/Ph3nol/Docker-Arch)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/acb7b2ff-0aa1-47bf-a0a9-7b944c36b7c4/big.png)](https://insight.sensiolabs.com/projects/acb7b2ff-0aa1-47bf-a0a9-7b944c36b7c4)

## Demonstration

[![asciicast](https://asciinema.org/a/137142.png)](https://asciinema.org/a/137142?autoplay=1)

## Local installation (PHP 7.1+ required)

To install, just get `install.sh` file, and launch it.
Here is an example with `curl` usage:

```
curl -L -s -o docker-arch-install \
    https://raw.githubusercontent.com/Ph3nol/Docker-Arch/master/install.sh
chmod +x docker-arch-install
./docker-arch-install
```

## Docker Architecture generation

From dedicated [Docker image](https://hub.docker.com/r/ph3nol/docker-arch/).

```
docker pull ph3nol/docker-arch
```

Go to your project root directory and create/edit `.docker-arch.yml` file [see examples](examples/).

Then generate your Docker environment:
 
```
docker run -it -v $(PWD):/destination ph3nol/docker-arch:latest generate /destination
```

Finally, use `.docker-arch/do` script, from the project, for somes actions:

```
.docker-arch/do build    # Build containers (like `docker-compose up --build -d`)
.docker-arch/do start    # Start containers (like `docker-compose up -d`)
.docker-arch/do shell    # Access a container Shell
.docker-arch/do dc       # Access `docker-compose` with your configuration, for specific requests
.docker-arch/do stop     # Stop containers (like `docker-compose stop`)
.docker-arch/do clean    # Stop/Remove containers and reset linked volumes
...
```

To use the Docker image so fast, you can use these aliases/functions:

```
function docker-arch {
    case "$1" in
        *)
            if [ -z $2 ]; then DESTINATION_PATH=$PWD; else DESTINATION_PATH=$2; fi
            if [[ "$DESTINATION_PATH" == "." ]]; then DESTINATION_PATH=$PWD; fi

            docker run -it -v $DESTINATION_PATH:/destination ph3nol/docker-arch:latest generate /destination
            ;;
    esac
}
alias dag="docker-arch generate"
alias dad=".docker-arch/do"
```

## To do

* Improve README and documentation
* Implement a UI to generate YML configuration, with all possible options
* Add some services (ElasticSearch, RabbitMQ, MongoDB, etc.)
* Write fucking unit Tests (Atoum powered)
