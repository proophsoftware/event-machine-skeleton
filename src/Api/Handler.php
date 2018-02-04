<?php

declare(strict_types=1);

namespace App\Api;

use App\Infrastructure\Building\BuildingHandler;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class Handler implements EventMachineDescription
{
    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->process(Command::ADD_BUILDING)
            ->withNew(Aggregate::BUILDING)
            ->identifiedBy(Payload::BUILDING_ID)
            ->handle([BuildingHandler::class, 'add'])
            ->recordThat(Event::BUILDING_ADDED)
            ->apply([BuildingHandler::class, 'whenBuildingAdd']);

        $eventMachine->process(Command::CHECK_IN_USER)
            ->withExisting(Aggregate::BUILDING)
            ->handle([BuildingHandler::class, 'checkInUser'])
            ->recordThat(Event::USER_CHECKED_IN)
            ->apply([BuildingHandler::class, 'apply'])
            ->orRecordThat(Event::DOUBLE_CHECK_IN_DETECTED)
            ->apply([BuildingHandler::class, 'apply']);

        /**
         * Forward commands to a static command handler that can pass command args to the aggregate
         *
         * @example
         *
         * $eventMachine->process(Command::REGISTER_USER) <-- Command name of the command that is expected by the handler function
         *      ->withNew(Aggregate::USER) //<-- aggregate type, also tell event machine that a new Aggregate is created
         *      ->identifiedBy(Payload::USER_ID) //<-- Payload property (of all user related commands) that identify the addressed User
         *      ->handle([UserHandler::class, 'register']) //<-- Handler functions are stateless and have static callable methods that can be linked to using PHP's callable array syntax
         *      ->recordThat(Event::USER_REGISTERED) //<-- Event name of the event yielded by the Aggregate's handle method
         *      ->apply([UserHandler::class, 'whenUserRegistered']) //<-- Handler method (again static) that is called when event is recorded
         *      ->orRecordThat(Event::DOUBLE_REGISTRATION_DETECTED) //Alternative event that can be yielded by the Aggregate's handle method
         *      ->apply([User::class, 'whenDoubleRegistrationDetected']); //Again the method that should be called in case above event is recorded
         *
         * $eventMachine->process(Command::CHANGE_USERNAME) //<-- UserHandler::changeUsername() expects a Command::CHANGE_USERNAME command
         *      ->withExisting(Aggregate::USER) //<-- Aggregate should already exist, Event Machine uses Payload::USER_ID to load User from event store
         *      ->handle([UserHandler::class, 'changeUsername'])
         *      ->recordThat(Event::USERNAME_CHANGED)
         *      ->apply([UserHandler::class, 'whenUsernameChanged']);
         */
    }
}
