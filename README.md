# event-machine-skeleton
Dockerized skeleton for prooph software [Event Machine](https://github.com/proophsoftware/event-machine)

## Installation
Please make sure you have installed [Docker](https://docs.docker.com/engine/installation/ "Install Docker") and [Docker Compose](https://docs.docker.com/compose/install/ "Install Docker Compose").

```bash
$ docker run --rm -it -v $(pwd):/app prooph/composer:7.1 create-project proophsoftware/event-machine-skeleton <your_project_name>
$ cd <your_project_name>
$ sudo chown $(id -u -n):$(id -g -n) . -R
$ docker-compose up -d
$ docker-compose run php php scripts/create_event_stream.php
```
## Tutorial

[https://proophsoftware.github.io/event-machine/tutorial/](https://proophsoftware.github.io/event-machine/tutorial/)

## Demo

We've prepared a `demo` branch that contains a small service called `BuildingMgmt`. It will show you the basics of
event machine and the skeleton structure. To run the demo you have to clone the skeleton instead of
`composer create-project` so that your local copy is still connected to the github repo.

*Note: Event Machine is very flexible in the way how you organize your code. The skeleton just gives an example of a possible structure.
The default way is to use static aggregate methods as pure functions. However, it is also possible to use stateful OOP aggregates. Take a look at the [tutorial](https://proophsoftware.github.io/event-machine/tutorial/) for more information.

```bash
$ git clone https://github.com/proophsoftware/event-machine-skeleton.git prooph-building-mgmt
$ cd prooph-building-mgmt
$ git checkout demo
$ docker run --rm -it -v $(pwd):/app prooph/composer:7.1 install
$ docker-compose up -d
$ docker-compose run php php scripts/create_event_stream.php
```

Head over to `http://localhost:8080` to check if the containers are up and running.
You should see a "It works" message.

### Database

The skeleton uses a single Postgres database for both write and read model. The write model is event sourced and writes
all events to prooph/event-store. The read model is created by projections (see `src/Api/Projection`) and is also stored in
the Postgres DB. Read model tables have the prefix `em_ds_` and end with a version number which is by default `0_1_0`.

You can connect to the Postgres DB using following credentials (listed also in `app.env`):

```dotenv
PDO_DSN=pgsql:host=postgres port=5432 dbname=event_machine
PDO_USER=postgres
PDO_PWD=
```

*Note: The DB runs insight a docker container. Use `localhost` as host name if you want to connect from your host system!*

### RabbitMQ

The skeleton uses RabbitMQ as a message broker with a preconfigured exchange called `ui-exchange` and a corresponding
queue called `ui-queue`. You can open the Rabbit Mgmt UI in the browser: `http://localhost:8081` and login with `user: prooph`
and `password: prooph`.

The skeleton also contains a demo JS client which connects to a websocket and consumes messages from the `ui-queue`.
Open `http://localhost:8080/ws.html` in your browser and forward events on the queue with `$eventMachine->on(Event::MY_EVENT, UiExchange::class)`.
Check `src/Api/Listener` for an example.

## Unit and Integration Tests

We've prepared a `BaseTestCase` located in `tests`. Extend your test cases from that class to get access to some very useful test helpers.
Check the tutorial for a detailed explanation.

You can run the tests using docker:

```bash
docker-compose run php php vendor/bin/phpunit
```

## Troubleshooting

With the command `docker-compose ps` you can list the running containers. This should look like the following list:

```bash
                    Name                                   Command               State                             Ports                           
---------------------------------------------------------------------------------------------------------------------------------------------------
proophbuildingmgmt_event_machine_projection_1   docker-php-entrypoint php  ...   Up                                                                
proophbuildingmgmt_nginx_1                      nginx -g daemon off;             Up      0.0.0.0:443->443/tcp, 0.0.0.0:8080->80/tcp                
proophbuildingmgmt_php_1                        docker-php-entrypoint php-fpm    Up      9000/tcp                                                  
proophbuildingmgmt_postgres_1                   docker-entrypoint.sh postgres    Up      0.0.0.0:5432->5432/tcp                                    
proophbuildingmgmt_rabbit_1                     docker-entrypoint.sh rabbi ...   Up      0.0.0.0:8081->15671/tcp, 15672/tcp,                       
                                                                                         0.0.0.0:15691->15691/tcp, 25672/tcp, 4369/tcp, 5671/tcp,  
                                                                                         5672/tcp 
```

Make sure that all required ports are available on your machine. If not you can modify port mapping in the `docker-compose.yml`.

### Have you tried turning it off and on again?

If something does not work as expected try to restart the containers first:

```bash
$ docker-compose down
$ docker-compose up -d
```

### Projection reset

Event machine uses a single projection process (read more about prooph projections in the [prooph docs](http://docs.getprooph.org/event-store/projections.html#3-4)).
You can register your own projections in event machine which are all handled by the one background process that is started automatically
with the script `bin/event_machine_projection.php`. Also see `docker-compose.yml`. The projection process runs in its own docker container
which is restarted by docker in case of a failure. The projection process dies from time to time to catch up with your latest code changes.

If you recognize that your read models are not up-to-date or you need to reset the read model you can use this command:

```bash
$ docker-compose run php php bin/reset.php
```

If you still have trouble try a step by step approach:

```bash
$ docker-compose stop event_machine_projection
$ docker-compose run php php bin/reset.php
$ docker-compose up -d
```

You can also check the projection log with:

```bash
$ docker-compose logs -f event_machine_projection
```

### Swagger UI is not updated

When you add new commands or queries in event machine the Swagger UI will not automatically reread the schema from the backend.
Simply reload the UI or press `Explore` button.


## Batteries Included

You know the headline from Docker, right?
The Event Machine skeleton follows the same principle. It ships with a default set up so that you can start without messing around with configuration and such.
The default set up is likely not what you want to use in production. The skeleton can be and **should be** adapted.

Focus of the skeleton is to provide *an easy to use development environment*, hence it uses default settings of Postgres and RabbitMQ containers.
**Make sure to secure the containers before you deploy them anywhere!** You should build and use your own docker containers in production anyway.
And if you cannot or don't want to use Docker then provide the needed infrastructure the way you prefer and just point event machine to it by adjusting configuration.

## Powered by prooph software

[![prooph software](https://github.com/codeliner/php-ddd-cargo-sample/blob/master/docs/assets/prooph-software-logo.png)](http://prooph.de)

Event Machine is maintained by the [prooph software team](http://prooph-software.de/). The source code of Event Machine 
is open sourced along with an API documentation and a getting started demo. Prooph software offers commercial support and workshops
for Event Machine as well as for the [prooph components](http://getprooph.org/).

If you are interested in this offer or need project support please [get in touch](http://getprooph.org/#get-in-touch).
