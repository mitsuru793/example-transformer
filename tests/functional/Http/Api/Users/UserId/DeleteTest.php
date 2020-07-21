<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api\Users\UserId;

use FunctionalTest\Http\Api\Users\TestCase;
use Php\Domain\Post\PostRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBPostRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBTagRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Php\Infrastructure\Tables\PostTable;
use Php\Infrastructure\Tables\TagTable;
use Php\Library\Fixture\AliceFixture;

final class DeleteTest extends TestCase
{
    private PostTable $postTable;

    private PostRepository $postRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable();
        $this->userRepo = new EasyDBUserRepository($this->db, $this->userTable);
        $tagRepo = new EasyDBTagRepository($this->db, new TagTable());
        $this->postRepo = new EasyDBPostRepository($this->db, $this->postTable, $this->userTable, $this->userRepo, $tagRepo);
    }

    public function testDelete()
    {
        $posts = $this->f()->posts('1..2');
        $authors = array_map(fn ($p) => $p->author, $posts);
        $this->userRepo->createMany($authors);
        $this->postRepo->createMany($posts);

        $authorId = $authors[0]->id;
        $res = $this->http('DELETE', "/users/$authorId");
        $body = json_decode((string)$res->getBody(), true);

        $this->assertSame(204, $res->getStatusCode());
        $this->assertSame([], $body);

        $row = $this->db->find($this->userTable, $authorId);
        $this->assertNull($row);

        $row = $this->db->find($this->postTable, 1);
        $this->assertNull($row);
    }
}