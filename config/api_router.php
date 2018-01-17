<?php
declare(strict_types = 1);

return \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
    $r->addRoute(
        ['GET', 'POST'],
        '/graphql',
        \Prooph\EventMachine\GraphQL\Server::class
    );

    $r->addRoute(
        ['POST'],
        '/messagebox',
        \Prooph\EventMachine\Http\MessageBox::class
    );

    $r->addRoute(
        ['POST'],
        '/messagebox/{message_name:[A-Za-z0-9_.-\/]+}',
        \Prooph\EventMachine\Http\MessageBox::class
    );

    $r->addRoute(
        ['GET'],
        '/messagebox-schema',
        \App\Http\MessageSchemaMiddleware::class
    );
});