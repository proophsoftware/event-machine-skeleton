<?php

declare(strict_types=1);

namespace App\Model\Building;

use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

final class BuildingData implements ImmutableRecord
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
}
