<?php
declare(strict_types=1);

use Faker\Factory;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('TEST_DB_HOST'),
    'database'  => getenv('TEST_DB_DATABASE'),
    'username'  => getenv('TEST_DB_USER'),
    'password'  => getenv('TEST_DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$db = new ExtendedEasyDB($capsule->getConnection()->getPdo());
$db->runSqlFile(__DIR__ . '/../config/create_tables.sql');
