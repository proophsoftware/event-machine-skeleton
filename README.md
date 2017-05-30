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
8. The table starting with _ followed by a sha1 hash is your "event_stream" table. You should see a new `Àpp.UserWasRegistered` event in it.
9. Test also the "App.ChangeUsername" request
10. Exercise: Implement the "App.ChangeEmail" use case

## What's insight?

The skeleton ships with a default set up so that you can start without messing around with configuration and such.
Don't worry if the default set up is not exactly what you want to use in production. The skeleton can be adopted were 
needed. 

tbd...