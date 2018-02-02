<?php

declare(strict_types=1);

namespace App\Model;

use App\Api\Event;
use App\Api\Payload;
use App\Model\Building\State;
use Prooph\EventMachine\Messaging\Message;

final class Building
{
    public static function add(Message $addBuilding): \Generator
    {
        yield [Event::BUILDING_ADDED, $addBuilding->payload()];
    }

    public static function whenBuildingAdd(Message $buildingAdded): State
    {
        return State::fromArray($buildingAdded->payload());
    }

    public static function checkInUser(State $state, Message $checkInUser): \Generator
    {
        if($state->isCheckedIn($checkInUser->get(Payload::NAME))) {
            yield [Event::DOUBLE_CHECK_IN_DETECTED, $checkInUser->payload()];
            return;
        }

        yield [Event::USER_CHECKED_IN, $checkInUser->payload()];
    }

    public static function whenUserCheckedIn(State $state, Message $userCheckedIn): State
    {
        return $state->checkIn($userCheckedIn->get(Payload::NAME));
    }

    public static function whenDoubleCheckInDetected(State $state, Message $doubleCheckInDetected): State
    {
        //no state chane needed at the moment, so we just return old state
        return $state;
    }
}
