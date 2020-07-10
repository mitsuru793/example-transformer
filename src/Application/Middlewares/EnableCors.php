<?php
declare(strict_types=1);

namespace Php\Application\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class EnableCors implements MiddlewareInterface
{
    /** @var string[] */
    private array $allowedList;

    public function __construct(array $allowedList)
    {
        $this->allowedList = $allowedList;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestOrigin = $request->getServerParams()['HTTP_ORIGIN'] ?? null;
        $response = $handler->handle($request);

        if (!in_array($requestOrigin, $this->allowedList)) {
            return $response;
        }

        $response = $response->withAddedHeader('Access-Control-Allow-Origin', $requestOrigin);
        $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
        // $response = $response->withAddedHeader('Access-Control-Max-Age', 5); // sec
        return $response;
    }
}
