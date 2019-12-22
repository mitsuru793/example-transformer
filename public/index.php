<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

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

$response = $router->dispatch($request);

// send the response to the browser
(new Zend\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
