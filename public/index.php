<?php
declare(strict_types = 1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = include 'config/container.php';

//Note: this is important and needs to happen before further dependencies are pulled
$env = getenv('PROOPH_ENV')?: 'prod';
$devMode = $env === \Prooph\EventMachine\EventMachine::ENV_DEV;

$app = new \Zend\Stratigility\MiddlewarePipe();

$app->setResponsePrototype(new \Zend\Diactoros\Response());

$app->pipe($container->get(\Zend\Stratigility\Middleware\ErrorHandler::class));

$app->pipe(new \Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware());

$app->pipe(new \App\Http\OriginalUriMiddleware());

$app->pipe('/api', function (Request $req) use($container, $env, $devMode) {
    /** @var FastRoute\Dispatcher $router */
    $router = require 'config/api_router.php';

    $route = $router->dispatch($req->getMethod(), $req->getUri()->getPath());

    if ($route[0] === FastRoute\Dispatcher::NOT_FOUND) {
        return new \Zend\Diactoros\Response\EmptyResponse(404);
    }

    if ($route[0] === FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
        return new \Zend\Diactoros\Response\EmptyResponse(405);
    }

    foreach ($route[2] as $name => $value) {
        $req = $req->withAttribute($name, $value);
    }

    if(!$container->has($route[1])) {
        throw new \RuntimeException("Http handler not found. Got " . $route[1]);
    }

    $container->get(\Prooph\EventMachine\EventMachine::class)->bootstrap($env, $devMode);

    /** @var \Interop\Http\Server\RequestHandlerInterface $httpHandler */
    $httpHandler = $container->get($route[1]);

    return $httpHandler->handle($req);
});

$app->pipe('/', function (Request $request): Response {
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

