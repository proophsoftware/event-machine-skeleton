# event-machine-skeleton
Dockerized skeleton for prooph software [Event Machine](https://github.com/proophsoftware/event-machine)

## Installation

```bash
$ git clone git@github.com:proophsoftware/event-machine-skeleton.git <my_project>
$ cd <my_project>
$ rm -rf .git
$ docker run --rm -it -v $(pwd):/app prooph/composer:7.1 install
$ docker-compose up -d
$ docker-compose run php php scripts/create_event_stream.php
```

## What's insight?

The skeleton ships with a default set up so that you can start without messing around with configuration and such.
Don't worry if the default set up is not exactly what you want to use in production. The skeleton can be adopted were 
needed. 

### Configuration and dependency management

[bitexpert/disco](https://talks.bitexpert.de/phpugmrn16-disco/#/) is used as PSR-11 compatible container. Disco has 
the advantage that dependency set up is done in [one place](...) and [configuration](...) is reduced to a bare minimum.

It should be easy to follow the default set up and reproduce it if you prefer another PSR-11 compatible DI container.

## Usage

