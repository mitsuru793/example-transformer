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

        $got = $fixture->get('user2');
        $this->assertSame('name2', $got);

        $got = $fixture->get('user{2..4}');
        $this->assertCount(3, $got);
        $this->assertSame('name2', $got['user2']);
        $this->assertSame('name3', $got['user3']);
        $this->assertSame('name4', $got['user4']);
    }

    public function testGetPlucksValue()
    {
        $fixture = new AliceFixture([
            'user1' => ['id' => 1, 'name' => 'name1']
        ]);
        $got = $fixture->get('user1.id');
        $this->assertSame(1, $got);

        $fixture = new AliceFixture([
            'user1' => (object)['id' => 1, 'name' => 'name1']
        ]);
        $got = $fixture->get('user1.id');
        $this->assertSame(1, $got);

        $fixture = new AliceFixture([
            'user1' => ['id' => 1, 'name' => 'name1'],
            'user2' => ['id' => 2, 'name' => 'name2'],
        ]);
        $got = $fixture->get('user{1..2}.id');
        $this->assertSame([1, 2], $got);

        $fixture = new AliceFixture([
            'user1' => (object)['id' => 1, 'name' => 'name1'],
            'user2' => (object)['id' => 2, 'name' => 'name2'],
        ]);
        $got = $fixture->get('user{1..2}.id');
        $this->assertSame([1, 2], $got);
    }

    public function testGetReIndex()
    {
        $fixture = new AliceFixture([
            'user1' => 'name1',
            'user2' => 'name2',
        ]);

        $got = $fixture->get();
        $this->assertSame(['user1', 'user2'], array_keys($got));

        $got = $fixture->get('user{1..2}');
        $this->assertSame(['user1', 'user2'], array_keys($got));

        $got = $fixture->get(null, true);
        $this->assertSame([0, 1], array_keys($got));

        $got = $fixture->get('user{1..2}', true);
        $this->assertSame([0, 1], array_keys($got));
    }
}
