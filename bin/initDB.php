<?php
declare(strict_types=1);

use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new League\Container\Container;
$add = require_once __DIR__ . '/../config/dependencies.php';
$add($container);

$container->get(ExtendedEasyDB::class)->runSqlFile(__DIR__ . '/../config/create_tables.sql');
