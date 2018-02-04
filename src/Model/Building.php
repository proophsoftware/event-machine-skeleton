<?php

declare(strict_types=1);

namespace App\Model;

use App\Api\Event;
use App\Api\Payload;
use App\Model\Building\BuildingId;
use Prooph\EventMachine\Messaging\Message;

final class Building implements \JsonSerializable
{
    /**
     * @var BuildingId
     */
    private $buildingId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $users = [];

    public static function add(BuildingId $buildingId, string $name): \Generator
    {
        yield [Event::BUILDING_ADDED, [
            Payload::BUILDING_ID => $buildingId->toString(),
            Payload::NAME => $name,
        ]];
    }

    public static function whenAdded(Message $buildingAdded): self
    {
        return new self(
            BuildingId::fromString($buildingAdded->get(Payload::BUILDING_ID)),
            $buildingAdded->get(Payload::NAME)
        );
    }

    private function __construct(BuildingId $buildingId, string $name)
    {
        $this->buildingId = $buildingId;
        $this->name = $name;
    }

    public function checkInUser(string $username): \Generator
    {
        if($this->isCheckedIn($username)) {
            yield [Event::DOUBLE_CHECK_IN_DETECTED, [
                Payload::BUILDING_ID => $this->buildingId->toString(),
                Payload::NAME => $username,
            ]];
            return;
        }

        yield [Event::USER_CHECKED_IN, [
            Payload::BUILDING_ID => $this->buildingId->toString(),
            Payload::NAME => $username,
        ]];
    }

    public function apply(Message $event): void
    {
        switch ($event->messageName()) {
            case Event::USER_CHECKED_IN:
                $this->users[$event->get(Payload::NAME)] = null;
                break;
            default:
                //no state change required
        }
    }

    private function isCheckedIn(string $username): bool
    {
        return array_key_exists($username, $this->users);
    }

    public function jsonSerialize()
    {
        return [
            Payload::BUILDING_ID => $this->buildingId->toString(),
            Payload::NAME => $this->name,
            Payload::USERS => array_keys($this->users),
        ];
    }
}
