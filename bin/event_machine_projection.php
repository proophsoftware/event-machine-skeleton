<?php
declare(strict_types = 1);

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = require 'config/container.php';

/** @var \Prooph\EventMachine\EventMachine $eventMachine */
$eventMachine = $container->get(\Prooph\EventMachine\EventMachine::class);

$eventMachine->bootstrap(getenv('PROOPH_ENV')?: 'prod', true);

$iterations = 0;

while (true) {
    $devMode = $eventMachine->env() === \Prooph\EventMachine\EventMachine::ENV_DEV;

    $eventMachine->runProjections(!$devMode);

    $iterations++;

    if($iterations > 100) {
        //force reload in dev mode by exiting with error so docker restarts the container
        exit(1);
    }

    usleep(100);
}
