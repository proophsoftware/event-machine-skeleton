<?php

declare(strict_types=1);

namespace App\Infrastructure\Building;


use App\Api\Payload;
use App\Model\Building;
use Prooph\EventMachine\Messaging\Message;

final class BuildingHandler
{
    public static function add(Message $addBuilding): \Generator
    {
        yield from Building::add(
            Building\BuildingId::fromString($addBuilding->get(Payload::BUILDING_ID)),
            $addBuilding->get(Payload::NAME)
        );
    }

    public static function whenBuildingAdd(Message $buildingAdded): Building
    {
        return Building::whenAdded($buildingAdded);
    }

    public static function checkInUser(Building $building, Message $checkInUser): \Generator
    {
        yield from $building->checkInUser($checkInUser->get(Payload::NAME));
    }

    public static function apply(Building $building, Message $event): Building
    {
        $building->apply($event);
        return $building;
    }
}
