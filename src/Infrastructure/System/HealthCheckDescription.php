<?php

declare(strict_types=1);

namespace App\Infrastructure\System;

use App\Api\Query;
use App\Api\Schema;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;

final class HealthCheckDescription implements EventMachineDescription
{
    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->registerType(Schema::TYPE_HEALTH_CHECK, Schema::healthCheckType());

        $eventMachine->registerQuery(Query::HEALTH_CHECK)
            ->resolveWith(HealthCheckResolver::class)
            ->returnType(JsonSchema::typeRef(Schema::TYPE_HEALTH_CHECK));
    }
}
