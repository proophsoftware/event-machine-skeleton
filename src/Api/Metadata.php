<?php
/**
 * This file is part of the proophsoftware/event-machine-skeleton.
 * (c) 2018 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Api;

interface Metadata
{
    /**
     * If you want to access or set metadata of a message you should define metadata keys as constants.
     * This makes it easy to find all places in the source code that access and work with that metadata keys.
     *
     * const CAUSATION_MESSAGE_ID = 'causationMessageId';
     */
}
