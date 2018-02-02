<?php

declare(strict_types=1);

namespace App\Model\Building;

use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;
use Prooph\EventMachine\JsonSchema\JsonSchema;
use Prooph\EventMachine\JsonSchema\Type;

final class State implements ImmutableRecord
{
    use ImmutableRecordLogic;

    /**
     * @var BuildingId
     */
    private $buildingId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $users = [];

    public static function __schema(): Type
    {
        //Type hint "users" property as an array that contains strings
        //This is needed because we cannot determine array item type from method's return type only
        return self::generateSchemaFromPropTypeMap([
            'users' => JsonSchema::string()
        ]);
    }

    /**
     * @return BuildingId
     */
    public function buildingId(): BuildingId
    {
        return $this->buildingId;
    }

    /**
     * @return mixed
     */
    public function name(): string
    {
        return $this->name;
    }

    public function users(): array
    {
        return $this->users;
    }

    public function checkIn(string $username): self
    {
        //State should be immutable, so only modify a copy
        $cp = clone $this;
        $cp->users[] = $username;
        return $cp;
    }

    public function isCheckedIn(string $username): bool
    {
        return in_array($username, $this->users);
    }
}
