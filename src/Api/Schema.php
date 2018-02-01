<?php

declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\JsonSchema\JsonSchema;
use Prooph\EventMachine\JsonSchema\Type\StringType;
use Prooph\EventMachine\JsonSchema\Type\TypeRef;

class Schema
{
    /**
     * This class acts as a central place for all schema related information.
     * In event machine you use JSON Schema for message validation and also for GraphQL types (more about that in the docs)
     *
     * It is a good idea to use static methods for schema definitions so that you don't need to repeat them when
     * defining message payloads or query return types.
     *
     * //Wrap basic JSON schema types with validation rules by domain specific types that you use in other schema definitions
     *
     * public static function user(): TypeRef
     * {
     *      return JsonSchema::typeRef(Type::USER);
     * }
     *
     * public static function userId(): UuidType
     * {
     *      return JsonSchema::uuid();
     * }
     *
     * public static function username(): StringType
     * {
     *      return JsonSchema::string(['minLength' => 1])
     * }
     */

    /**
     * Common schema definitions that are useful in nearly any application.
     * Add more or remove unneeded depending on project needs.
     */
    public static function healthCheck(): TypeRef
    {
        //Health check schema is a type reference to the registered Type::HEALTH_CHECK
        return JsonSchema::typeRef(Type::HEALTH_CHECK);
    }


    /**
     * Can be used as JsonSchema::object() (optional) property definition in query payloads to enable pagination
     * @return array
     */
    public static function queryPagination(): array
    {
        return [
            Payload::SKIP => JsonSchema::nullOr(JsonSchema::integer(['minimum' => 0])),
            Payload::LIMIT => JsonSchema::nullOr(JsonSchema::integer(['minimum' => 1])),
        ];
    }

    public static function iso8601DateTime(): StringType
    {
        return JsonSchema::string()->withPattern('^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(\.\d+)?(([+-]\d\d:\d\d)|Z)?$');
    }
}
