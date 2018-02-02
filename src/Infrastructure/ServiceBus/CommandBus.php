<?php
/**
 * This file is part of the prooph/mongodb-event-store.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 * (c) %year% Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Infrastructure\ServiceBus;

use Prooph\EventMachine\Messaging\GenericJsonSchemaMessage;
use Prooph\ServiceBus\CommandBus as ProophCommandBus;

class CommandBus extends ProophCommandBus
{
    protected function getMessageName($message): string
    {
        if($message instanceof GenericJsonSchemaMessage) {
            return $message->messageName();
        }

        return parent::getMessageName($message);
    }
}
