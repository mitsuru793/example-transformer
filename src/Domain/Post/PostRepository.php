<?php
declare(strict_types=1);

namespace Php\Domain\Post;

use Php\Domain\Tag\Tag;

interface PostRepository
{
    public function create(Post $post): Post;

    public function createMany(array $posts): void;

    public function store(Post $post);

    public function find(int $postId): ?Post;

    /**
     * @param Tag[] $tags
     */
    public function updateTags(int $postId, array $tags): void;

    /**
     * @return Post[]
     */
    public function paging(int $page, int $perPage): array;
}
