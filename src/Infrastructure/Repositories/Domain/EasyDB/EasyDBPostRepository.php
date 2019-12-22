<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Post\Post;
use Php\Domain\Post\PostRepository;
use Php\Domain\User\UserRepository;

final class EasyDBPostRepository implements PostRepository
{
    private ExtendedEasyDB $db;

    private UserRepository $userRepo;

    public function __construct(ExtendedEasyDB $db, UserRepository $userRepo)
    {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    public function create(Post $post): Post
    {
        $this->db->insert('posts', [
            'title' => $post->title,
            'year' => $post->year,
            'author_id' => $post->author->id,
            'viewable_user_ids' => json_encode($post->viewableUserIds),
        ]);
        $post->id = (int)$this->db->lastInsertId();
        return $post;
    }

    public function count(): int
    {
        return (int)$this->db->single('SELECT count(*) FROM posts');
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

    public function columns(): array
    {
        $columns = ['id', 'title', 'year', 'author_id', 'viewable_user_ids'];
        return array_map(fn($v) => "posts.$v AS posts_$v", $columns);
    }

    public function columnsStr(): string
    {
        return implode(',', $this->columns());
    }

    public function toPost(array $row): Post
    {
        $author = $this->userRepo->toUser($row);
        return new Post((int)$row['posts_id'], $row['posts_title'], $row['posts_year'], $author, json_decode($row['posts_viewable_user_ids']));
    }
}
