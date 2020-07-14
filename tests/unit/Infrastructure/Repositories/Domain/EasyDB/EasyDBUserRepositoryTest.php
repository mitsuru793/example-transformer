<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\User\User;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Tables\UserTable;
use Php\Library\Fixture\AliceFixture;

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
        $f = $this->fixturesRow();
        $this->db->insertMany($this->userTable->name(), [
            $f['user1'], $f['user2'],
        ]);

        $got = $this->userRepo->find($f['user1']['id']);
        $this->assertInstanceOf(User::class, $got);
        $this->assertEqualsUser($f['user1'], $got);

        $got = $this->userRepo->find(999);
        $this->assertNull($got);
    }

    public function testFindByNameAndPassword()
    {
        $f = new AliceFixture($this->fixturesRow());
        $this->db->insertMany($this->userTable->name(), $f->get('user{1..2}', true));

        $f = new AliceFixture($this->fixtures());
        /** @var User $user1 */
        $user1 = $f->get('user1');

        $got = $this->userRepo->findByNameAndPassword($user1->name, $user1->password);
        $this->assertEqualsUser($user1, $got);

        $got = $this->userRepo->findByNameAndPassword($user1->name, 'invalid');
        $this->assertNull($got);
        $got = $this->userRepo->findByNameAndPassword('invalid', $user1->password);
        $this->assertNull($got);
    }

    public function testPaging()
    {
        $f = $this->fixturesRow();
        $this->db->insertMany(
            $this->userTable->name(),
            array_map(fn ($i) => $f["user{$i}"], range(1, 10)),
        );

        $users = $this->userRepo->paging(3, 3);
        $this->assertCount(3, $users);
        $this->assertSame($f['user9']['id'], end($users)->id);
        $this->assertSame($f['user9']['name'], end($users)->name);

        $users = $this->userRepo->paging(4, 3);
        $this->assertCount(1, $users);
        $this->assertSame($f['user10']['id'], end($users)->id);
        $this->assertSame($f['user10']['name'], end($users)->name);

        $users = $this->userRepo->paging(100, 3);
        $this->assertCount(0, $users);
    }

    public function testCreate()
    {
        $got = $this->userRepo->find(1);
        $this->assertNull($got);

        $newUser = $this->fixtures()['user1'];
        $newUser->id = null;

        $created = $this->userRepo->create($newUser);
        $this->assertNotNull($created->id);

        $got = $this->userRepo->find($created->id);
        $this->assertSame($newUser->name, $got->name);
    }

    public function testCreateMany()
    {
        $this->userRepo->createMany([]);
        $got = $this->userRepo->paging(1, 3);
        $this->assertCount(0, $got);

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
    /**
     * @param array|User $expected
     */
    private function assertEqualsUser($expected, User $actual): void
    {
        $value = $this->getValue($expected, 'id', 'id');
        $this->assertSame($value, $actual->id);

        $value = $this->getValue($expected, 'name', 'name');
        $this->assertSame($value, $actual->name);

        $value = $this->getValue($expected, 'password', 'password');
        $this->assertSame($value, $actual->password);
    }

    private function getValue($model, string $snakeCaseProp, string $camelCaseProp)
    {
        if (is_array($model)) {
            $t = $this->userTable->name();
            return $model[$snakeCaseProp] ?? $model["{$t}_$snakeCaseProp"];
        }
        if ($model instanceof User) {
            return $model->{$camelCaseProp};
        }
        $err = sprintf('Type of $model must be array of entity, but %s.', get_class($model));
        throw new \LogicException($err);
    }
}
