<?php

declare(strict_types=1);

namespace App\Infrastructure\ServiceBus;

use Prooph\EventMachine\Messaging\Message;

/**
 * Marker Interface UiExchange
 *
 * @package App\Infrastructure\ServiceBus
 */
interface UiExchange
{
    public function __invoke(Message $event): void;
}
