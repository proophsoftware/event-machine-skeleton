<?php

declare(strict_types=1);

namespace App\Api;

use App\Model\Building;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class Aggregate
{
    /**
     * Define aggregate names using constants
     *
     * @example
     *
     * const USER = 'User';
     */
    const BUILDING = 'Building';
}
