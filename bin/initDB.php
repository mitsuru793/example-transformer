<?php
declare(strict_types=1);

use Php\Infrastructure\Database;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new League\Container\Container;
$add = require_once __DIR__ . '/../config/dependencies.php';
$add($container);

$container->get(Database::class)->runSqlFile(__DIR__ . '/../config/create_tables.sql');
