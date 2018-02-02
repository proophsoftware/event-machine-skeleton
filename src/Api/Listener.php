<?php

declare(strict_types=1);

namespace App\Api;

use App\Infrastructure\ServiceBus\UiExchange;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class Listener implements EventMachineDescription
{

    public static function describe(EventMachine $eventMachine): void
    {
        //Forward double check ins to the preconfigured rabbitMQ ui-exchange to simulate that we can notify
        //security in case of a double check-in.
        $eventMachine->on(Event::DOUBLE_CHECK_IN_DETECTED, UiExchange::class);

        /**
         * Register domain event listeners
         *
         * This can be anything f.e. a mailer, a process manager or a message producer as long as
         * it is a callable that takes the domain event as a single argument and is loadable from DI container
         *
         * @example
         *
         * $eventMachine->on(Event::USER_REGISTERED, VerificationMailer::class);
         */
    }
}
