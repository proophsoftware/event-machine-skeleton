<?php
declare(strict_types = 1);

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = require 'config/container.php';

/** @var \Prooph\EventMachine\EventMachine $eventMachine */
$eventMachine = $container->get('eventMachine');

$eventMachine->bootstrap();

/** @var \Prooph\EventStore\Projection\ProjectionManager $projectionManager */
$projectionManager = $container->get('projectionManager');

$projection = $projectionManager->createReadModelProjection(
    'aggregate_projection',
    $container->get('aggregateReadModel'),
    [
        \Prooph\EventStore\Projection\ReadModelProjector::OPTION_PERSIST_BLOCK_SIZE => 1
    ]
);

$projection->fromStream('event_stream')
    ->whenAny(function ($state, \Prooph\Common\Messaging\Message $event) {
        /** @var \App\Infrastructure\MongoDb\AggregateReadModel $readModel */
        $readModel = $this->readModel();
        $readModel->stack('upsert', $event);
    })
    ->run();


