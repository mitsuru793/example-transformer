<?php
declare(strict_types=1);

return function (\League\Container\Container $c) {
    $c->add(\Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB::class, function () {
        $pdo = new \PDO(
            sprintf('mysql:host=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_DATABASE')),
            getenv('DB_USER'),
            getenv('DB_PASS'),
        );
        return new \Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB($pdo, 'mysql');
    }, true);

    $c->add(\League\Plates\Engine::class, function () {
        return League\Plates\Engine::create(__DIR__ . '/../src/Template');
    }, true);

    $c->add(\Php\Library\UIFaces\Client::class, function () {
        $apiKey = getenv('UI_FACES_API_KEY');
        return new \Php\Library\UIFaces\Client($apiKey);
    }, true);

    // Middlewares
    $c->add(\Php\Application\Middlewares\EnableCors::class, function () {
        $allowed = \Php\Library\Util\Origin::web();
        return new \Php\Application\Middlewares\EnableCors([$allowed]);
    }, true);
};
