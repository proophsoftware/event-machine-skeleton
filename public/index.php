<?php
declare(strict_types = 1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Http\ServerMiddleware\DelegateInterface as Delegate;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = include 'config/container.php';

$app = new \Zend\Stratigility\MiddlewarePipe();

$app->pipe($container->get('httpErrorHandler'));

$app->pipe('/', function (Request $request, Delegate $delegate): Response {
    throw new \RuntimeException("test error");
    //@TODO add homepage with infos about event-machine and the skeleton
    return new \Zend\Diactoros\Response\TextResponse("It works");
});

$server = \Zend\Diactoros\Server::createServer(
    $app,
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$server->listen(new \Zend\Stratigility\NoopFinalHandler());

