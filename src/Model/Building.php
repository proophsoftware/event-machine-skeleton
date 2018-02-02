<?php

declare(strict_types=1);

namespace App\Model;

use App\Api\Event;
use App\Model\Building\BuildingData;
use Prooph\EventMachine\Messaging\Message;

final class Building
{
    public static function add(Message $addBuilding): \Generator
    {
        yield [Event::BUILDING_ADDED, $addBuilding->payload()];
    }

    public static function whenBuildingAdd(Message $buildingAdded): BuildingData
    {
        return BuildingData::fromArray($buildingAdded->payload());
    }
}
