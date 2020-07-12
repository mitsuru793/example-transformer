<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\User\User;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Tables\UserTable;

class EasyDBUserRepositoryTest extends TestCase
{
    private UserTable $userTable;

    private UserRepository $userRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTable = new UserTable();
        $this->userRepo = new EasyDBUserRepository($this->db, $this->userTable);
    }

    public function testFind()
    {
        $this->db->insertMany($this->userTable->name(), [
            ['id' => 1, 'name' => 'n1'],
            ['id' => 2, 'name' => 'n2'],
            ['id' => 3, 'name' => 'n3'],
        ]);

        $user = $this->userRepo->find(2);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(2, $user->id);
        $this->assertSame('n2', $user->name);

        $user = $this->userRepo->find(999);
        $this->assertNull($user);
    }

    public function testPaging()
    {
        $rows = array_map(
            fn ($i) => ['id' => $i, 'name' => "n{$i}"],
            range(1, 10)
        );
        $this->db->insertMany($this->userTable->name(), $rows);

        $users = $this->userRepo->paging(3, 3);
        $this->assertCount(3, $users);
        $this->assertSame(9, end($users)->id);
        $this->assertSame('n9', end($users)->name);

        $users = $this->userRepo->paging(4, 3);
        $this->assertCount(1, $users);
        $this->assertSame(10, end($users)->id);
        $this->assertSame('n10', end($users)->name);

        $users = $this->userRepo->paging(100, 3);
        $this->assertCount(0, $users);
    }

    public function testCreate()
    {
        $got = $this->userRepo->find(1);
        $this->assertNull($got);

        $user = new User(null, 'n1');
        $this->assertNull($user->id);

        $created = $this->userRepo->create($user);
        $this->assertNotNull($created->id);

        $got = $this->userRepo->find($created->id);
        $this->assertSame('n1', $got->name);
    }

    public function testCreateMany()
    {
        $f = $this->fixtures();

        $got = $this->userRepo->find($f['user1']->id);
        $this->assertNull($got);
        $got = $this->userRepo->find($f['user2']->id);
        $this->assertNull($got);

        $this->userRepo->createMany([$f['user1'], $f['user2']]);

        $got = $this->userRepo->paging(1, 3);
        $this->assertCount(2, $got);
        $this->assertSame($f['user1']->name, $got[0]->name);
        $this->assertSame($f['user2']->name, $got[1]->name);
    }

    public function testDelete()
    {
        $this->db->insert($this->userTable->name(), [
            'id' => 1, 'name' => 'n1',
        ]);

        $this->userRepo->delete(1);
        $got = $this->userRepo->find(1);
        $this->assertNull($got);
    }
}
