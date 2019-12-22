<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Post\Post;
use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\Tag;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\User;
use Php\Domain\User\UserRepository;

final class EasyDBPostRepository implements PostRepository
{
    private ExtendedEasyDB $db;

    private UserRepository $userRepo;

    private TagRepository $tagRepo;

    public function __construct(ExtendedEasyDB $db, UserRepository $userRepo)
    {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    public function create(Post $post): Post
    {
        $this->db->insert('posts', [
            'title' => $post->title,
            'content' => $post->content,
            'year' => $post->year,
            'author_id' => $post->author->id,
            'viewable_user_ids' => json_encode($post->viewableUserIds),
        ]);
        $post->id = (int)$this->db->lastInsertId();
        return $post;
    }

    public function find(int $postId): Post
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

    public function addTags(int $postId, array $tags): void
    {
        $data = array_map(fn(Tag $tag) => [
            'post_id' => $postId,
            'tag_id' => $tag->id,
        ], $tags);

        if (empty($data)) {
            return;
        }
        $this->db->insertMany('posts_tags', $data);
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
        return (int)$this->db->single('SELECT count(*) FROM posts');
    }

    public function columns(): array
    {
        $columns = ['id', 'author_id', 'title', 'content', 'year', 'viewable_user_ids'];
        return array_map(fn($v) => "posts.$v AS posts_$v", $columns);
    }

    public function columnsStr(): string
    {
        return implode(', ', $this->columns());
    }

    public function toPost(array $row): Post
    {
        $author = $this->userRepo->toUser($row);
        return new Post((int)$row['posts_id'], $author, $row['posts_title'], $row['posts_content'], $row['posts_year'], json_decode($row['posts_viewable_user_ids']));
    }
}
