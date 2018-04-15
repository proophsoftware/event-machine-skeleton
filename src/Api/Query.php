<?php

declare(strict_types=1);

namespace App\Api;

use App\Infrastructure\Building\BuildingResolver;
use App\Infrastructure\System\HealthCheckResolver;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;

class Query implements EventMachineDescription
{
    /**
     * Define query names using constants
     *
     * For a clean and simple API it is recommended to just use the name of the "thing"
     * you want to return as query name, see example for user queries:
     *
     * @example
     *
     * const USER = 'User';
     * const USERS = 'Users';
     * const FRIENDS = 'Friends';
     */

    const BUILDING = Type::BUILDING;
    const BUILDINGS = 'Buildings';

    /**
     * Default Query, used to perform health checks using the messagebox endpoint
     */
    const HEALTH_CHECK = 'HealthCheck';

    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->registerQuery(self::BUILDING, JsonSchema::object([
            Payload::BUILDING_ID => Schema::buildingId(),
        ]))
            ->resolveWith(BuildingResolver::class)
            ->setReturnType(Schema::building());

        $eventMachine->registerQuery(
            self::BUILDINGS,
            JsonSchema::object([], array_merge([
                Payload::NAME => JsonSchema::nullOr(Schema::buildingName())
            ],
                Schema::queryPagination()
            )))
            ->resolveWith(BuildingResolver::class)
            ->setReturnType(JsonSchema::array(Schema::building()));

        //Default query: can be used to check if service is up and running
        $eventMachine->registerQuery(self::HEALTH_CHECK) //<-- Payload schema is optional for queries
            ->resolveWith(HealthCheckResolver::class) //<-- Service id (usually FQCN) to get resolver from DI container
            ->setReturnType(Schema::healthCheck()); //<-- Type returned by resolver

        /**
         * Register queries and if they have arguments (like filters, skip, limit, orderBy arguments)
         * you define the schema of that arguments as query payload
         *
         * You also tell event machine which resolver should be used to resolve the query.
         * The resolver is requested from the PSR-11 DI container used by event machine.
         *
         * Each query also has a return type, which can be a JsonSchema::TYPE_ARRAY or one of the scalar JsonSchema types.
         * If the query returns an object (for example user data), this object should be registered in EventMachine as a Type
         * @see \App\Api\Type for details
         * @see \App\Api\Schema for best practise of how to reuse return type schemas
         *
         * @example
         *
         * //Register User query with Payload::USER_ID as required argument, Schema::userId() is reused here, so that only valid
         * //user ids are passed to the resolver
         * $eventMachine->registerQuery(self::User, JsonSchema::object([Payload::USER_ID => Schema::userId()]))
         *      ->resolveWith(UserResolver::class)
         *      ->setReturnType(Schema::user()); //<-- Pass type reference as return type, @see \App\Api\Schema::user() (in the comment) for details
         *
         * //Register a second query to load many Users, this query takes an optional Payload::ACTIVE argument
         * $eventMachine->registerQuery(self::Users, JsonSchema::object([], [
         *      Payload::ACTIVE => JsonSchema::nullOr(JsonSchema::boolean()) 
         * ]))
         *  ->resolveWith(UsersResolver::class)
         *  ->setReturnType(JsonSchema::array(Schema::user())); //<-- Return type is an array of Schema::user() (type reference to Type::USER)
         */
    }
}
