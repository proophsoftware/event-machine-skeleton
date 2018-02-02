<?php

declare(strict_types=1);

namespace App\Http;

use Exception;
use GraphQL\Error\FormattedError;
use Prooph\ServiceBus\Exception\MessageDispatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zend\Stratigility\Utils;

final class ErrorResponseGenerator
{
    /**
     * @var bool
     */
    private $developmentMode;

    public function __construct($isDevelopmentMode = false)
    {
        $this->developmentMode = $isDevelopmentMode;
    }

    /**
     * Create/update the response representing the error.
     *
     * @param Throwable|Exception $e
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke($e, ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = $response->withStatus(Utils::getStatusCode($e, $response));

        $response = $response->withAddedHeader('content-type', 'application/json');

        $json = json_encode([
            'errors' => [
                FormattedError::createFromException($e, $this->developmentMode)
            ],
            'data' => []
        ]);

        $body = $response->getBody();

        $body->write($json);

        return $response;
    }
}
