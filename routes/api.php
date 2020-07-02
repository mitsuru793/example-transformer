<?php
declare(strict_types=1);

use Php\Application\Actions\Debug\RenderStringAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return function (\League\Route\Router $router) {
    $router->options('/{routes:.*}', function (ServerRequestInterface $request) : ResponseInterface {
        $response = new \Zend\Diactoros\Response();
        $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
        $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'Content-Type');
        return $response;
    });

    $router->get('/', new RenderStringAction('welcome!'));

    $router->get('/string', new RenderStringAction('get string'));
    $router->post('/string', new RenderStringAction('post string'));
    $router->put('/string', new RenderStringAction('put string'));
    $router->delete('/string', new RenderStringAction('delete string'));
};
