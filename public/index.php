<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';

function extendFormHttpMethod(ServerRequestInterface $request): ServerRequestInterface
{
    $body = $request->getParsedBody();
    $formMethod = $body['_method'] ?? null;
    error_log(json_encode([
        'method' => $request->getMethod(),
        'form method' => $formMethod,
    ]));
    if ($request->getMethod() !== 'POST' || is_null($formMethod)) {
        return $request;
    }

    error_log('******' . $formMethod);
    return $request->withMethod($formMethod);
}

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$container = new League\Container\Container;
$container->delegate(new League\Container\ReflectionContainer);

$strategy = (new \League\Route\Strategy\ApplicationStrategy);
$strategy->setContainer($container);

$add = require_once __DIR__ . '/../config/dependencies.php';
$add($container);

$add = require_once __DIR__ . '/../config/repositories.php';
$add($container);

$router = new League\Route\Router;
$router->setStrategy($strategy);

$add = require_once __DIR__ . '/../config/routes.php';
$add($router);

$request = extendFormHttpMethod($request);
$response = $router->dispatch($request);

// send the response to the browser
(new Zend\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
