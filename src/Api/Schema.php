<?php

declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\JsonSchema\JsonSchema;
use Ramsey\Uuid\Uuid;

class Schema
{
    /**
     * This class acts as a central place for all schema related information.
     * In event machine you use JSON Schema for message validation and also for GraphQL types (more about that in the docs)
     *
     * It is a good idea to use static methods for schema definitions so that you don't need to repeat them when
     * defining message payloads or query return types.
     *
     * Define constants for query return types. Do not mix up return types with App\Api\Aggregate types.
     * Both can have the same name and probably represent the same data but you can and should keep them separated.
     * Aggregate types are for your write model and query return types are for your read model.
     *
     * @example
     *
     * const TYPE_USER = 'User';
     *
     * public static function userType(): array
     * {
     *      return JsonSchema::object([
     *          Payload::USER_ID => Schema::userId(),
     *          Payload::USERNAME => Schema::username()
     *      ])
     * }
     *
     * //Wrap basic JSON schema types with validation rules by domain specific types that you use in other schema definitions
     * public static function userId(): array
     * {
     *      return Schema::uuid();
     * }
     *
     * public static function username(): array
     * {
     *      return JsonSchema::string(['minLength' => 1])
     * }
     */

    /**
     * Common schema definitions that are useful in nearly any application.
     * Add more or remove unneeded depending on project needs.
     */
    const TYPE_HEALTH_CHECK = 'HealthCheck';

    public static function queryPagination(): array
    {
        return [
            Payload::SKIP => JsonSchema::nullOr(JsonSchema::integer(['minimum' => 0])),
            Payload::LIMIT => JsonSchema::nullOr(JsonSchema::integer(['minimum' => 1])),
        ];
    }

    public static function uuid(): array
    {
        return JsonSchema::string(['pattern' => Uuid::VALID_PATTERN]);
    }

    public static function iso8601DateTime(): array
    {
        return JsonSchema::string([
            'pattern' => '^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(\.\d+)?(([+-]\d\d:\d\d)|Z)?$'
        ]);
    }

    public static function healthCheckType(): array
    {
        return JsonSchema::object([
            'system' => JsonSchema::boolean()
        ]);
    }
}
