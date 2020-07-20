<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users;

use Php\Library\Fixture\AliceFixture;

final class DeleteTest extends TestCase
{
    public function testDelete()
    {
        $f = new AliceFixture($this->fixturesRow());
        $this->db->insertMany($this->userTable->name(), $f->get('user{1..2}', true));

        $res = $this->http('DELETE', '/users/1');
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(204, $res->getStatusCode());
        $this->assertSame([], $body);
    }
}