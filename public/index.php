<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => getenv('DB_HOST'),
    'database' => getenv('DB_DATABASE'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container = new League\Container\Container;
$container->delegate(new League\Container\ReflectionContainer);

$add = require_once __DIR__ . '/../config/dependencies.php';
$add($container);

$add = require_once __DIR__ . '/../config/repositories.php';
$add($container);

$app = new \Php\Application\App($container);

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);
$response = $app->process($request);
(new \Zend\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
