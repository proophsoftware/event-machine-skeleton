<?php
declare(strict_types = 1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Http\ServerMiddleware\DelegateInterface as Delegate;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = include 'config/container.php';

//Note: this is important and needs to happen before further dependencies are pulled
$container->get('eventMachine')->bootstrap();

$app = new \Zend\Stratigility\MiddlewarePipe();

$app->pipe($container->get('httpErrorHandler'));

$app->pipe(new \Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware());

$app->pipe(new \App\Http\OriginalUriMiddleware());

$app->pipe('/api', function (Request $req, Delegate $delegate) use($container) {
    /** @var FastRoute\Dispatcher $router */
    $router = require 'config/api_router.php';

    $route = $router->dispatch($req->getMethod(), $req->getUri()->getPath());

    if ($route[0] === FastRoute\Dispatcher::NOT_FOUND) {
        return $delegate->process($req);
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

    /** @var \Interop\Http\ServerMiddleware\MiddlewareInterface $httpHandler */
    $httpHandler = $container->get($route[1]);

    return $httpHandler->process($req, $delegate);
});

$app->pipe('/', function (Request $request, Delegate $delegate): Response {
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

