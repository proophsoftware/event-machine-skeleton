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

interface Command
{
    /**
     * Define command names using constants
     *
     * Note: If you use the GraphQL integration then make sure that your command names can be used as type names
     * in GraphQL. Dots for example do not work: MyContext.RegisterUser
     * Either use MyContext_RegisterUser or just MyContextRegisterUser. Event machine is best suited for single context
     * services anyway, so in most cases you don't need to set a context in front of your commands because the context
     * is defined by the service boundaries itself.
     *
     * @example
     *
     * const REGISTER_USER = 'RegisterUser';
     */
}
