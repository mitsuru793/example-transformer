<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$container = new League\Container\Container;
$container->delegate(new League\Container\ReflectionContainer);

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$add = require_once __DIR__ . '/../config/dependencies.php';
$add($container);

$application->addCommands([
    $container->get(\Php\Application\Command\InitDBCommand::class),
]);

$application->run();