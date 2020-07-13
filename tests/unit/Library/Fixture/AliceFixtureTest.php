<?php
declare(strict_types=1);

namespace Php\Library\Fixture;

use PHPUnit\Framework\TestCase;

class AliceFixtureTest extends TestCase
{
    public function testGet()
    {
        $data = [
            'user1' => 'name1',
            'user2' => 'name2',
            'user3' => 'name3',
            'user4' => 'name4',
            'user5' => 'name5',
        ];
        $fixture = new AliceFixture($data);

        $got = $fixture->get();
        $this->assertCount(5, $got);
        $this->assertSame('name1', $got['user1']);
        $this->assertSame('name2', $got['user2']);
        $this->assertSame('name5', $got['user5']);

        $got = $fixture->get('user{2..4}');
        $this->assertCount(3, $got);
        $this->assertSame('name2', $got['user2']);
        $this->assertSame('name3', $got['user3']);
        $this->assertSame('name4', $got['user4']);
    }
}
