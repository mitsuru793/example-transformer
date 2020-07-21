<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users\UserId;

use FunctionalTest\Http\Api\Users\TestCase;
use Php\Application\ActionError;
use Php\Library\Fixture\AliceFixture;

final class UpdateTest extends TestCase
{
    public function testUpdate()
    {
        $f = new AliceFixture($this->fixtures());
        $users = $f->get('user{1..2}', true);
        $this->userRepo->createMany($users);

        $res = $this->http('PUT', "/users/{$users[0]->id}", [
            'name' => 'after name',
        ]);
        $body = json_decode((string)$res->getBody(), true);

        $users[0]->name = 'after name';
        $this->assertSame(200, $res->getStatusCode());
        $this->assertEqualsUser($users[0], $body);

        $got = $this->userRepo->find($users[0]->id);
        $this->assertEqualsStrictUser($users[0], (array)$got);

        $got = $this->userRepo->find($users[1]->id);
        $this->assertEqualsStrictUser($users[1], (array)$got);
    }

    public function testNotUpdatePassword()
    {
        $f = new AliceFixture($this->fixtures());
        $user = $f->get('user1');
        $this->userRepo->create($user);

        $this->http('PUT', "/users/$user->id", [
            'password' => 'after pass',
        ]);

        $got = $this->userRepo->find($user->id);
        $this->assertNotSame('after pass', $got->password);
        $this->assertEqualsStrictUser($user, (array)$got);
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testValidateInput(bool $success, $input)
    {
        $f = new AliceFixture($this->fixtures());
        $user = $f->get('user1');
        $this->userRepo->create($user);

        $res = $this->http('PUT', "/users/$user->id", $input);
        $body = json_decode((string)$res->getBody(), true);

        if ($success) {
            $this->assertSame(200, $res->getStatusCode());
        } else {
            $error = $body['error'];
            $this->assertSame(422, $res->getStatusCode());
            $this->assertSame(ActionError::UNPROCESSABLE_ENTITY, $error['type']);
            $this->assertNotEmpty($error['description']);
        }
    }
}