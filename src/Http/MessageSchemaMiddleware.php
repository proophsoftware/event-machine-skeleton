<?php
declare(strict_types = 1);

namespace App\Http;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Prooph\EventMachine\EventMachine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\TextResponse;

final class MessageSchemaMiddleware implements MiddlewareInterface
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
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
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
