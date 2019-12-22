<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;

final class EasyDBTagRepository implements TagRepository
{
    private ExtendedEasyDB $db;

    public function __construct(ExtendedEasyDB $db)
    {
        $this->db = $db;
    }

    public function create(Tag $tag): Tag
    {
        $this->db->insert('tags', [
            'name' => $tag->name,
        ]);
        $tag->id = (int)$this->db->lastInsertId();
        return $tag;
    }

    public function findRandoms(int $count): array
    {
        $rows = $this->db->run(<<<SQL
            SELECT {$this->columnsStr()}
            FROM tags
            ORDER BY RAND()
            LIMIT $count
            SQL
        );
        return array_map(fn ($row) =>$this->toTag($row), $rows);
    }

    public function findByPostId(int $postId): array
    {
        $rows = $this->db->run(<<<SQL
            SELECT {$this->columnsStr()}
            FROM tags
            INNER JOIN posts_tags ON posts_tags.tag_id = tags.id
            WHERE posts_tags.post_id = $postId
            SQL
        );
        return array_map(fn ($row) => $this->toTag($row), $rows);
    }

    public function columns(): array
    {
        $columns = ['id', 'name'];
        return array_map(fn($v) => "tags.$v AS tags_$v", $columns);
    }

    public function columnsStr(): string
    {
        return implode(',', $this->columns());
    }

    public function toTag(array $row): Tag
    {
        return new Tag((int)$row['tags_id'], $row['tags_name']);
    }
}
