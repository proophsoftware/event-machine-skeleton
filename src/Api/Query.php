<?php

declare(strict_types=1);

namespace App\Api;

use App\Infrastructure\System\HealthCheckResolver;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class Query implements EventMachineDescription
{
    /**
     * Define query names using constants
     *
     * Note: If you use the GraphQL integration then make sure that your query names can be used as type names in GraphQL.
     * Dots for example do not work: MyContext.Something
     * Either use MyContext_Something or just MyContextSomething. Event machine is best suited for single context
     * services anyway, so in most cases you don't need to set a context in front of your queries because the context
     * is defined by the service boundaries itself.
     *
     * For a clean and simple API (when using GraphQL integration) it is recommended to just use the name of the "thing"
     * you want to return as query name, see example for user queries:
     *
     * @example
     *
     * const USER = 'User';
     * const USERS = 'Users';
     * const FRIENDS = 'Friends';
     */

    /**
     * Default Query, used to perform health checks using messagebox or GraphQL endpoint
     */
    const HEALTH_CHECK = 'HealthCheck';

    public static function describe(EventMachine $eventMachine): void
    {
        //Default query: can be used to check if service is up and running
        $eventMachine->registerQuery(self::HEALTH_CHECK) //<-- Payload schema is optional for queries
            ->resolveWith(HealthCheckResolver::class) //<-- Service id (usually FQCN) to get resolver from DI container
            ->setReturnType(Schema::healthCheck()); //<-- Type returned by resolver, this is converted to a GraphQL type

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
         *      Payload::ACTIVE => JsonSchema::nullOr(JsonSchema::boolean()) //<-- Note: an optional argument should also be nullable to work with GraphQL
         * ]))
         *  ->resolveWith(UsersResolver::class)
         *  ->setReturnType(JsonSchema::array(Schema::user())); //<-- Return type is an array of Schema::user() (type reference to Type::USER)
         */
    }
}
