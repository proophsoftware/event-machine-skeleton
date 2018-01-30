<?php

declare(strict_types=1);

namespace App\Api;


interface Event
{
    /**
     * Define event names using constants
     *
     * Note: It is NOT recommended to use a context in command and query names, see note in App\Api\Command.
     * But using a context in your event names is a good idea, because events tell other services in a system what
     * happened in your service. So these foreign services need to know the origin of the event.
     * A very simple way is to put the context in the event name separated by a dot. When using a message broker like
     * RabbitMQ you can use such a naming convention to route events of a certain context to a dedicated queue.
     *
     * @example
     *
     * const EVENT_CONTEXT = 'MyContext.';
     * const USER_REGISTERED = self::EVENT_CONTEXT.'UserRegistered';
     */
}
