<?php
declare(strict_types=1);

namespace Php\Controller;

use League\Plates\Engine;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

abstract class Controller
{
    protected ResponseInterface $response;

    /** @var Engine */
    private $templates;

    public function __construct(Engine $templates)
    {
        $this->templates = $templates;
        $this->response = new Response();
    }

    protected function view(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $output = $this->templates->render($template, $data);
        $response->getBody()->write($output);
        return $response;
    }

    protected function redirectBack(RequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $referrer = $req->getServerParams()['HTTP_REFERER'];
        return $res
            ->withStatus(303)
            ->withHeader('Location', $referrer);
    }
}
