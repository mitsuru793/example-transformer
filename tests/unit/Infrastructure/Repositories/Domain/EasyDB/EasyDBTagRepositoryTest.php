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
        $f = $this->fixtures();

        $got = $this->tagRepo->find($f['tag1']->id);
        $this->assertNull($got);
        $got = $this->tagRepo->find($f['tag2']->id);
        $this->assertNull($got);

        $this->tagRepo->createMany([$f['tag1'], $f['tag2']]);

        $got = $this->tagRepo->paging(1, 3);
        $this->assertCount(2, $got);
        $this->assertSame($f['tag1']->name, $got[0]->name);
        $this->assertSame($f['tag2']->name, $got[1]->name);
    }

    public function testFind()
    {
        $f = $this->fixtures();
        $this->tagRepo->createMany([
            $f['tag1'], $f['tag2'],
        ]);
        $got = $this->tagRepo->find($f['tag1']->id);
        $this->assertInstanceOf(Tag::class, $got);
        $this->assertSame($f['tag1']->id, $got->id);
        $this->assertSame($f['tag1']->name, $got->name);
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
        $this->db->insertMany($this->tagTable->name(), [
            ['id' => 1, 'name' => 'tag1'],
            ['id' => 2, 'name' => 'tag2'],
            ['id' => 3, 'name' => 'tag3'],
        ]);

        $got = $this->tagRepo->findRandoms(2);
        $this->assertCount(2, $got);
        $expected = ['tag1', 'tag2', 'tag3'];
        $this->assertContains($got[0]->name, $expected);
        $expected = array_diff($expected, [$got[0]->name]);
        $this->assertContains($got[1]->name, $expected);

        $got = $this->tagRepo->findRandoms(4);
        $this->assertCount(3, $got);
    }

    public function testFindByPostId()
    {
        $f = $this->fixtures();
        $this->tagRepo->createMany([$f['tag1'], $f['tag2'], $f['tag3']]);
        $this->userRepo->createMany([$f['post1']->author, $f['post2']->author]);
        $this->postRepo->createMany([$f['post1'], $f['post2']]);
        $this->db->insertMany('posts_tags', [
            ['post_id' => $f['post1']->id, 'tag_id' => $f['tag1']->id],
            ['post_id' => $f['post1']->id, 'tag_id' => $f['tag2']->id],
            ['post_id' => $f['post2']->id, 'tag_id' => $f['tag3']->id],
        ]);

        $got = $this->tagRepo->findByPostId($f['post1']->id);
        $this->assertCount(2, $got);
        $this->assertSame($f['tag1']->id, $got[0]->id);
        $this->assertSame($f['tag2']->id, $got[1]->id);

        $got = $this->tagRepo->findByPostId(999);
        $this->assertSame([], $got);
    }
}
