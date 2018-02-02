<?php

declare(strict_types=1);

namespace App\Model\Building;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class BuildingId
{
    private $buildingId;

    public static function fromString(string $buildingId): self
    {
        return new self(Uuid::fromString($buildingId));
    }

    private function __construct(UuidInterface $buildingId)
    {
        $this->buildingId = $buildingId;
    }

    public function toString(): string
    {
        return $this->buildingId->toString();
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->buildingId->equals($other);
    }

    public function __toString(): string
    {
        return $this->buildingId->toString();
    }
}
