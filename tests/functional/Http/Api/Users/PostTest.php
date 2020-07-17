<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Application\ActionError;
use Php\Library\Fixture\AliceFixture;

final class PostTest extends TestCase
{
    public function testCreateUser()
    {
        $f = new AliceFixture($this->fixturesRow());

        $res = $this->http('POST', '/users', $f->get('user1'));
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(200, $res->getStatusCode());
        $expected = $f->get('user1');
        $expected['id'] = $this->db->row("SELECT id FROM {$this->userTable}")['id'];
        $this->assertArrayNotHasKey('password', $body);
        $this->assertEqualsUser($expected, $body);
    }

    /**
     * @dataProvider validateInputProvider
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

    public function validateInputProvider()
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

        $repeat = fn ($count) => $input->merge(['password' => str_repeat('s', $count)]);
        yield 'Password must be required' => [false, $input->merge([])->forget('password')];
        yield 'Password must not empty' => [false, $repeat(0)];

        yield 'Length of password must be grater than 2' => [false, $repeat(7)];
        yield 'Length of password is grater than 2' => [true, $repeat(8)];
        yield 'Length of password is less than 31' => [true, $repeat(16)];
        yield 'Length of password must be less than 31' => [false, $repeat(17)];
    }
}
