<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Application\ActionError;
use Php\Domain\User\User;
use Php\Library\Fixture\AliceFixture;

final class PostTest extends TestCase
{
    public function testCreateUser()
    {
        $user = $this->f()->user(1);

        $res = $this->http('POST', '/users', [
            'name' => $user->name,
            'password' => $user->password,
        ]);
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(200, $res->getStatusCode());
        $user->id = $this->db->row("SELECT id FROM {$this->userTable}")['id'];
        $this->assertArrayNotHasKey('password', $body);
        $this->assertEqualsUser($user, $body);
    }

    /**
     * @dataProvider invalidNameProvider
     * @dataProvider invalidPasswordProvider
     */
    public function testValidateInput(bool $success, $input)
    {
        $res = $this->http('POST', '/users', $input);
        $body = json_decode((string)$res->getBody(), true);

        if ($success) {
            $this->assertSame(200, $res->getStatusCode());
            $this->assertNotEmpty($body['name']);
        } else {
            $error = $body['error'];
            $this->assertSame(422, $res->getStatusCode());
            $this->assertSame(ActionError::UNPROCESSABLE_ENTITY, $error['type']);
            $this->assertNotEmpty($error['description']);
        }
    }
}
