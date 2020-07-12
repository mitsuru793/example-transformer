<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use ParagonIE\EasyDB\EasyStatement;
use Php\Domain\Post\Post;
use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;
use Php\Infrastructure\Tables\TagTable;

final class EasyDBTagRepository implements TagRepository
{
    private ExtendedEasyDB $db;

    private TagTable $tagTable;

    public function __construct(ExtendedEasyDB $db, TagTable $tagTable)
    {
        $this->db = $db;
        $this->tagTable = $tagTable;
    }

    public function create(Tag $tag): Tag
    {
        $this->db->insert('tags', $this->toRow($tag));
        $tag->id = (int)$this->db->lastInsertId();
        return $tag;
    }

    public function createMany(array $tags): void
    {
        $this->db->insertMany(
            $this->tagTable->name(),
            $this->toRows($tags),
        );
    }

    public function find(int $tagId): ?Tag
    {
        return $this->db->find($this->tagTable, $tagId, [$this, 'toTag']);
    }

    public function findOrCreateMany(array $names): array
    {
        $inNames = EasyStatement::open()->in('tags.name IN (?*)', $names);
        $existedRows = $this->db->run(<<<SQL
        SELECT {$this->tagTable->columnsStr()}
        FROM tags
        WHERE $inNames
        SQL, ...$inNames->values());

        $existedNames = array_map(fn ($row) => $row['tags_name'], $existedRows);
        $newNames = array_filter($names, fn ($name) => !in_array($name, $existedNames));

        $newTagMap = array_map(fn ($n) => ['name' => $n], $newNames);
        $newTagMap = array_values($newTagMap);
        if (empty($newTagMap)) {
            return $this->toTags($existedRows);
        }
        $this->db->insertMany('tags', $newTagMap);

        $rows = $this->db->run(<<<SQL
        SELECT {$this->tagTable->columnsStr()}
        FROM tags
        WHERE $inNames
        SQL, ...$inNames->values());
        return $this->toTags($rows);
    }

    public function findRandoms(int $count): array
    {
        $rows = $this->db->run(<<<SQL
            SELECT {$this->tagTable->columnsStr()}
            FROM tags
            ORDER BY RAND()
            LIMIT $count
            SQL
        );
        return $this->toTags($rows);
    }

    public function findByPostId(int $postId): array
    {
        $rows = $this->db->run(<<<SQL
            SELECT {$this->tagTable->columnsStr()}
            FROM tags
            INNER JOIN posts_tags ON posts_tags.tag_id = tags.id
            WHERE posts_tags.post_id = $postId
            SQL
        );
        return $this->toTags($rows);
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    public function findByPosts(array $posts): array
    {
        if (empty($posts)) {
            return [];
        }

        $postIds = array_map(fn (Post $p) => $p->id, $posts);
        $postIdsStr = implode(', ', $postIds);
        $rows = $this->db->run(<<<SQL
            SELECT {$this->tagTable->columnsStr()}, posts_tags.post_id as post_id
            FROM tags
            INNER JOIN posts_tags ON posts_tags.tag_id = tags.id
            WHERE post_id IN ($postIdsStr)
            SQL
        );

        $tags = [];
        foreach ($rows as $row) {
            $postId = $row['post_id'];
            $tags[$postId] ??= [];
            array_push($tags[$postId], $this->toTag($row));
        }
        return array_map(fn (Post $p) => $p->addTags($tags[$p->id] ?? []), $posts);
    }

    public function findByPost(Post $post): Post
    {
        return $this->findByPosts([$post])[0];
    }

    /**
     * @todo test
     */
    public function paging(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $rows = $this->db->run(<<<SQL
            SELECT {$this->tagTable->columnsStr()}
            FROM {$this->tagTable->name()}
            ORDER BY tags_id ASC
            LIMIT $perPage OFFSET $offset
            SQL
        );
        return $this->toTags($rows);
    }

    public function toTag(array $row): Tag
    {
        return new Tag((int)$row['tags_id'], $row['tags_name']);
    }

    /**
     * @return Tag[]
     */
    public function toTags(array $rows): array
    {
        return array_map([$this, 'toTag'], $rows);
    }

    public function toRow(Tag $tag): array
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
        ];
    }

    /**
     * @param Tag[] $tags
     */
    public function toRows(array $tags): array
    {
        return array_map([$this, 'toRow'], $tags);
    }
}
