<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Library\Fixture\AliceFixture;

final class GetTest extends TestCase
{
    public function testGetUsers()
    {
        $f = new AliceFixture($this->fixtures());
        $this->userRepo->createMany($f->get('user{1..2}', true));

        $res = $this->http('GET', '/users');
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(200, $res->getStatusCode());
        $this->assertCount(2, $body);
        $this->assertArrayNotHasKey('password', $body[0]);
        $this->assertEqualsUser($f->get('user1'), $body[0]);
        $this->assertEqualsUser($f->get('user2'), $body[1]);
    }

    public function testGetEmpty()
    {
        $res = $this->http('GET', '/users');
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(200, $res->getStatusCode());
        $this->assertCount(0, $body);
    }
}