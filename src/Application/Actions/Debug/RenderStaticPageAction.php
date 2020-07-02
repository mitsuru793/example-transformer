<?php
declare(strict_types=1);

namespace Php\Application\Actions\Debug;

use League\Plates\Engine;
use Php\Application\Middlewares\LoginAuth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RenderStaticPageAction
{
    private Engine $templates;

    private string $file;

    public function __construct(Engine $templates, string $file)
    {
        $this->templates = $templates;
        $this->file = $file;
    }

    public function __invoke(Request $request, array $args): Response
    {
        $loginUser = $request->getAttribute(LoginAuth::ATTRIBUTE_KEY);

        $response = new \Zend\Diactoros\Response();
        return $this->renderView($response, $this->file, compact('loginUser'));
    }

    private function renderView(Response $response, string $template, array $data = []): Response
    {
        $output = $this->templates->render($template, $data);
        $response->getBody()->write($output);
        return $response;
    }
}