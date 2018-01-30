<?php

declare(strict_types = 1);

namespace App\Http;

use Interop\Http\Server\RequestHandlerInterface;
use Prooph\EventMachine\EventMachine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\JsonResponse;

final class MessageSchemaMiddleware implements RequestHandlerInterface
{
    /**
     * @var EventMachine
     */
    private $eventMachine;

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }


    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request)
    {
        /** @var UriInterface $uri */
        $uri = $request->getAttribute('original_uri', $request->getUri());

        $messageBoxUri = $uri->withPath(str_replace('-schema', '', $uri->getPath()));

        return new JsonResponse(array_merge(
            ['messageBox' => (string)$messageBoxUri],
            $this->eventMachine->messageSchemas()
        ));
    }
}
