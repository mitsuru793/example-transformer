<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use ParagonIE\EasyDB\EasyStatement;
use Php\Domain\Post\Post;
use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Tables\PostTable;

final class EasyDBPostRepository implements PostRepository
{
    private ExtendedEasyDB $db;

    private PostTable $postTable;

    private UserRepository $userRepo;

    private TagRepository $tagRepo;

    public function __construct(ExtendedEasyDB $db, PostTable $postTable, UserRepository $userRepo, TagRepository $tagRepo)
    {
        $this->db = $db;
        $this->postTable = $postTable;
        $this->userRepo = $userRepo;
        $this->tagRepo = $tagRepo;
    }

    public function create(Post $post): Post
    {
        $this->db->insert($this->postTable->name(), [
            'title' => $post->title,
            'content' => $post->content,
            'year' => $post->year,
            'author_id' => $post->author->id,
            'viewable_user_ids' => json_encode($post->viewableUserIds),
        ]);
        $post->id = (int)$this->db->lastInsertId();
        return $post;
    }

    public function store(Post $post): void
    {
        $this->db->update($this->postTable->name(), [
            'title' => $post->title,
            'content' => $post->content,
            'year' => $post->year,
            'author_id' => $post->author->id,
            'viewable_user_ids' => json_encode($post->viewableUserIds),
        ], [
            'id' => $post->id,
        ]);
    }

    public function find(int $postId): ?Post
    {
        $row = $this->db->row(<<<SQL
            SELECT {$this->columnsStr()}, {$this->userRepo->columnsStr()}
            FROM posts
            INNER JOIN users ON users.id = posts.author_id
            WHERE posts.id = ?
            SQL,
            $postId,
            );
        if (!$row) {
            return null;
        }
        return $this->toPost($row);
    }

    public function updateTags(int $postId, array $tags): void
    {
        $rows = $this->db->run(<<<SQL
        SELECT tag_id
        FROM posts_tags 
        WHERE post_id = ?
        SQL, $postId);

        $existedIds = array_map(fn ($row) => (int)$row['tag_id'], $rows);

        $newTags = array_filter($tags, fn (Tag $tag) => !in_array($tag->id, $existedIds));
        foreach ($newTags as $newTag) {
            // TODO replace createMany
            $this->tagRepo->create($newTag);
        }
        $data = array_map(fn (Tag $tag) => [
            'post_id' => $postId,
            'tag_id' => $tag->id,
        ], $newTags);
        $data = array_values($data);
        if (!empty($data)) {
            $this->db->insertMany('posts_tags', $data);
        }

        $newTagIds = array_map(fn ($tag) => $tag->id, $tags);
        $removeTagIds = array_filter($existedIds, fn (int $existedId) => !in_array($existedId, $newTagIds));
        if (!empty($removeTagIds)) {
            $statement = EasyStatement::open()->in('tag_id IN (?*)', $removeTagIds);
            $this->db->delete('posts_tags', $statement);
        }
    }

    public function paging(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $rows = $this->db->run(<<<SQL
            SELECT {$this->columnsStr()}, {$this->userRepo->columnsStr()}
            FROM posts
            INNER JOIN users ON users.id = posts.author_id
            ORDER BY posts_id ASC
            LIMIT $perPage OFFSET $offset
            SQL
        );
        return array_map(function ($row) {
            return $this->toPost($row);
        }, $rows);
    }

    public function count(): int
    {
        return (int)$this->db->single("SELECT count(*) FROM {$this->postTable->name()}");
    }

    public function columnsStr(): string
    {
        return implode(', ', $this->postTable->columns());
    }

    public function toPost(array $row): Post
    {
        $author = $this->userRepo->toUser($row);
        return new Post((int)$row['posts_id'], $author, $row['posts_title'], $row['posts_content'], $row['posts_year'], json_decode($row['posts_viewable_user_ids']));
    }
}
