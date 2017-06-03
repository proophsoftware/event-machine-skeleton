<?php
declare(strict_types = 1);

namespace App\Http;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

final class OriginalUriMiddleware implements MiddlewareInterface
{

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $request = $request->withAttribute('original_uri', $request->getUri());

        return $delegate->process($request);
    }
}
