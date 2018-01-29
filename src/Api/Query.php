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

interface Query
{
    /**
     * Define query names using constants
     *
     * Note: If you use the GraphQL integration then make sure that your query names can be used as type names
     * in GraphQL. Dots for example do not work: MyContext.Something
     * Either use MyContext_Something or just MyContextSomething. Event machine is best suited for single context
     * services anyway, so in most cases you don't need to set a context in front of your queries because the context
     * is defined by the service boundaries itself.
     *
     * For a clean and simple API (when using GraphQL integration) it is recommended to just use the name of the "thing"
     * you want to return as query name, see example for user queries:
     *
     * @example
     *
     * const USER = 'User';
     * const USERS = 'Users';
     * const FRIENDS = 'Friends';
     */
}
