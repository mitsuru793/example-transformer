<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

abstract class TestCase extends \Php\Infrastructure\Repositories\TestCase
{
    protected ExtendedEasyDB $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new ExtendedEasyDB($this->pdo, 'mysql');
    }
}
