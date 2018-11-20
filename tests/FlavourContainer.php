<?php

declare(strict_types=1);

namespace AppTest;

use Prooph\EventMachine\Container\ServiceNotFound;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\Runtime\Flavour;
use Psr\Container\ContainerInterface;

final class FlavourContainer implements ContainerInterface
{
    /**
     * @var Flavour
     */
    private $flavour;

    public function __construct(Flavour $flavour)
    {
        $this->flavour = $flavour;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if($id === EventMachine::SERVICE_ID_FLAVOUR) {
            return $this->flavour;
        }

        throw ServiceNotFound::withServiceId($id);
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return $id === EventMachine::SERVICE_ID_FLAVOUR;
    }
}
