# event-machine-skeleton
Dockerized skeleton for prooph software [Event Machine](https://github.com/proophsoftware/event-machine)

## Installation

```bash
$ composer create-project proophsoftware/event-machine-skeleton .
$ docker run --rm -it -v $(pwd):/app prooph/composer:7.1 install
$ docker-compose up -d
$ docker-compose run php php scripts/create_event_stream.php
```
## Quick Start

Head over to `http://localhost:8080` to check if the containers are up and running.
You should see a "It works" message.

Let's register a user in our new application. It is a small example that already illustrates the power of Event Machine.

1. Create a folder named "Model" in `src`
2. Add a class `UserDescription` and copy and paste the content from [that gist](https://gist.github.com/codeliner/20c3944195d0c60ceb2a4bbe6d3d2638#file-userdescription-php) into it.
3. Register `UserDescription` in Event Machine by adding it to `config/autoload/global.php` (see [gist](https://gist.github.com/codeliner/20c3944195d0c60ceb2a4bbe6d3d2638#file-global-php) )
4. Open [Postman](https://www.getpostman.com/) and import the [example collection](https://gist.github.com/codeliner/20c3944195d0c60ceb2a4bbe6d3d2638#file-eventmachine_example-postman_collection-json)
5. Send the "App.RegisterUser" request
6. Event Machine should return a 202 Accepted response with an empty body
7. Have a look at the postgres db, see `docker-compose.yml` of the skeleton for login credentials
8. The table starting with _ followed by a sha1 hash is your "event_stream" table. You should see a new `App.UserWasRegistered` event in it.
9. Test also the "App.ChangeUsername" request
10. Exercise: Implement the "App.ChangeEmail" use case

## Batteries Included

You know the headline from Docker, right?
The Event Machine skeleton follows the same principle. It ships with a default set up so that you can start without messing around with configuration and such.
The default set up is likely not what you want to use in production. The skeleton can be and **should be** adapted.

Focus of the skeleton is to provide *an easy to use development environment*, hence it uses default settings of Postgres, MongoDB and RabbitMQ containers.
**Make sure to secure the containers before you deploy them anywhere!** You should build and use your own docker containers in production anyway.
And if you cannot or don't want to use Docker then provide the needed infrastructure the way you prefer and just point event machine to it by adjusting configuration.

### Postgres Event Store

Event Machine skeleton uses prooph's [Postgres event store](https://github.com/prooph/pdo-event-store) by default. 
It is fully set up with transaction management and event publishing after transaction commit (done by Event Machine).
 
### MongoDB Projections

Event Machine ships with a neat feature called **aggregate projection**. To explain it we should take a look at the user aggregate description from
the linked gist.

```php
$eventMachine
//...
->apply(function (Message $userWasRegistered) {
    $userState = $userWasRegistered->payload();
    return $userState;
});

$eventMachine
//...
->apply(function(array $userState, Message $usernameWasChanged) {
    $userState['username'] = $usernameWasChanged->payload()['newUsername'];
    return $userState;
});
```
What you return as `state` from an aggregate `apply` method is persisted by the `aggregate_projection` as a document.
Each time `state` changes, the corresponding projection document is updated, too. This happens fully automatic and runs
in the background. It is recommended to run each prooph event store projection as a separate background process.
Event Machine skeleton gives you an idea how such a projection can look like. Checkout the `docker-compose.yml` and 
`bin/aggregate_projection.php` for details. 

If you've followed the quick start you can connect to the MongoDB container (f.e. with mongoDB PHPStorm plugin and default connection settings)
and should find a database called `event_machine` with a collection `aggregate_user` and a document that has the user id as
document `_id` and all properties set. 

The projection uses the `aggregate type` (in the example it is `User`), normalizes the type and prefixes it with `aggregate_`.

The `aggregate identifier` (int the example it is `userId`) is used as document id.

The `aggregate state` is turned into the document itself. Event Machine does not force the type of the state. In the example an `array`
is used but an object would also be possible. See [the example](https://github.com/proophsoftware/event-machine/blob/master/examples/Aggregate/UserDescription.php#L60) 
shipped with Event Machine for an alternative approach (If you prefer value objects use them!).

**Make sure that the MongoDB driver is able to [persist](http://php.net/manual/de/mongodb.persistence.php) the state**

*Note: You can of course deactivate the projection, if you don't like it or need different behaviour. Remember batteries are included. 
And of course you can add your own projections and use another database than MongoDB as storage system.*

### RabbitMQ messaging and web sockets

tbd
