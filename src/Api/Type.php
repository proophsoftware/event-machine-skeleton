<?php

declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;
use Prooph\EventMachine\JsonSchema\Type\ObjectType;

class Type implements EventMachineDescription
{
    /**
     * Define constants for query return types. Do not mix up return types with App\Api\Aggregate types.
     * Both can have the same name and probably represent the same data but you can and should keep them separated.
     * Aggregate types are for your write model and query return types are for your read model.
     *
     * @example
     *
     * const USER = 'User';
     *
     * You can use private static methods to define the type schemas and then register them in event machine together with the type name
     * private static function user(): array
     * {
     *      return JsonSchema::object([
     *          Payload::USER_ID => Schema::userId(),
     *          Payload::USERNAME => Schema::username()
     *      ])
     * }
     *
     * Queries should only use type references as return types (at least when return type is an object), this enables a
     * seamless GraphQL integration and gives you a nice GraphQL schema documentation out-of-the-box
     * @see \App\Api\Query for more about query return types
     */


    const HEALTH_CHECK = 'HealthCheck';

    private static function healthCheck(): ObjectType
    {
        return JsonSchema::object([
            'system' => JsonSchema::boolean()
        ]);
    }

    /**
     * @param EventMachine $eventMachine
     */
    public static function describe(EventMachine $eventMachine): void
    {
        //Register the HealthCheck type returned by @see \App\Api\Query::HEALTH_CHECK
        $eventMachine->registerType(self::HEALTH_CHECK, self::healthCheck());

        /**
         * Register all types returned by queries
         * @see \App\Api\Query for more details about return types
         *
         * @example
         *
         * $eventMachine->registerType(self::USER, self::user());
         *
         * Note: If you want to use objects as command or query arguments, for example a command like this:
         * $eventMachine->registerCommand(Command::REGISTER_USER, JsonSchema::object([
         *      Payload::USER => JsonSchema::object([ ... ])
         * ]));
         *
         * You should register an InputType instead. This would look something like that:
         *
         * const USER_INPUT = 'UserInput';
         *
         * $eventMachine->registerInputType(self::USER_INPUT, self::user()); //<-- Note we can reuse the user schema definition
         *                                                                   //as long as it does NOT contain other type references to RETURN TYPES!
         *                                                                   //But we give this type another name (Input suffix) so that GraphQL can
         *                                                                   //distinguish between a User type used as return type
         *                                                                   //and a UserInput type used as command or query input argument
         *
         * $eventMachine->registerCommand(Command::REGISTER_USER, JsonSchema::object([
         *      Payload::USER => JsonSchema::typeRef(self::USER_INPUT) //<-- even better if you move that to a \App\Api\Schema::userInput() method
         *                                                             // but we show it this way here to ease the example
         * ]));
         */
    }
}
