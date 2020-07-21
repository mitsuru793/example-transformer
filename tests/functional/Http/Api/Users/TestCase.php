<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Php\Infrastructure\Tables\UserTable;
use Php\Library\Fixture\AliceFixture;

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

    protected function assertEqualsStrictUser(array $expected, array $actual)
    {
        $this->assertEqualsUser($expected, $actual);

        $passA = $expected['password'] ?? null;
        $passB = $actual['password'] ?? null;
        if ($passA || $passB) {
            $this->assertSame($passA ?? null, $passB ?? null);
        }
    }

    public function invalidNameProvider()
    {
        $f = new AliceFixture(self::fixturesRow());
        $input = collect($f->get('user1'));

        $repeat = fn ($count) => $input->merge(['name' => str_repeat('s', $count)]);
        yield 'Name must be required' => [false, $input->merge([])->forget('name')];
        yield 'Name must not empty' => [false, $repeat(0)];

        yield 'Length of name must be grater than 2' => [false, $repeat(2)];
        yield 'Length of name is grater than 2' => [true, $repeat(3)];
        yield 'Length of name is less than 30' => [true, $repeat(30)];
        yield 'Length of name must be less than 30' => [false, $repeat(31)];
    }

    public function invalidPasswordProvider()
    {
        $f = new AliceFixture(self::fixturesRow());
        $input = collect($f->get('user1'));

        $repeat = fn ($count) => $input->merge(['password' => str_repeat('s', $count)]);
        yield 'Password must be required' => [false, $input->merge([])->forget('password')];
        yield 'Password must not empty' => [false, $repeat(0)];

        yield 'Length of password must be grater than 2' => [false, $repeat(7)];
        yield 'Length of password is grater than 2' => [true, $repeat(8)];
        yield 'Length of password is less than 31' => [true, $repeat(16)];
        yield 'Length of password must be less than 31' => [false, $repeat(17)];
    }
}