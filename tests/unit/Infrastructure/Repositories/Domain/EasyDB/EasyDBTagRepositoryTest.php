<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;
use Php\Infrastructure\Tables\TagTable;

class EasyDBTagRepositoryTest extends TestCase
{
    private TagTable $tagTable;

    private TagRepository $tagRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagTable = new TagTable();
        $this->tagRepo = new EasyDBTagRepository($this->db);
    }

    public function testCreate()
    {
        // TODO replace find
        $got = $this->db->find($this->tagTable, 1);
        $this->assertNull($got);

        $tag = new Tag(null, 'tag1');
        $this->assertNull($tag->id);

        $created = $this->tagRepo->create($tag);
        $this->assertNotNull($created->id);

        $got = $this->db->find($this->tagTable, $created->id);
        $this->assertSame('tag1', $got['tags_name']);
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
}
