<?php
declare(strict_types=1);

namespace Php\Application\Actions\Debug;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RenderStringAction
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __invoke(Request $request): Response
    {
        $response = new \Zend\Diactoros\Response();
        $response->getBody()->write($this->text);
        return $response;
    }
}
