<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Tables\PostTable;
use Php\Infrastructure\Tables\TagTable;
use Php\Infrastructure\Tables\UserTable;
use Php\Library\Fixture\AliceFixture;

class EasyDBTagRepositoryTest extends TestCase
{
    private UserTable $userTable;

    private UserRepository $userRepo;

    private PostTable $postTable;

    private PostRepository $postRepo;

    private TagTable $tagTable;

    private TagRepository $tagRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTable = new UserTable();
        $this->userRepo = new EasyDBUserRepository($this->db, $this->userTable);
        $this->tagTable = new TagTable();
        $this->tagRepo = new EasyDBTagRepository($this->db, $this->tagTable);
        $this->postTable = new PostTable();
        $this->postRepo = new EasyDBPostRepository($this->db, $this->postTable, $this->userTable, $this->userRepo, $this->tagRepo);
    }

    public function testCreate()
    {
        $got = $this->tagRepo->find(1);
        $this->assertNull($got);

        $tag = new Tag(null, 'tag1');
        $this->assertNull($tag->id);

        $created = $this->tagRepo->create($tag);
        $this->assertNotNull($created->id);

        $got = $this->tagRepo->find($created->id);
        $this->assertSame('tag1', $got->name);
    }

    public function testCreateMany()
    {
        $this->tagRepo->createMany([]);
        $got = $this->tagRepo->paging(1, 3);
        $this->assertCount(0, $got);

        $f = new AliceFixture($this->fixtures());

        $got = $this->tagRepo->find($f->get('tag1.id'));
        $this->assertNull($got);
        $got = $this->tagRepo->find($f->get('tag2.id'));
        $this->assertNull($got);

        $this->tagRepo->createMany($f->get('tag{1..2}', true));

        $got = $this->tagRepo->paging(1, 3);
        $this->assertCount(2, $got);
        $this->assertSame($f->get('tag1.name'), $got[0]->name);
        $this->assertSame($f->get('tag2.name'), $got[1]->name);
    }

    public function testFind()
    {
        $f = new AliceFixture($this->fixtures());
        $this->tagRepo->createMany($f->get('tag{1..2}', true));
        $got = $this->tagRepo->find($f->get('tag1.id'));
        $this->assertInstanceOf(Tag::class, $got);
        $this->assertSame($f->get('tag1.id'), $got->id);
        $this->assertSame($f->get('tag1.name'), $got->name);
    }

    public function testFindOrCreateMany()
    {
        $this->db->insert($this->tagTable->name(), [
            'id' => 1, 'name' => 'tag1',
        ]);

        $got = $this->tagRepo->findOrCreateMany(['tag1', 'tag2']);
        $this->assertCount(2, $got);
        $this->assertSame(1, $got[0]->id);
        $this->assertSame('tag1', $got[0]->name);
        $this->assertNotEmpty($got[1]->id);
        $this->assertSame('tag2', $got[1]->name);
    }

    public function testFindRandoms()
    {
        $f = new AliceFixture($this->fixturesRow());
        $this->db->insertMany($this->tagTable->name(), $f->get('tag{1..3}', true));

        $got = $this->tagRepo->findRandoms(2);
        $this->assertCount(2, $got);
        $expected = $f->get('tag{1..3}.name');
        $this->assertContains($got[0]->name, $expected);
        $expected = array_diff($expected, [$got[0]->name]);
        $this->assertContains($got[1]->name, $expected);

        $got = $this->tagRepo->findRandoms(4);
        $this->assertCount(3, $got);
    }

    public function testFindByPostId()
    {
        $f = new AliceFixture($this->fixtures());
        $this->tagRepo->createMany($f->get('tag{1..3}', true));
        $this->userRepo->createMany($f->get('post{1..2}.author', true));
        $this->postRepo->createMany($f->get('post{1..2}', true));
        $this->db->insertMany('posts_tags', [
            ['post_id' => $f->get('post1.id'), 'tag_id' => $f->get('tag1.id')],
            ['post_id' => $f->get('post1.id'), 'tag_id' => $f->get('tag2.id')],
            ['post_id' => $f->get('post2.id'), 'tag_id' => $f->get('tag3.id')],
        ]);

        $got = $this->tagRepo->findByPostId($f->get('post1.id'));
        $this->assertCount(2, $got);
        $this->assertSame($f->get('tag1.id'), $got[0]->id);
        $this->assertSame($f->get('tag2.id'), $got[1]->id);

        $got = $this->tagRepo->findByPostId(999);
        $this->assertSame([], $got);
    }
}
