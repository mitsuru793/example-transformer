<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Infrastructure\Tables\UserTable;

abstract class TestCase extends \FunctionalTest\Http\Api\TestCase
{
    protected UserTable $userTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTable = new UserTable();
    }

    protected function assertEqualsUser(array $expected, array $actual)
    {
        $this->assertSame($expected['id'], $actual['id']);
        $this->assertSame($expected['name'], $actual['name']);
    }
}