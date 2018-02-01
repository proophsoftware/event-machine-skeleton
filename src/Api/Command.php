<?php

declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class Command implements EventMachineDescription
{
    /**
     * Define command names using constants
     *
     * Note: If you use the GraphQL integration then make sure that your command names can be used as type names
     * in GraphQL. Dots for example do not work: MyContext.RegisterUser
     * Either use MyContext_RegisterUser or just MyContextRegisterUser. Event machine is best suited for single context
     * services anyway, so in most cases you don't need to set a context in front of your commands because the context
     * is defined by the service boundaries itself.
     *
     * @example
     *
     * const REGISTER_USER = 'RegisterUser';
     */


    /**
     * @param EventMachine $eventMachine
     */
    public static function describe(EventMachine $eventMachine): void
    {
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
