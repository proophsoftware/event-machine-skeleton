<?php
declare(strict_types = 1);

$config = include 'config.php';

$serviceFactory = new \App\Service\ServiceFactory($config);

//@TODO use cached serviceFactoryMap for production
$container = new \Prooph\EventMachine\Container\ReflectionBasedContainer(
    $serviceFactory,
    [
        \Prooph\EventMachine\EventMachine::SERVICE_ID_EVENT_STORE => \Prooph\EventStore\EventStore::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_PROJECTION_MANAGER => \Prooph\EventStore\Projection\ProjectionManager::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_COMMAND_BUS => \App\Infrastructure\ServiceBus\CommandBus::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_EVENT_BUS => \App\Infrastructure\ServiceBus\EventBus::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_QUERY_BUS => \App\Infrastructure\ServiceBus\QueryBus::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_DOCUMENT_STORE => \Prooph\EventMachine\Persistence\DocumentStore::class,
    ]
);

$serviceFactory->setContainer($container);

return $container;