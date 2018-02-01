<?php

declare(strict_types=1);

namespace App\Api;

use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class Projection implements EventMachineDescription
{
    /**
     * You can register aggregate and custom projections in event machine
     *
     * For custom projection you should define a unique projection name using a constant
     *
     * const USER_FRIENDS = 'UserFriends';
     */

    /**
     * @param EventMachine $eventMachine
     */
    public static function describe(EventMachine $eventMachine): void
    {
        /**
         * Register an aggregate projection using the aggregate type as projection name
         *
         * $eventMachine->watch(\Prooph\EventMachine\Persistence\Stream::ofWriteModel())
         *  ->withAggregateProjection(Aggregate::USER);
         *
         * Note: \Prooph\EventMachine\Projecting\AggregateProjector::aggregateCollectionName(string $aggregateType)
         * will be used to generate a collection name for the aggregate data to be stored (as documents).
         * This means that a query resolver (@see \App\Api\Query) should use the same method to generate the collection name
         *
         * Register a custom projection
         *
         * $eventMachine->watch(\Prooph\EventMachine\Persistence\Stream::ofWriteModel())
         *  ->with(self::USER_FRIENDS, UserFriendsProjector::class) //<-- Custom projection name and Projector service id (for DI container)
         *                                                          //Projector should implement Prooph\EventMachine\Projecting\Projector
         *  ->filterEvents([Event::USER_ADDED, EVENT::FRIEND_LINKED]); //Projector is only interested in listed events
         */
    }
}
