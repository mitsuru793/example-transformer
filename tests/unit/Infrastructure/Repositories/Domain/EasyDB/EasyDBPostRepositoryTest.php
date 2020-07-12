<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Post\Post;
use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\Tag;
use Php\Domain\User\User;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Tables\PostTable;
use Php\Infrastructure\Tables\TagTable;
use Php\Infrastructure\Tables\UserTable;

final class EasyDBPostRepositoryTest extends TestCase
{
    private PostTable $postTable;

    private PostRepository $postRepo;

    private UserTable $userTable;

    private UserRepository $userRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable();
        $this->userTable = new UserTable();
        $this->userRepo = new EasyDBUserRepository($this->db, $this->userTable);
        $tagRepo = new EasyDBTagRepository($this->db, new TagTable());
        $this->postRepo = new EasyDBPostRepository($this->db, $this->postTable, $this->userRepo, $tagRepo);
    }

    public function testCreate()
    {
        $got = $this->postRepo->find(1);
        $this->assertNull($got);

        $author = new User(null, 'name1');
        $author = $this->userRepo->create($author);
        $post = new Post(null, $author, 'title1', 'content1', 2010, [2, 3]);
        $this->assertNull($post->id);

        $created = $this->postRepo->create($post);
        $this->assertNotNull($created->id);

        $got = $this->postRepo->find($created->id);
        $this->assertSame('title1', $got->title);
        $this->assertSame('content1', $got->content);
        $this->assertSame(2010, $got->year);
        $this->assertSame([2, 3], $got->viewableUserIds);
        $this->assertSame('name1', $got->author->name);
    }

    public function testStore()
    {
        $author = new User(null, 'name1');
        $author = $this->userRepo->create($author);
        $post = new Post(null, $author, 'title1', 'content1', 2010, [2, 3]);
        $this->assertNull($post->id);

        $author = new User(null, 'name2');
        $author = $this->userRepo->create($author);
        $created = $this->postRepo->create($post);
        $created->author = $author;
        $created->title = 'title2';
        $created->content = 'content2';
        $created->year = 2011;
        $created->viewableUserIds = [4, 5];
        $this->postRepo->store($created);

        $got = $this->postRepo->find($created->id);
        $this->assertSame('title2', $got->title);
        $this->assertSame('content2', $got->content);
        $this->assertSame(2011, $got->year);
        $this->assertSame([4, 5], $got->viewableUserIds);
        $this->assertSame('name2', $post->author->name);
    }

    public function testFind()
    {
        $this->db->insert($this->userTable->name(), ['id' => 1, 'name' => 'mike']);
        $rows = array_map(fn ($i) => ['id' => $i, 'title' => "t$i", 'content' => "c$i", 'year' => 2010 + $i, 'author_id' => 1, 'viewable_user_ids' => '[]'], range(1, 3));
        $this->db->insertMany($this->postTable->name(), $rows);

        $got = $this->postRepo->find(2);
        $this->assertInstanceOf(Post::class, $got);
        $this->assertSame(2, $got->id);
        $this->assertSame('t2', $got->title);
        $this->assertSame('c2', $got->content);
        $this->assertSame(2012, $got->year);
        $this->assertSame([], $got->viewableUserIds);
        $this->assertSame(1, $got->author->id);
        $this->assertSame('mike', $got->author->name);

        $got = $this->postRepo->find(999);
        $this->assertNull($got);
    }

    public function testUpdateTags()
    {
        $this->db->insert($this->userTable->name(), ['id' => 1, 'name' => 'mike']);
        $rows = array_map(fn ($i) => ['id' => $i, 'title' => "t$i", 'content' => "c$i", 'year' => 2010 + $i, 'author_id' => 1, 'viewable_user_ids' => '[]'], range(1, 3));
        $this->db->insertMany($this->postTable->name(), $rows);

        $this->postRepo->updateTags(1, [new Tag(null, 'tag1'), new Tag(null, 'tag2')]);
    }
}
