<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users\UserId;

use FunctionalTest\Http\Api\Users\TestCase;
use Php\Application\ActionError;
use Php\Library\Fixture\AliceFixture;

final class GetTest extends TestCase
{
    public function testGetUserById()
    {
        $users = $this->f()->users('1..2');
        $this->userRepo->createMany($users);

        $res = $this->http('GET', '/users/1');
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(200, $res->getStatusCode());
        $this->assertArrayNotHasKey('password', $body);
        $this->assertEqualsUser($users[0], $body);
    }

    public function testNotFound()
    {
        $res = $this->http('GET', '/users/999');
        $body = json_decode((string)$res->getBody(), true);
        $error = $body['error'];

        $this->assertSame(404, $res->getStatusCode());
        $this->assertSame(ActionError::NOT_FOUND, $error['type']);
        $this->assertNotEmpty($error['description']);
    }
}