<?php

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
