<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories;

abstract class TestCase extends \Php\TestCase
{
    protected \PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new \PDO(
            sprintf('mysql:host=%s;dbname=%s', getenv('TEST_DB_HOST'), getenv('TEST_DB_DATABASE')),
            getenv('TEST_DB_USER'),
            getenv('TEST_DB_PASS'),
        );
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollback();
        }
    }
}
