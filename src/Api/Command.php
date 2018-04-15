<?php

declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;

class Command implements EventMachineDescription
{
    /**
     * Define command names using constants
     *
     * Note: Event machine is best suited for single context services.
     * So in most cases you don't need to set a context in front of your commands because the context
     * is defined by the service boundaries itself, but the example includes a context to be complete.
     *
     * @example
     *
     * const COMMAND_CONTEXT = 'MyContext.';
     * const REGISTER_USER = self::COMMAND_CONTEXT . 'RegisterUser';
     */
    const ADD_BUILDING = 'AddBuilding';
    const CHECK_IN_USER = 'CheckInUser';

    /**
     * @param EventMachine $eventMachine
     */
    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->registerCommand(self::ADD_BUILDING, JsonSchema::object([
            Payload::BUILDING_ID => Schema::buildingId(),
            Payload::NAME => Schema::buildingName(),
        ]));

        $eventMachine->registerCommand(self::CHECK_IN_USER, JsonSchema::object([
            Payload::BUILDING_ID => Schema::buildingId(),
            Payload::NAME => Schema::username(),
        ]));

        /**
         * Describe commands of the service and corresponding payload schema (used for input validation)
         *
         * @example
         *
         * $eventMachine->registerCommand(
         *      self::REGISTER_USER,  //<-- Name of the  command defined as constant above
         *      JsonSchema::object([
         *          Payload::USER_ID => Schema::userId(), //<-- We only work with constants and domain specific reusable schemas
         *          Payload::USERNAME => Schema::username(), //<-- See App\Api\Payload for property constants ...
         *          Payload::EMAIL => Schema::email(), //<-- ... and App\Api\Schema for schema definitions
         *      ])
         * );
         */
    }
}
