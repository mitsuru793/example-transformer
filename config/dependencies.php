<?php
declare(strict_types=1);

return function (\League\Container\Container $c) {
    $c->add(\Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB::class, function () {
        $pdo = new \PDO(
            'mysql:host=localhost;dbname=development_db',
            'root',
            'pass',
        );
        return new \Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB($pdo, 'mysql');
    }, true);

    $c->add(\League\Plates\Engine::class, function () {
        return League\Plates\Engine::create(__DIR__ . '/../src/Template');
    }, true);
};
