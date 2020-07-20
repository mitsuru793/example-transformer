<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Php\Infrastructure\Tables\UserTable;

abstract class TestCase extends \FunctionalTest\Http\Api\TestCase
{
    protected UserTable $userTable;

    protected UserRepository $userRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTable = new UserTable();
        $this->userRepo = new EasyDBUserRepository($this->db, $this->userTable);
    }

    protected function assertEqualsUser(array $expected, array $actual)
    {
        $this->assertSame($expected['id'], $actual['id']);
        $this->assertSame($expected['name'], $actual['name']);
    }
}