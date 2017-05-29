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
